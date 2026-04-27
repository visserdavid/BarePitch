# Prompt: Build v0.5.0 — Match Management

Follow all conventions in CLAUDE.md.

Create a checkpoint commit before making any changes:

```bash
git add .
git commit -m "checkpoint: before building v0.5.0 match management"
```

---

## Context

BarePitch is a minimal PHP/MySQL application for football coaches. Player management (v0.4.0) is complete. This prompt implements v0.5.0: full match management within a team.

Stack: plain PHP 8.x, MySQL 8 via PDO, HTML, CSS, vanilla JS. No frameworks, no Composer.

All conventions from CLAUDE.md apply:
- Bootstrap chain: every public file starts with `require '../app/config/bootstrap.php'`
- Database access: always via `getPdo()`, never `global $pdo`
- All output: `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` or the `e()` helper
- All queries: prepared statements, never string concatenation
- All POST actions: validate CSRF with `validateCsrf()`, check ownership server-side
- All user-facing strings: `__()` helper — never hardcode interface text
- Access control: deny by default; ownership chain is user → team → match

---

## Migration

Create `database/migrations/004_create_matches_table.sql`:

```sql
CREATE TABLE matches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_id INT UNSIGNED NOT NULL,
    opponent_name VARCHAR(100) NOT NULL,
    match_date DATE NOT NULL,
    kickoff_time TIME NULL,
    location VARCHAR(150) NULL,
    home_away ENUM('home', 'away', 'neutral') NULL,
    status ENUM('planned', 'completed', 'archived') NOT NULL DEFAULT 'planned',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,

    CONSTRAINT fk_matches_team
        FOREIGN KEY (team_id)
        REFERENCES teams(id)
        ON DELETE CASCADE,

    INDEX idx_matches_team_id (team_id),
    INDEX idx_matches_match_date (match_date),
    INDEX idx_matches_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Files to create

### app/models/MatchModel.php

Methods:

- `findAllForTeam(int $teamId, int $userId): array`
  - Returns all non-deleted, non-archived matches for a team, only if the team belongs to the user
  - Ordered by: match_date DESC, kickoff_time ASC
  - Upcoming matches (match_date >= today) first, then past matches

- `findUpcomingForTeam(int $teamId, int $userId): array`
  - Returns matches where match_date >= today, status = 'planned', not deleted
  - Ordered by match_date ASC, kickoff_time ASC

- `findPastForTeam(int $teamId, int $userId): array`
  - Returns matches where match_date < today, not deleted, not archived
  - Ordered by match_date DESC

- `findForTeam(int $matchId, int $teamId, int $userId): ?array`
  - Returns one match only if it belongs to the team, and the team belongs to the user
  - Includes all fields including optional ones
  - Returns null if not found or unauthorized — never throw

- `create(int $teamId, string $opponentName, string $matchDate, ?string $kickoffTime, ?string $location, ?string $homeAway): int`
  - Inserts a new match, returns the new ID

- `update(int $matchId, int $teamId, int $userId, string $opponentName, string $matchDate, ?string $kickoffTime, ?string $location, ?string $homeAway): bool`
  - Updates all editable fields only if ownership is confirmed
  - Returns true on success, false otherwise

- `archive(int $matchId, int $teamId, int $userId): bool`
  - Sets status = 'archived' only if ownership is confirmed
  - Returns true on success, false otherwise

All methods use `getPdo()`. Ownership always verified through JOIN on teams.user_id. No raw SQL concatenation. No `global` keyword.

---

### app/services/MatchService.php

Methods:

- `validateMatchInput(array $post): array`
  - Validates:
    - `opponent_name`: required, max 100 characters
    - `match_date`: required, valid date in Y-m-d format, not an impossible date (e.g. Feb 31)
    - `kickoff_time`: optional; if provided, must be valid HH:MM format
    - `location`: optional, max 150 characters
    - `home_away`: optional; if provided, must be one of: home, away, neutral
  - Returns array of error strings keyed by field name
  - Trims all input before validating
  - Returns empty array if valid

---

### app/views/matches/index.php

Purpose: list all matches for a team.

Elements:
- Page title: `__('matches.title')` — include team name for context
- Link back to teams list
- Button to create a new match
- Two sections: upcoming matches and past matches
- Per match row: date, kickoff time (if set), opponent name, home/away badge (if set), location (if set), status, edit link, archive form
- Empty state if no matches exist: `__('matches.empty')`
- Flash message display

Layout: include `layouts/header.php` and `layouts/footer.php`.
All output escaped. No database queries in the view.

---

### app/views/matches/create.php

Purpose: form to create a new match.

Elements:
- Page title: `__('matches.create')`
- Link back to match list for this team
- Hidden field for `team_id`
- Form fields:
  - Opponent name (required, text input)
  - Match date (required, date input `type="date"`)
  - Kickoff time (optional, time input `type="time"`)
  - Location (optional, text input)
  - Home / Away / Neutral (optional, select with blank option)
- CSRF hidden field
- Submit button
- Validation error messages near each field
- Safe input preserved after validation failure

Layout: include `layouts/header.php` and `layouts/footer.php`.

---

### app/views/matches/edit.php

Purpose: form to edit an existing match.

Elements:
- Page title: `__('matches.edit')`
- Link back to match list for this team
- Same fields as create, pre-filled with current values
- CSRF hidden field
- Submit button
- Separate POST form to archive the match — with CSRF token and confirmation text
- Link to match detail page (for future attendance, shown as disabled or placeholder for now)
- Validation error messages near each field

Layout: include `layouts/header.php` and `layouts/footer.php`.

---

### app/views/matches/detail.php

Purpose: show match details and act as the future entry point for attendance.

Elements:
- Page title: opponent name + match date
- Match summary: date, kickoff time, location, home/away, status
- Team name with link back to match list
- Edit match link
- Placeholder section for attendance (visible but inactive): "Player selection — coming in the next version."
- Back link to match list

This view is intentionally minimal. Attendance will be added in v0.6.0. The structure must be in place now so v0.6.0 can extend it cleanly.

Layout: include `layouts/header.php` and `layouts/footer.php`.

---

### public/matches.php

- Require bootstrap
- Call `requireLogin()`
- Read `team_id` from GET, validate it is a positive integer; redirect to `teams.php` if invalid
- Load team via `TeamModel::findForUser()` — if null, redirect to `teams.php`
- Load upcoming and past matches separately via MatchModel
- Pass flash message from session if present
- Render `matches/index` with team, upcoming matches, and past matches

---

### public/match_create.php

- Require bootstrap
- Call `requireLogin()`
- Read `team_id` from GET or POST, validate it is a positive integer
- Load team via `TeamModel::findForUser()` — if null, redirect to `teams.php`
- GET: render `matches/create` with team context
- POST:
  - Validate CSRF
  - Validate input via MatchService
  - If valid: create match, set flash success, redirect to `matches.php?team_id={team_id}`
  - If invalid: re-render form with errors and preserved input

---

### public/match_edit.php

- Require bootstrap
- Call `requireLogin()`
- Read `id` and `team_id` from GET or POST, validate both are positive integers
- Load team via `TeamModel::findForUser()` — if null, redirect to `teams.php`
- Load match via `MatchModel::findForTeam()` — if null, redirect to `matches.php?team_id={team_id}`
- GET: render `matches/edit` with current match data
- POST with action=update:
  - Validate CSRF
  - Validate input via MatchService
  - If valid: update, set flash success, redirect to `match_edit.php?id={id}&team_id={team_id}`
  - If invalid: re-render form with errors and preserved input
- POST with action=archive:
  - Validate CSRF
  - Archive the match, set flash success, redirect to `matches.php?team_id={team_id}`

---

### public/match.php

- Require bootstrap
- Call `requireLogin()`
- Read `id` and `team_id` from GET, validate both are positive integers
- Load team via `TeamModel::findForUser()` — if null, redirect to `teams.php`
- Load match via `MatchModel::findForTeam()` — if null, redirect to `matches.php?team_id={team_id}`
- Render `matches/detail`

---

## Validation rules

| Field         | Rule                                                              |
|---------------|-------------------------------------------------------------------|
| opponent_name | Required, trimmed, max 100 characters                             |
| match_date    | Required, valid Y-m-d date, must parse correctly via DateTime     |
| kickoff_time  | Optional; if provided, must match HH:MM format (00:00–23:59)      |
| location      | Optional, trimmed, max 150 characters                             |
| home_away     | Optional; if provided, must be one of: home, away, neutral        |

Kickoff time validation example:
```php
$time = trim($_POST['kickoff_time'] ?? '');
if ($time !== '') {
    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time)) {
        $errors['kickoff_time'] = __('validation.invalid_time');
    }
}
```

---

## Language keys

Add these to both `lang/en.php` and `lang/nl.php`:

```
matches.title
matches.create
matches.edit
matches.detail
matches.opponent
matches.date
matches.kickoff
matches.location
matches.home_away
matches.status
matches.created
matches.updated
matches.archived
matches.empty
matches.home
matches.away
matches.neutral
matches.status.planned
matches.status.completed
matches.status.archived
matches.attendance_placeholder
validation.invalid_time
```

English values:
- `matches.home` → `Home`
- `matches.away` → `Away`
- `matches.neutral` → `Neutral`
- `matches.status.planned` → `Planned`
- `matches.status.completed` → `Completed`
- `matches.status.archived` → `Archived`
- `matches.attendance_placeholder` → `Player selection — coming in the next version.`
- `validation.invalid_time` → `Please enter a valid time (HH:MM).`

Dutch values:
- `matches.home` → `Thuis`
- `matches.away` → `Uit`
- `matches.neutral` → `Neutraal`
- `matches.status.planned` → `Gepland`
- `matches.status.completed` → `Gespeeld`
- `matches.status.archived` → `Gearchiveerd`
- `matches.attendance_placeholder` → `Spelerselectie — beschikbaar in de volgende versie.`
- `validation.invalid_time` → `Voer een geldige tijd in (UU:MM).`

---

## Access control rules

- Every page requires login
- The team must belong to the current user — verify via JOIN on `teams.user_id`
- The match must belong to that team — verify via JOIN on `matches.team_id`
- A non-numeric or missing `id` or `team_id` redirects, never crashes
- Archived matches are hidden from the default list but remain retrievable if accessed directly
- Archive and update POST actions repeat the ownership check independently

---

## Security requirements

- CSRF token on every POST form
- Validate CSRF before any processing
- Prepared statements for all queries
- Escape all output with `e()` or `htmlspecialchars()`
- No SQL in public files
- No `global` keyword
- Hidden `team_id` fields are never trusted — always re-verify ownership in the database

---

## Navigation update

Update `app/views/players/index.php` and team-related views to include a link to the match list per team:

```
Matches → matches.php?team_id={team_id}
```

Also update `app/views/teams/index.php` to show both links per team row:
- Players → `players.php?team_id={team_id}`
- Matches → `matches.php?team_id={team_id}`

---

## Seed data update

Add fictional matches to `database/seeds/dev_seed.sql` for the existing test team:

```sql
INSERT INTO matches (team_id, opponent_name, match_date, kickoff_time, location, home_away, status) VALUES
(1, 'FC Voorbeeld', '2026-05-10', '14:30:00', 'Sportpark Noord', 'home', 'planned'),
(1, 'VV Testclub', '2026-05-17', '10:00:00', NULL, 'away', 'planned'),
(1, 'SC Demo', '2026-04-05', '13:00:00', 'Sportpark Zuid', 'home', 'planned');
```

Adjust `team_id` to match the seeded test team. Use fictional names only.

---

## After completing all files

1. Run the migration:
```bash
mysql -u barepitch_user -p barepitch_local < database/migrations/004_create_matches_table.sql
```

2. Optionally re-run seed data:
```bash
mysql -u barepitch_user -p barepitch_local < database/seeds/dev_seed.sql
```

3. Commit:
```bash
git add .
git commit -m "feat: add match management (create, edit, archive, list, detail)"
```

4. When the full match flow works:
```bash
git checkout main
git merge wip
git tag v0.5.0
git push
git push --tags
git checkout wip
git merge main
gh release create v0.5.0 --title "v0.5.0 Match Management" --notes "Coaches can create, edit, archive and view matches per team. Match detail page is in place as foundation for v0.6.0 attendance."
```

5. Confirm:
- [ ] Match list shows upcoming and past matches separately, only for the current user's team
- [ ] Create match works with validation (opponent and date required, time/location/home_away optional)
- [ ] Edit match works with all fields pre-filled
- [ ] Archive removes the match from the default list
- [ ] Match detail page loads with all match information
- [ ] Attendance placeholder is visible on the detail page
- [ ] Changing team_id or match id in the URL does not expose another user's data
- [ ] Flash messages appear after create, update and archive
- [ ] Empty state shown when a team has no matches
- [ ] Team list links to both players and matches per team
- [ ] All output is escaped
- [ ] CSRF validation on all POST actions
- [ ] Invalid kickoff time (e.g. 25:00) is rejected with a clear error
- [ ] Invalid date (e.g. 2026-02-31) is rejected with a clear error
