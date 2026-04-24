# Prompt: Build v0.4.0 — Player Management

Follow all conventions in CLAUDE.md.

Create a checkpoint commit before making any changes:

```bash
git add .
git commit -m "checkpoint: before building v0.4.0 player management"
```

---

## Context

BarePitch is a minimal PHP/MySQL application for football coaches. Team management (v0.3.0) is complete. This prompt implements v0.4.0: full player management within a team.

Stack: plain PHP 8.x, MySQL 8 via PDO, HTML, CSS, vanilla JS. No frameworks, no Composer.

All conventions from CLAUDE.md apply:
- Bootstrap chain: every public file starts with `require '../app/config/bootstrap.php'`
- Database access: always via `getPdo()`, never `global $pdo`
- All output: `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` or the `e()` helper
- All queries: prepared statements, never string concatenation
- All POST actions: validate CSRF with `validateCsrf()`, check ownership server-side
- All user-facing strings: `__()` helper — never hardcode interface text
- Access control: deny by default; ownership chain is user → team → player

---

## Migration

Create `database/migrations/003_create_players_table.sql`:

```sql
CREATE TABLE players (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_id INT UNSIGNED NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    shirt_number TINYINT UNSIGNED NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,

    CONSTRAINT fk_players_team
        FOREIGN KEY (team_id)
        REFERENCES teams(id)
        ON DELETE CASCADE,

    INDEX idx_players_team_id (team_id),
    INDEX idx_players_status (status),
    INDEX idx_players_shirt_number (shirt_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Files to create

### app/models/PlayerModel.php

Methods:

- `findAllForTeam(int $teamId, int $userId): array`
  - Returns all non-deleted players for a team, only if the team belongs to the user
  - Includes both active and inactive players
  - Ordered by: shirt_number ASC (nulls last), then display_name ASC

- `findActiveForTeam(int $teamId, int $userId): array`
  - Same as above but only returns players with status = 'active'
  - Used by attendance later

- `findForTeam(int $playerId, int $teamId, int $userId): ?array`
  - Returns one player only if it belongs to the team, and the team belongs to the user
  - Returns null if not found or unauthorized — never throw

- `create(int $teamId, string $displayName, ?int $shirtNumber): int`
  - Inserts a new player, returns the new ID

- `update(int $playerId, int $teamId, int $userId, string $displayName, ?int $shirtNumber): bool`
  - Updates display_name and shirt_number only if ownership is confirmed
  - Returns true on success, false otherwise

- `setInactive(int $playerId, int $teamId, int $userId): bool`
  - Sets status = 'inactive' only if ownership is confirmed
  - Preferred over deletion when the player has match history
  - Returns true on success, false otherwise

- `hasMatchHistory(int $playerId): bool`
  - Returns true if the player appears in any match_players record
  - Used to decide between deactivation and deletion

- `delete(int $playerId, int $teamId, int $userId): bool`
  - Hard deletes a player only if ownership is confirmed AND hasMatchHistory() returns false
  - If the player has match history, do not delete — set inactive instead
  - Returns true on success, false otherwise

All methods use `getPdo()`. No raw SQL concatenation. Ownership always verified through JOIN on teams.user_id.

---

### app/services/PlayerService.php

Methods:

- `validatePlayerInput(array $post): array`
  - Validates display_name (required, max 100 chars) and shirt_number (optional, integer 1–99)
  - Returns array of error strings keyed by field name
  - Trims input before validating
  - Returns empty array if valid

---

### app/views/players/index.php

Purpose: list all players in a team.

Elements:
- Page title: `__('players.title')` — include team name for context
- Link back to teams list
- Link/button to add a new player
- Table or list with: shirt number (or dash if empty), display name, status badge, edit link, deactivate/delete form
- Inactive players visually distinct (e.g. dimmed or labelled)
- Empty state message if no players exist: `__('players.empty')`
- Flash message display (success or error)

Layout: include `layouts/header.php` and `layouts/footer.php`.
All output escaped. No database queries in the view.

---

### app/views/players/create.php

Purpose: form to add a new player to a team.

Elements:
- Page title: `__('players.add')`
- Link back to player list for this team
- Hidden field for `team_id`
- Form with: display name (required), shirt number (optional)
- CSRF hidden field
- Submit button
- Validation error messages near each field
- Safe input preserved after validation failure

Layout: include `layouts/header.php` and `layouts/footer.php`.

---

### app/views/players/edit.php

Purpose: form to edit an existing player.

Elements:
- Page title: `__('players.edit')`
- Link back to player list for this team
- Form with: display name (required), shirt number (optional), pre-filled with current values
- CSRF hidden field
- Submit button
- Separate POST form to set player inactive (if currently active) — with CSRF token and confirmation
- Separate POST form to delete player (only shown if hasMatchHistory() is false) — with CSRF token and strong confirmation text
- Validation error messages near each field

Layout: include `layouts/header.php` and `layouts/footer.php`.

---

### public/players.php

- Require bootstrap
- Call `requireLogin()`
- Read `team_id` from GET, validate it is a positive integer; redirect to `teams.php` if invalid
- Load team via `TeamModel::findForUser()` — if null, redirect to `teams.php`
- Load all players for the team via `PlayerModel::findAllForTeam()`
- Pass flash message from session if present
- Render `players/index`

---

### public/player_create.php

- Require bootstrap
- Call `requireLogin()`
- Read `team_id` from GET or POST, validate it is a positive integer
- Load team via `TeamModel::findForUser()` — if null, redirect to `teams.php`
- GET: render `players/create` with team context
- POST:
  - Validate CSRF
  - Validate input via PlayerService
  - If valid: create player, set flash success, redirect to `players.php?team_id={team_id}`
  - If invalid: re-render form with errors and preserved input

---

### public/player_edit.php

- Require bootstrap
- Call `requireLogin()`
- Read `id` and `team_id` from GET or POST, validate both are positive integers
- Load team via `TeamModel::findForUser()` — if null, redirect to `teams.php`
- Load player via `PlayerModel::findForTeam()` — if null, redirect to `players.php?team_id={team_id}`
- GET: render `players/edit` with current player data and hasMatchHistory() result
- POST with action=update:
  - Validate CSRF
  - Validate input via PlayerService
  - If valid: update, set flash success, redirect to `player_edit.php?id={id}&team_id={team_id}`
  - If invalid: re-render form with errors
- POST with action=deactivate:
  - Validate CSRF
  - Set player inactive, set flash success, redirect to `players.php?team_id={team_id}`
- POST with action=delete:
  - Validate CSRF
  - Check hasMatchHistory() — if true, set flash error and redirect (never delete)
  - If false: delete, set flash success, redirect to `players.php?team_id={team_id}`

---

## Validation rules

| Field        | Rule                                                      |
|--------------|-----------------------------------------------------------|
| display_name | Required, trimmed, max 100 characters                     |
| shirt_number | Optional; if provided: integer, between 1 and 99 inclusive |

Error messages must use `__()` keys. Add these to both `lang/en.php` and `lang/nl.php` if missing:

```
players.title
players.add
players.edit
players.display_name
players.shirt_number
players.status
players.created
players.updated
players.deactivated
players.deleted
players.empty
players.has_match_history
validation.shirt_number_range
```

English values:
- `players.deactivated` → `Player set to inactive.`
- `players.deleted` → `Player removed.`
- `players.has_match_history` → `This player has match history and cannot be deleted. Set them to inactive instead.`
- `validation.shirt_number_range` → `Shirt number must be between 1 and 99.`

Dutch values:
- `players.deactivated` → `Speler op inactief gezet.`
- `players.deleted` → `Speler verwijderd.`
- `players.has_match_history` → `Deze speler heeft wedstrijdhistorie en kan niet worden verwijderd. Zet de speler op inactief.`
- `validation.shirt_number_range` → `Rugnummer moet tussen 1 en 99 liggen.`

---

## Access control rules

- Every page requires login
- The team must belong to the current user — verify via JOIN on `teams.user_id`
- The player must belong to that team — verify via JOIN on `players.team_id`
- A non-numeric or missing `id` or `team_id` redirects, never crashes
- Inactive players remain visible but are clearly marked
- A player with match history cannot be hard deleted — only deactivated
- No player data from another user is ever returned

---

## Security requirements

- CSRF token on every POST form
- Validate CSRF before any processing
- Prepared statements for all queries
- Escape all output with `e()` or `htmlspecialchars()`
- No SQL in public files
- No `global` keyword
- Hidden `team_id` fields are never trusted — always re-verify ownership in the database
- Deactivate and delete actions repeat ownership check independently on POST

---

## Navigation update

Update the team list view (`app/views/teams/index.php`) and team edit view to include a link to the player list per team:

```
Players → players.php?team_id={team_id}
```

---

## Seed data update

Add fictional players to `database/seeds/dev_seed.sql` for the existing test team. Use fictional names only — no real people. Example:

```sql
INSERT INTO players (team_id, display_name, shirt_number, status) VALUES
(1, 'Jan de Vries', 1, 'active'),
(1, 'Pieter Bakker', 5, 'active'),
(1, 'Klaas Smit', 9, 'active'),
(1, 'Thomas Visser', 11, 'active'),
(1, 'Erik Meijer', NULL, 'active');
```

Adjust `team_id` to match the seeded test team.

---

## After completing all files

1. Run the migration:
```bash
mysql -u barepitch_user -p barepitch_local < database/migrations/003_create_players_table.sql
```

2. Optionally re-run seed data:
```bash
mysql -u barepitch_user -p barepitch_local < database/seeds/dev_seed.sql
```

3. Commit:
```bash
git add .
git commit -m "feat: add player management (add, edit, deactivate, delete, list)"
```

4. When the full player flow works:
```bash
git checkout main
git merge wip
git tag v0.4.0
git push
git push --tags
git checkout wip
git merge main
gh release create v0.4.0 --title "v0.4.0 Player Management" --notes "Coaches can add, edit, deactivate and delete players within a team. Players with match history are protected from deletion."
```

5. Confirm:
- [ ] Player list shows only players from the current user's team
- [ ] Add player works with validation (name required, shirt number optional and range-checked)
- [ ] Edit player works with pre-filled values
- [ ] Deactivate sets status to inactive and player appears as inactive in the list
- [ ] Delete is blocked when the player has match history — error message shown
- [ ] Delete works when the player has no match history
- [ ] Changing team_id or player id in the URL does not expose another user's data
- [ ] Flash messages appear after create, update, deactivate and delete
- [ ] Empty state shown when a team has no players
- [ ] Team list and edit views link to the player list
- [ ] All output is escaped
- [ ] CSRF validation on all POST actions
