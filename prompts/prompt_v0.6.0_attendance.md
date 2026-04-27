# Prompt: Build v0.6.0 — Attendance Tracking

Follow all conventions in CLAUDE.md.

Create a checkpoint commit before making any changes:

```bash
git add .
git commit -m "checkpoint: before building v0.6.0 attendance tracking"
```

---

## Context

BarePitch is a minimal PHP/MySQL application for football coaches. Match management (v0.5.0) is complete. This prompt implements v0.6.0: attendance and player selection per match — the core purpose of BarePitch.

This is the most complex feature so far. It is the first many-to-many flow in the application. Every part of it must be implemented with strict ownership validation.

Stack: plain PHP 8.x, MySQL 8 via PDO, HTML, CSS, vanilla JS. No frameworks, no Composer.

All conventions from CLAUDE.md apply:
- Bootstrap chain: every public file starts with `require '../app/config/bootstrap.php'`
- Database access: always via `getPdo()`, never `global $pdo`
- All output: `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` or the `e()` helper
- All queries: prepared statements, never string concatenation
- All POST actions: validate CSRF with `validateCsrf()`, check ownership server-side
- All user-facing strings: `__()` helper — never hardcode interface text
- Access control: deny by default; ownership chain is user → team → match → match_players

---

## Migration

Create `database/migrations/005_create_match_players_table.sql`:

```sql
CREATE TABLE match_players (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    match_id INT UNSIGNED NOT NULL,
    player_id INT UNSIGNED NOT NULL,
    status ENUM('unknown', 'available', 'unavailable', 'selected') NOT NULL DEFAULT 'unknown',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_match_players_match
        FOREIGN KEY (match_id)
        REFERENCES matches(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_match_players_player
        FOREIGN KEY (player_id)
        REFERENCES players(id)
        ON DELETE RESTRICT,

    CONSTRAINT uq_match_players_match_player
        UNIQUE (match_id, player_id),

    INDEX idx_match_players_match_id (match_id),
    INDEX idx_match_players_player_id (player_id),
    INDEX idx_match_players_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Files to create or update

### app/models/AttendanceModel.php

Methods:

- `findForMatch(int $matchId, int $teamId, int $userId): array`
  - Returns all active players for the team, each with their current match status (or 'unknown' if no record exists yet)
  - Uses the exact query pattern from the database design doc:
    ```sql
    SELECT
        p.id,
        p.display_name,
        p.shirt_number,
        COALESCE(mp.status, 'unknown') AS match_status
    FROM players p
    LEFT JOIN match_players mp
        ON mp.player_id = p.id
        AND mp.match_id = :match_id
    WHERE p.team_id = :team_id
      AND p.status = 'active'
      AND p.deleted_at IS NULL
    ORDER BY p.shirt_number IS NULL, p.shirt_number ASC, p.display_name ASC
    ```
  - Before running this query, the caller must have already verified that the match belongs to the user through MatchModel — this method takes teamId as a confirmed, trusted value
  - Returns array of player rows with match_status included

- `saveStatuses(int $matchId, int $teamId, int $userId, array $statuses): bool`
  - Saves attendance status for multiple players in one operation
  - `$statuses` is an array of `[player_id => status]` pairs submitted from the form
  - For each entry:
    - Validate that player_id is a positive integer
    - Validate that status is one of: unknown, available, unavailable, selected
    - Verify that the player belongs to the team (JOIN on players.team_id = :teamId) — never trust submitted player IDs
    - Use upsert pattern:
      ```sql
      INSERT INTO match_players (match_id, player_id, status)
      VALUES (:match_id, :player_id, :status)
      ON DUPLICATE KEY UPDATE
          status = VALUES(status),
          updated_at = CURRENT_TIMESTAMP
      ```
  - Only saves valid entries — silently skips invalid player IDs
  - Returns true on success, false on failure
  - Never runs a bulk INSERT from unvalidated input — validate and insert row by row

- `getStatusSummary(int $matchId): array`
  - Returns count per status for a match: `['unknown' => n, 'available' => n, 'unavailable' => n, 'selected' => n]`
  - Used to show a summary line on the match list and detail page

All methods use `getPdo()`. No raw SQL concatenation. No `global` keyword.

---

### Update app/models/MatchModel.php

Add to the existing `findAllForTeam()` and `findUpcomingForTeam()` queries: include a `selected_count` column via subquery or LEFT JOIN that counts match_players records with status = 'selected' for each match. This allows the match list to show how many players are selected per match without a separate query.

---

### app/views/matches/detail.php (replace placeholder)

Replace the attendance placeholder section from v0.5.0 with the full attendance interface.

Purpose: show match info and allow the coach to set each player's status.

Elements:
- Match header: opponent name, date, kickoff time (if set), location (if set), home/away badge (if set)
- Link back to match list
- Edit match link
- Attendance form — one form for all players, single POST submit
- Per player row:
  - Shirt number (or dash if null)
  - Display name
  - Status control: four options — Unknown, Available, Unavailable, Selected
  - Render as a `<select>` or as a group of radio buttons
  - Current status pre-selected
- Summary line below the list: e.g. "3 selected · 2 available · 1 unavailable · 5 unknown"
- CSRF hidden field
- Save button — large, easy to tap on mobile
- Flash message display

Important UI notes:
- This screen is used on mobile, near training or matches. Keep it fast and clear.
- Selected players should be visually distinct (e.g. bold name, distinct background or border — use CSS only, not color alone)
- Buttons and select controls must have adequate touch target size (min 44px height)
- The form must work without JavaScript — progressive enhancement only

Layout: include `layouts/header.php` and `layouts/footer.php`.
All output escaped. No database queries in the view.

---

### public/match.php (update)

Extend the existing match detail controller to handle attendance:

- Require bootstrap
- Call `requireLogin()`
- Read `id` and `team_id` from GET, validate both are positive integers
- Load team via `TeamModel::findForUser()` — if null, redirect to `teams.php`
- Load match via `MatchModel::findForTeam()` — if null, redirect to `matches.php?team_id={team_id}`
- Load attendance data via `AttendanceModel::findForMatch()`
- Load status summary via `AttendanceModel::getStatusSummary()`
- Pass flash message from session if present
- GET: render `matches/detail` with match, players with statuses, and summary
- POST:
  - Validate CSRF
  - Read submitted `statuses` array from POST (format: `statuses[player_id] = status`)
  - Call `AttendanceModel::saveStatuses()`
  - Set flash success: `__('attendance.saved')`
  - Redirect to `match.php?id={id}&team_id={team_id}` (POST-redirect-GET pattern)

---

### Update app/views/matches/index.php

Add a `selected_count` indicator per match row:
- Show "X selected" if selected_count > 0
- Show nothing or a neutral indicator if 0
- This gives the coach a quick overview of which matches have been prepared

---

## Validation rules

All status values submitted in the form must be validated server-side:

```php
$allowedStatuses = ['unknown', 'available', 'unavailable', 'selected'];
if (!in_array($status, $allowedStatuses, true)) {
    // skip this entry silently — do not crash
}
```

Player IDs from POST must be validated as positive integers:

```php
if (!ctype_digit((string)$playerId) || (int)$playerId <= 0) {
    // skip this entry silently
}
```

After numeric validation, each player ID must still be confirmed to belong to the team via the database query — never trust the submitted value alone.

---

## Language keys

Add these to both `lang/en.php` and `lang/nl.php`:

```
attendance.title
attendance.save
attendance.saved
attendance.status.unknown
attendance.status.available
attendance.status.unavailable
attendance.status.selected
attendance.summary
attendance.selected_count
attendance.no_players
```

English values:
- `attendance.title` → `Attendance`
- `attendance.save` → `Save attendance`
- `attendance.saved` → `Attendance saved.`
- `attendance.status.unknown` → `Unknown`
- `attendance.status.available` → `Available`
- `attendance.status.unavailable` → `Unavailable`
- `attendance.status.selected` → `Selected`
- `attendance.summary` → `:selected selected · :available available · :unavailable unavailable · :unknown unknown`
- `attendance.selected_count` → `:count selected`
- `attendance.no_players` → `No active players in this team. Add players before setting attendance.`

Dutch values:
- `attendance.title` → `Aanwezigheid`
- `attendance.save` → `Aanwezigheid opslaan`
- `attendance.saved` → `Aanwezigheid opgeslagen.`
- `attendance.status.unknown` → `Onbekend`
- `attendance.status.available` → `Beschikbaar`
- `attendance.status.unavailable` → `Niet beschikbaar`
- `attendance.status.selected` → `Geselecteerd`
- `attendance.summary` → `:selected geselecteerd · :available beschikbaar · :unavailable niet beschikbaar · :unknown onbekend`
- `attendance.selected_count` → `:count geselecteerd`
- `attendance.no_players` → `Geen actieve spelers in dit team. Voeg spelers toe voordat je aanwezigheid instelt.`

---

## CSS additions

Add to `public/assets/css/components.css` or `public/assets/css/pages.css`:

```css
/* Attendance screen — player rows */
.attendance-row { ... }              /* each player row */
.attendance-row--selected { ... }    /* visually distinct for selected players */
.attendance-status { ... }           /* the select or radio group */
.attendance-summary { ... }          /* the summary line */
.attendance-save { ... }             /* the save button — large, full-width on mobile */
```

Rules:
- Touch targets for status controls must be at least 44px height
- Selected players must be visually distinct using more than color alone (e.g. bold + background)
- The save button must be easy to tap at the bottom of the list on mobile
- The layout must remain usable with 20+ players in the list

---

## Optional: lightweight JavaScript enhancement

If JavaScript is added, it must be strictly optional — the form must work without it.

Acceptable use: visually highlight a player row immediately when the status changes to 'selected', without a page reload. This is purely cosmetic.

Rules:
- No JavaScript for security or validation
- No JavaScript for form submission
- No fetch() or AJAX — standard form POST only
- Keep the script small, page-specific, in `public/assets/js/match_attendance.js`
- Graceful degradation: if JS is disabled, the form still works completely

---

## Access control rules

- Every access requires login
- The team must belong to the current user — verified via JOIN on `teams.user_id`
- The match must belong to that team — verified via JOIN on `matches.team_id`
- Each submitted player ID must belong to the team — verified in `saveStatuses()` via JOIN on `players.team_id`
- A non-numeric or missing ID redirects, never crashes
- Never save a status without first confirming that the player belongs to the same team as the match

Critical: the form shows only the user's own players, but the POST handler must re-validate every submitted player ID independently. A submitted player_id that doesn't belong to the team must be silently skipped, never saved.

---

## Security requirements

- CSRF token on the attendance form POST
- Validate CSRF before processing
- Prepared statements for all queries including the upsert
- Escape all output
- No SQL in public files
- No `global` keyword
- POST-redirect-GET pattern after saving attendance (prevents duplicate submissions on refresh)
- Invalid status values silently skipped, never stored

---

## After completing all files

1. Run the migration:
```bash
mysql -u barepitch_user -p barepitch_local < database/migrations/005_create_match_players_table.sql
```

2. Commit:
```bash
git add .
git commit -m "feat: add attendance tracking with player selection per match"
```

3. When the full attendance flow works:
```bash
git checkout main
git merge wip
git tag v0.6.0
git push
git push --tags
git checkout wip
git merge main
gh release create v0.6.0 --title "v0.6.0 Attendance Tracking" --notes "Coaches can set availability and selection status per player per match. Status persists across sessions. Match list shows selected player count."
```

4. Confirm:
- [ ] Match detail page shows all active players for the team
- [ ] Each player has a status control pre-filled with their current status (or unknown)
- [ ] Saving updates all statuses in the database via upsert
- [ ] Refreshing the page after saving shows the correct saved statuses
- [ ] Summary line shows correct counts per status
- [ ] Match list shows selected player count per match
- [ ] Selected players are visually distinct on the attendance screen
- [ ] Empty state shown if the team has no active players
- [ ] Submitting a player_id from another team is silently ignored (test manually)
- [ ] Submitting an invalid status value is silently ignored
- [ ] CSRF validation on the POST action
- [ ] POST-redirect-GET prevents duplicate submissions on page refresh
- [ ] Attendance screen is usable on mobile — controls large enough to tap
- [ ] All output is escaped
- [ ] No SQL in public files
