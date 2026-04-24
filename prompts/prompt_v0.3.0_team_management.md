# Prompt: Build v0.3.0 — Team Management

Follow all conventions in CLAUDE.md.

Create a checkpoint commit before making any changes:

```bash
git add .
git commit -m "checkpoint: before building v0.3.0 team management"
```

---

## Context

BarePitch is a minimal PHP/MySQL application for football coaches. Authentication (v0.2.0) is complete. This prompt implements v0.3.0: full team management.

Stack: plain PHP 8.x, MySQL 8 via PDO, HTML, CSS, vanilla JS. No frameworks, no Composer.

All conventions from CLAUDE.md apply:
- Bootstrap chain: every public file starts with `require '../app/config/bootstrap.php'`
- Database access: always via `getPdo()`, never `global $pdo`
- All output: `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` or the `e()` helper
- All queries: prepared statements, never string concatenation
- All POST actions: validate CSRF with `validateCsrf()`, check ownership server-side
- All user-facing strings: `__()` helper — never hardcode interface text
- Access control: deny by default, check ownership on every GET and POST

---

## Migration

Create `database/migrations/002_create_teams_table.sql`:

```sql
CREATE TABLE teams (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    season VARCHAR(20) NULL,
    status ENUM('active', 'archived') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,

    CONSTRAINT fk_teams_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_teams_user_id (user_id),
    INDEX idx_teams_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Files to create

### app/models/TeamModel.php

Methods:

- `findAllForUser(int $userId): array`
  - Returns all active (non-archived, non-deleted) teams for the user
  - Ordered by name ASC
  - Include player_count and match_count via LEFT JOIN

- `findForUser(int $teamId, int $userId): ?array`
  - Returns one team only if it belongs to the user and is not deleted
  - Returns null otherwise — never throw

- `create(int $userId, string $name, ?string $season): int`
  - Inserts a new team, returns the new ID

- `update(int $teamId, int $userId, string $name, ?string $season): bool`
  - Updates name and season only if ownership matches
  - Returns true on success, false otherwise

- `archive(int $teamId, int $userId): bool`
  - Sets status = 'archived' only if ownership matches
  - Returns true on success, false otherwise

All methods use `getPdo()`. No raw SQL concatenation. No `global` keyword.

---

### app/services/TeamService.php

Methods:

- `validateTeamInput(array $post): array`
  - Validates name (required, max 100 chars) and season (optional, max 20 chars)
  - Returns array of error strings keyed by field name
  - Trims input before validating
  - Returns empty array if valid

---

### app/views/teams/index.php

Purpose: list all teams for the logged-in user.

Elements:
- Page title: `__('teams.title')`
- Link/button to create a new team
- Table or list of teams with: name, season, player count, match count, status
- Edit link per team
- Archive form per team (POST, with CSRF token and confirmation)
- Empty state message if no teams exist: `__('teams.empty')`
- Flash message display (success or error)

Layout: include `layouts/header.php` and `layouts/footer.php`.
All output escaped. No database queries in the view.

---

### app/views/teams/create.php

Purpose: form to create a new team.

Elements:
- Page title: `__('teams.create')`
- Form with: team name (required), season (optional)
- CSRF hidden field
- Submit button
- Validation error messages near the relevant field
- Safe input preserved after validation failure

Layout: include `layouts/header.php` and `layouts/footer.php`.

---

### app/views/teams/edit.php

Purpose: form to edit an existing team.

Elements:
- Page title: `__('teams.edit')`
- Same fields as create, pre-filled with current values
- CSRF hidden field
- Submit button
- Archive button (separate POST form with CSRF)
- Validation error messages near the relevant field

Layout: include `layouts/header.php` and `layouts/footer.php`.

---

### public/teams.php

- Require bootstrap
- Call `requireLogin()`
- Load all teams for the current user via TeamModel
- Pass flash message from session if present
- Render `teams/index`

---

### public/team_create.php

- Require bootstrap
- Call `requireLogin()`
- GET: render `teams/create` (empty form)
- POST:
  - Validate CSRF
  - Trim and validate input via TeamService
  - If valid: create team, set flash success, redirect to `teams.php`
  - If invalid: re-render `teams/create` with errors and preserved input

---

### public/team_edit.php

- Require bootstrap
- Call `requireLogin()`
- Read `id` from GET/POST, validate it is a positive integer
- Load team via `TeamModel::findForUser()` — if null, redirect to `teams.php`
- GET: render `teams/edit` with current team data
- POST with action=update:
  - Validate CSRF
  - Validate input via TeamService
  - If valid: update, set flash success, redirect to `team_edit.php?id={id}`
  - If invalid: re-render form with errors
- POST with action=archive:
  - Validate CSRF
  - Archive the team, set flash success, redirect to `teams.php`

---

### app/helpers/flash.php

A minimal flash message system using the session.

Functions:
- `setFlash(string $type, string $message): void` — stores message in `$_SESSION['flash']`
- `getFlash(): ?array` — returns and clears the flash message, or null if none

Register this helper in `app/config/bootstrap.php` alongside the others.

---

### lang/en.php and lang/nl.php additions

Add any missing team-related keys to both language files. Verify these keys exist:

```
teams.title
teams.create
teams.edit
teams.name
teams.season
teams.status
teams.created
teams.updated
teams.archived
teams.empty
general.save
general.cancel
general.archive
general.confirm
```

---

### Dashboard update

Update `public/dashboard.php` to:
- Show a link to `teams.php`
- Show the count of active teams for the current user (query via TeamModel)
- Keep it minimal — no large table, just a summary

---

## CSS additions

Add reusable styles to the appropriate existing CSS files:

- `public/assets/css/components.css` (create if not exists):
  - `.flash` — success and error flash message styles
  - `.table` — basic table layout
  - `.btn`, `.btn-primary`, `.btn-danger`, `.btn-secondary` — button variants
  - `.empty-state` — centered message for empty lists

- `public/assets/css/forms.css` (create if not exists):
  - `.form-group` — label + input stacked
  - `.form-error` — inline error message styling
  - `.form-actions` — button row at bottom of form

---

## Validation rules

| Field   | Rule                                      |
|---------|-------------------------------------------|
| name    | Required, trimmed, max 100 characters     |
| season  | Optional, trimmed, max 20 characters      |

Error messages must use `__('validation.required')` and `__('validation.too_long')`.

---

## Access control rules

- Every page requires login
- `findForUser()` always includes `user_id` in the query — never load by ID alone
- Archive and update POST actions repeat the ownership check independently
- A non-numeric or missing `id` in the URL redirects to `teams.php`, never crashes
- Archived teams do not appear in the default list
- No team data from another user is ever returned

---

## Security requirements

- CSRF token on every POST form
- Validate CSRF before any processing
- Prepared statements for all queries
- Escape all output with `e()` or `htmlspecialchars()`
- No SQL in public files
- No `global` keyword
- No sensitive data in error messages

---

## After completing all files

1. Run the migration manually:
```bash
mysql -u barepitch_user -p barepitch_local < database/migrations/002_create_teams_table.sql
```

2. Commit:
```bash
git add .
git commit -m "feat: add team management (create, edit, archive, list)"
```

3. When the full team flow works (create, edit, archive, list, dashboard count):
```bash
git checkout main
git merge wip
git tag v0.3.0
git push
git push --tags
git checkout wip
git merge main
gh release create v0.3.0 --title "v0.3.0 Team Management" --notes "Coaches can create, edit, archive and list teams. Dashboard shows active team count."
```

4. Confirm:
- [ ] Teams list shows only the current user's teams
- [ ] Create team works with validation
- [ ] Edit team works with pre-filled values
- [ ] Archive removes the team from the active list
- [ ] Changing the team ID in the URL does not expose another user's team
- [ ] Flash messages appear after create, update and archive
- [ ] Empty state shown when no teams exist
- [ ] Dashboard shows team count and link
- [ ] All output is escaped
- [ ] CSRF validation on all POST actions
