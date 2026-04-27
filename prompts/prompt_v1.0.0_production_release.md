# Prompt: Build v1.0.0 — Production Preparation and Release

Follow all conventions in CLAUDE.md.

Create a checkpoint commit before making any changes:

```bash
git add .
git commit -m "checkpoint: before v1.0.0 production preparation"
```

---

## Context

BarePitch is a minimal PHP/MySQL application for football coaches. The security cleanup (v0.8.0) is complete. This prompt implements v1.0.0: the final preparation for a production-ready release.

This version adds no new features. It makes the application deployable, documented, and trustworthy.

The guiding question for every decision:

> Would you trust this system in a real moment, when time and attention are limited?

Stack: plain PHP 8.x, MySQL 8 via PDO, HTML, CSS, vanilla JS. No frameworks, no Composer.

---

## Scope

This version touches:

- Documentation files: `README.md`, `CHANGELOG.md`, `docs/`
- `docs/test-log.md` — final manual test run
- `docs/decisions.md` — final decisions recorded
- `public/index.php` — final production check
- `app/config/bootstrap.php` — production environment guard
- `app/views/` — remove any remaining debug output
- `.env.example` — final accuracy check
- No new migrations, no new features

---

## 1. Code cleanup before release

Scan the entire codebase for development artifacts that must not exist in v1.0.0.

**Remove:**
- Any `var_dump()`, `print_r()`, `die()`, `exit('debug...')` calls
- Any commented-out debug blocks
- Any hardcoded test email addresses or passwords in non-seed files
- Any `TODO` or `FIXME` comments that describe unfinished security or validation work
- Any unused `require` or `include` statements
- Any files in `public/` that were created for testing (e.g. `test.php`, `info.php`)

**Do not remove:**
- `database/seeds/dev_seed.sql` — this is intentionally local-only and not deployed
- `// Known limitation:` comments — these are intentional documentation
- `docs/` files — these ship with the repository

After cleanup, verify the application still runs locally end to end.

---

## 2. Final regression test

Perform a full manual test of the core flow before writing any documentation. Record results in `docs/test-log.md`.

Test script:

1. Open `http://localhost:8000` — confirm redirect to login
2. Attempt login with wrong password — confirm neutral error message, no email enumeration
3. Log in with correct credentials — confirm redirect to dashboard
4. Create a team with a name and season — confirm flash message, team appears in list
5. Edit the team — confirm values pre-filled, update works
6. Navigate to the team's player list — confirm empty state
7. Add three players, one with shirt number, two without — confirm all appear
8. Edit a player's name — confirm update works
9. Mark one player inactive — confirm player shows as inactive in list
10. Navigate to matches for the team — confirm empty state
11. Create a match with all fields filled — confirm flash message, match appears in upcoming list
12. Edit the match — confirm values pre-filled, update works
13. Open the match detail — confirm attendance section shows all active players
14. Set two players to Available, one to Selected, leave others as Unknown — confirm save works
15. Navigate away and return to the match — confirm statuses are preserved
16. Archive the match — confirm it disappears from the main list
17. Archive the team — confirm it disappears from the team list
18. Log out — confirm session destroyed, redirect to login
19. Press browser back — confirm protected page is not accessible
20. Log in again — confirm previous data still exists

**Authorization tests** (requires a second test user):
21. While logged in as User A, manually change a `team_id` in the URL to one owned by User B — confirm 404
22. While logged in as User A, manually change a `player_id` to one owned by User B's team — confirm 404
23. While logged in as User A, manually change a `match_id` to one owned by User B — confirm 404
24. Submit attendance form with a player ID from User B's team — confirm silently ignored

**Validation tests:**
25. Submit team creation form with empty name — confirm field error appears
26. Submit player form with shirt number "abc" — confirm field error appears
27. Submit match form with date "2026-02-31" — confirm field error appears
28. Submit match form with kickoff time "25:00" — confirm field error appears

**Security tests:**
29. Enter `<script>alert('xss')</script>` as a team name, save, view team list — confirm plain text output
30. Visit `/.env` in browser — confirm access denied
31. Visit `/app/config/bootstrap.php` in browser — confirm access denied
32. Visit `/storage/logs/app.log` in browser — confirm access denied
33. Submit a form with the CSRF token field removed — confirm 403 response

Record each result as passed or failed in `docs/test-log.md` with the date.

---

## 3. README.md

Write a complete, accurate `README.md`:

```markdown
# BarePitch

BarePitch is a minimal web application for football coaches to manage teams, players, matches and attendance.

## Purpose

A coach can use BarePitch to:

- manage one or more teams and their players
- create and track matches
- record player availability and selection per match

BarePitch is intentionally minimal. It does not include statistics, lineup visualization, multi-user access, or integrations.

## Stack

- PHP 8.2+
- MySQL 8 / MariaDB
- HTML, CSS, vanilla JavaScript
- No frameworks, no Composer, no build tools

## Requirements

- PHP 8.2 or higher with extensions: pdo, pdo_mysql, mbstring, openssl, session, json
- MySQL 8 or MariaDB
- A web server with the document root set to `public/` (Apache or Nginx)

## Local setup

1. Clone the repository
2. Copy `.env.example` to `.env` and fill in your database credentials
3. Create the database:
   ```sql
   CREATE DATABASE barepitch_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. Run migrations in order:
   ```bash
   mysql -u barepitch_user -p barepitch_local < database/migrations/001_create_users_table.sql
   mysql -u barepitch_user -p barepitch_local < database/migrations/002_create_teams_table.sql
   mysql -u barepitch_user -p barepitch_local < database/migrations/003_create_players_table.sql
   mysql -u barepitch_user -p barepitch_local < database/migrations/004_create_matches_table.sql
   mysql -u barepitch_user -p barepitch_local < database/migrations/005_create_match_players_table.sql
   ```
5. Optionally load seed data (fictional data, local only):
   ```bash
   mysql -u barepitch_user -p barepitch_local < database/seeds/dev_seed.sql
   ```
6. Start the local server:
   ```bash
   php -S localhost:8000 -t public
   ```
7. Open `http://localhost:8000` in your browser

## Configuration

All configuration is done via `.env`. See `.env.example` for all required variables.

Important: never commit `.env` to version control.

## Production

- Set `APP_ENV=production` and `APP_DEBUG=false`
- Use HTTPS — the application is not safe without it
- Set the web server document root to `public/` only
- Protect `app/`, `database/`, `storage/` from web access
- Run migrations on the production database
- Do not use seed data in production

## Language

Set `APP_LANG=nl` for Dutch or `APP_LANG=en` for English in `.env`.
Language files are in `lang/`.

## Adding a user

BarePitch has no registration UI in Version 1. Create a user manually:

```bash
php -r "echo password_hash('YourPassword', PASSWORD_DEFAULT);"
```

Then insert into the database:
```sql
INSERT INTO users (email, password_hash, display_name)
VALUES ('your@email.com', 'generated_hash', 'Your Name');
```

## Translations

To add a new language, copy `lang/en.php` to `lang/{locale}.php` and translate the values. Set `APP_LANG={locale}` in `.env`.

## License

MIT
```

---

## 4. CHANGELOG.md

Write a complete `CHANGELOG.md` covering all versions:

```markdown
# Changelog

## v1.0.0 — [current date]

First production-ready release.

- Production preparation: documentation, final cleanup, regression testing
- README and setup instructions complete
- All documentation files updated

## v0.8.0

Security cleanup.

- Full output escaping audit across all views
- Prepared statements verified in all models
- CSRF validation confirmed on every POST action
- Ownership checks verified on GET and POST independently
- ID validation on all URL parameters
- Session hardening: httponly, samesite, strict mode
- Global exception handler and error pages (403, 404, 500)
- Logging helper with logError() and logSecurity()
- .htaccess security headers and directory protection
- PHP-level security headers as fallback

## v0.7.0

Responsive and accessibility cleanup.

- Mobile-first CSS across all five stylesheet files
- Semantic HTML audit: headings, labels, buttons, focus
- Touch targets minimum 44px across all interactive elements
- Attendance screen optimised for mobile use
- Optional JavaScript enhancements for confirmation dialogs and status highlighting

## v0.6.0

Attendance tracking.

- Player attendance and selection status per match
- Statuses: unknown, available, unavailable, selected
- Upsert pattern with per-row ownership validation
- Status summary on match list and detail page
- POST-redirect-GET after saving attendance

## v0.5.0

Match management.

- Create, edit, archive and list matches per team
- Fields: opponent name, date, kickoff time, location, home/away
- Separate upcoming and past match sections
- Match detail page as foundation for attendance

## v0.4.0

Player management.

- Add, edit, deactivate and delete players within a team
- Shirt number (optional, 1–99)
- Players with match history protected from deletion
- Inactive players visible but marked

## v0.3.0

Team management.

- Create, edit, archive and list teams
- Season field (optional)
- Flash message system
- Dashboard shows active team count

## v0.2.0

Authentication.

- Login and logout with session management
- Secure password hashing with password_hash()
- Session regeneration on login
- Inactivity timeout (7200 seconds)
- CSRF protection on all POST actions

## v0.1.0

Project foundation.

- Project structure and directory layout
- Bootstrap chain: .env parsing, error reporting, session, helpers
- PDO database connection via getPdo()
- CSRF, auth, view, flash, and lang helpers
- i18n infrastructure with Dutch and English language files
- Git workflow established
```

---

## 5. docs/decisions.md

Ensure `docs/decisions.md` includes at minimum these decisions (add any not already present):

```markdown
## Decision: No framework

Context: BarePitch is a minimal tool with a narrow scope.
Decision: Plain PHP, no framework, no Composer.
Reason: Keeps the project understandable, avoids dependency management, and fits the minimal philosophy.

---

## Decision: Minimal player data

Context: More data fields increase privacy responsibility under GDPR.
Decision: Store only display name and optional shirt number.
Reason: Sufficient for coaching use, minimises legal risk.

---

## Decision: No file uploads in Version 1

Context: Uploads increase storage, privacy and security complexity.
Decision: BarePitch Version 1 does not support uploads.
Reason: Not needed for match preparation. Keeps the system simple and safe.

---

## Decision: Soft deletion for teams, players, and matches

Context: Hard deletion would break match history.
Decision: Use deleted_at (soft delete) or inactive/archived status instead of removing rows.
Reason: Preserves relational integrity without complex history tables.

---

## Decision: Lightweight array-based i18n

Context: The application is developed in English but used in Dutch by default.
Decision: PHP array files in lang/ with a __() helper. APP_LANG in .env.
Reason: Simple, no dependencies, easy to extend with new languages.

---

## Decision: No registration UI in Version 1

Context: BarePitch is a single-user or small-team tool.
Decision: Users are created manually via SQL and password_hash().
Reason: Registration adds complexity (email verification, security flows) that is not needed for the current use case.

---

## Decision: POST-only logout with CSRF

Context: GET-based logout can be triggered by third-party pages (logout CSRF).
Decision: Logout is always a POST request with CSRF validation.
Reason: Prevents session hijacking via social engineering.

---

## Decision: Attendance status limited to four values in Version 1

Context: More statuses (present, absent) add post-match tracking complexity.
Decision: Version 1 uses: unknown, available, unavailable, selected.
Reason: Covers match preparation without extending into post-match analysis.

---

## Decision: No rate limiting on login in Version 1

Context: Rate limiting requires either server configuration or application-level state.
Decision: Not implemented in Version 1. Comment added in code.
Reason: Acceptable for initial scope. Brute-force protection should be added via server configuration (fail2ban, nginx limit_req) before exposure to the public internet.
```

---

## 6. docs/test-log.md

Create or update `docs/test-log.md` with results from the regression test in section 2 of this prompt.

Format:

```markdown
# Test Log

## v1.0.0 — [date]

### Core flow
- [ ] Login and redirect: passed
- [ ] Invalid login: passed (neutral error)
- [ ] Dashboard loads: passed
- [ ] Team create: passed
- [ ] Team edit: passed
- [ ] Player list (empty state): passed
- [ ] Player add (with and without shirt number): passed
- [ ] Player edit: passed
- [ ] Player deactivate: passed
- [ ] Match create (all fields): passed
- [ ] Match edit: passed
- [ ] Match detail / attendance loads: passed
- [ ] Attendance save: passed
- [ ] Attendance persists after navigation: passed
- [ ] Match archive: passed
- [ ] Team archive: passed
- [ ] Logout: passed
- [ ] Back button after logout: passed (protected)
- [ ] Data persists after re-login: passed

### Authorization
- [ ] URL tampering (team): passed (404)
- [ ] URL tampering (player): passed (404)
- [ ] URL tampering (match): passed (404)
- [ ] Cross-user attendance submission: passed (silently ignored)

### Validation
- [ ] Empty team name: passed (field error)
- [ ] Invalid shirt number: passed (field error)
- [ ] Invalid date (Feb 31): passed (field error)
- [ ] Invalid kickoff time: passed (field error)

### Security
- [ ] XSS in team name: passed (escaped)
- [ ] /.env access: passed (denied)
- [ ] /app/config/bootstrap.php access: passed (denied)
- [ ] /storage/logs/app.log access: passed (denied)
- [ ] CSRF bypass: passed (403)

### Known limitations
- No rate limiting on login (see decisions.md)
- No registration UI (users created manually)
- No password reset flow
```

Run the tests, fill in results, and record any failures or known issues honestly.

---

## 7. Production environment guard

In `app/config/bootstrap.php`, verify the environment-based error reporting is correctly configured:

```php
$appEnv = getenv('APP_ENV') ?: 'production';
$appDebug = getenv('APP_DEBUG') === 'true';

if ($appEnv === 'local' && $appDebug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../../storage/logs/app.log');
}
```

This ensures that in production, errors are logged but never shown to users.

---

## 8. Verify storage/logs/ is writable

Confirm that `storage/logs/` exists and contains a `.gitkeep` file.

Confirm that `storage/logs/*.log` is excluded from Git via `.gitignore`.

Trigger a test log entry locally:

```php
// temporary, remove after test
logError('Test log entry for v1.0.0 verification');
```

Check that the entry appears in `storage/logs/app.log`. Then remove the test line.

---

## 9. Final .env.example check

Ensure `.env.example` is complete and matches every `getenv()` call in the codebase:

```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LANG=nl

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=barepitch_local
DB_USER=barepitch_user
DB_PASS=

SESSION_NAME=barepitch_session
```

If any `getenv()` call references a key not present in `.env.example`, add it with a safe placeholder value.

---

## 10. Reproducibility test

Verify the application can be set up from scratch:

```bash
# Drop and recreate the local database
mysql -u root -p -e "DROP DATABASE IF EXISTS barepitch_test; CREATE DATABASE barepitch_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run all migrations in order
mysql -u root -p barepitch_test < database/migrations/001_create_users_table.sql
mysql -u root -p barepitch_test < database/migrations/002_create_teams_table.sql
mysql -u root -p barepitch_test < database/migrations/003_create_players_table.sql
mysql -u root -p barepitch_test < database/migrations/004_create_matches_table.sql
mysql -u root -p barepitch_test < database/migrations/005_create_match_players_table.sql

# Point .env at the new database, start server, confirm login works
```

If this fails for any reason, fix the migration files before proceeding.

Drop the test database afterward:
```bash
mysql -u root -p -e "DROP DATABASE barepitch_test;"
```

---

## 11. Tag and release

When all steps above are complete and the test log shows no critical failures:

```bash
# Final commit
git add .
git commit -m "docs: complete v1.0.0 documentation, test log, and release preparation"

# Merge to main
git checkout main
git merge wip

# Tag
git tag v1.0.0

# Push
git push
git push --tags

# Return to wip
git checkout wip
git merge main

# GitHub release
gh release create v1.0.0 \
  --title "v1.0.0 — First production-ready release" \
  --notes "BarePitch Version 1 is complete. A coach can log in, manage teams, players and matches, and record attendance and player selection. The application is mobile-friendly, secure for its scope, accessible, documented and reproducible from migrations."
```

---

## Final confirmation checklist

**Functionality**
- [ ] Login and logout work
- [ ] Teams: create, edit, archive, list
- [ ] Players: add, edit, deactivate, delete (if no history), list
- [ ] Matches: create, edit, archive, upcoming/past split
- [ ] Attendance: set per player, persist, summary visible
- [ ] Data persists after logout and re-login

**Security**
- [ ] All dynamic output uses e()
- [ ] All queries use prepared statements
- [ ] CSRF on every POST form
- [ ] Ownership checked on GET and POST
- [ ] ID validation on all URL parameters
- [ ] Session is secure (httponly, samesite)
- [ ] Error pages exist for 403, 404, 500
- [ ] Technical errors are never shown to users
- [ ] Security headers present in responses
- [ ] .env is not in Git

**Accessibility and mobile**
- [ ] All form fields have labels
- [ ] Keyboard navigation works through core flows
- [ ] Attendance screen usable at 375px width
- [ ] Touch targets minimum 44px
- [ ] Status indicators use text, not color alone

**Documentation**
- [ ] README reflects current state and setup instructions work
- [ ] CHANGELOG covers all versions
- [ ] decisions.md includes all key decisions
- [ ] test-log.md has results from final test run
- [ ] .env.example is complete and accurate

**Infrastructure**
- [ ] Migrations recreate a working database from scratch
- [ ] storage/logs/ is writable and excluded from Git
- [ ] .htaccess blocks access to non-public directories
- [ ] No debug code, var_dump, or test files in public/

**Release**
- [ ] v1.0.0 tag created on main
- [ ] Tag pushed to remote
- [ ] GitHub release created with summary
