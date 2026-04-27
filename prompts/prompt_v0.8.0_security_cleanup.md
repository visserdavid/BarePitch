# Prompt: Build v0.8.0 — Security Cleanup

Follow all conventions in CLAUDE.md.

Create a checkpoint commit before making any changes:

```bash
git add .
git commit -m "checkpoint: before building v0.8.0 security cleanup"
```

---

## Context

BarePitch is a minimal PHP/MySQL application for football coaches. The responsive cleanup (v0.7.0) is complete. This prompt implements v0.8.0: a systematic security audit and hardening pass before the v1.0.0 production release.

This version adds no new features. It only strengthens what exists.

The guiding question for every change:

> Could this expose, alter or misuse team or player data if someone behaves unexpectedly?

Stack: plain PHP 8.x, MySQL 8 via PDO, HTML, CSS, vanilla JS. No frameworks, no Composer.

All conventions from CLAUDE.md apply throughout.

---

## Scope

This version touches:

- All public PHP files in `public/`
- `app/config/bootstrap.php`
- `app/helpers/`
- `app/models/`
- `app/services/`
- `app/views/`
- `public/.htaccess` (new)
- `app/views/errors/` (new)
- `app/helpers/logger.php` (new)
- `.gitignore` (verify)
- `.env.example` (verify)

No new user-facing features. No new database migrations.

---

## 1. Output escaping audit

Scan every view file in `app/views/` for dynamic output.

**Rule:** every value that originates from the database, from `$_GET`, `$_POST`, or from user-controlled session data must be wrapped in `e()`.

Check these risk fields specifically:
- team name, season
- player display_name, shirt_number
- match opponent_name, location
- user display_name
- any value rendered into an HTML attribute (e.g. `value="..."`, `href="..."`)

**Fix:** replace any bare `<?= $value ?>` or `<?php echo $value ?>` with `<?= e($value) ?>` where the value is user-controlled.

**Confirm:** the `e()` helper function exists in `app/helpers/view.php` or a dedicated helper:

```php
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
```

If missing, add it and register it in bootstrap.

---

## 2. Prepared statements audit

Scan every model file in `app/models/` and every service file in `app/services/` for database queries.

**Rule:** every query that uses a variable value must use a prepared statement with bound parameters. No exceptions.

Check for:
- string concatenation in SQL: `"SELECT ... WHERE id = " . $id` — this is a critical vulnerability
- unbound variables passed directly to `query()` instead of `prepare()` + `execute()`

**Fix:** convert any unsafe query to a prepared statement.

Correct pattern:
```php
$stmt = getPdo()->prepare("SELECT * FROM teams WHERE id = :id AND user_id = :user_id");
$stmt->execute([':id' => $id, ':user_id' => $userId]);
```

---

## 3. CSRF audit

Scan every public PHP file in `public/` that handles POST requests.

**Rule:** every POST handler must call `validateCsrf()` before processing any data. Every form must include `<?= csrfField() ?>`.

Create a checklist internally and verify each of the following:

| File | Has CSRF form field | Validates CSRF on POST |
|---|---|---|
| public/login.php | | |
| public/logout.php | | |
| public/team_create.php | | |
| public/team_edit.php (update) | | |
| public/team_edit.php (archive) | | |
| public/player_create.php | | |
| public/player_edit.php (update) | | |
| public/player_edit.php (deactivate) | | |
| public/player_edit.php (delete) | | |
| public/match_create.php | | |
| public/match_edit.php (update) | | |
| public/match_edit.php (archive) | | |
| public/match.php (attendance POST) | | |

Fix any gap found. The `validateCsrf()` call must happen before any other processing on POST.

Also verify that `validateCsrf()` uses `hash_equals()` to prevent timing attacks:

```php
function validateCsrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    return $sessionToken !== '' && hash_equals($sessionToken, $token);
}
```

If the current implementation uses `===` instead of `hash_equals()`, update it.

---

## 4. Ownership check audit

Scan every public PHP file that loads a resource by ID.

**Rule:** every resource must be loaded using an ownership-aware model method that includes the user_id in the WHERE clause (directly or via JOIN). Loading by ID alone is never acceptable.

Verify these patterns:

| Resource | Correct pattern |
|---|---|
| Team | `TeamModel::findForUser($teamId, $userId)` |
| Player | `PlayerModel::findForTeam($playerId, $teamId, $userId)` |
| Match | `MatchModel::findForTeam($matchId, $teamId, $userId)` |
| Attendance save | player IDs verified against team in `AttendanceModel::saveStatuses()` |

For each public file that does a POST (update, archive, delete, save attendance), confirm that the ownership check is performed **again on POST**, not only on GET. A user may submit a POST request directly without loading the GET page first.

If any public file loads a resource without ownership verification, fix it.

---

## 5. ID validation audit

Scan every public PHP file for URL parameter and POST field reading.

**Rule:** every ID read from `$_GET` or `$_POST` must be validated as a positive integer before use.

Correct pattern:
```php
$teamId = $_GET['team_id'] ?? null;
if (!$teamId || !ctype_digit((string)$teamId) || (int)$teamId <= 0) {
    http_response_code(404);
    render('errors/404');
    exit;
}
$teamId = (int)$teamId;
```

Fix any file that uses a URL parameter without this check.

---

## 6. Error handling and error pages

### Create error views

Create the following view files:

**`app/views/errors/403.php`**
```
h1: Access denied
Message: You do not have access to this page.
Link: Return to dashboard (if logged in) or login page
```

**`app/views/errors/404.php`**
```
h1: Page not found
Message: This page could not be found.
Link: Return to dashboard (if logged in) or login page
```

**`app/views/errors/500.php`**
```
h1: Something went wrong
Message: An unexpected error occurred. Please try again.
Link: Return to dashboard (if logged in) or login page
```

All error views must include `layouts/header.php` and `layouts/footer.php`. All output must be escaped.

### Set up a global error handler

In `app/config/bootstrap.php`, after the environment check, add:

```php
set_exception_handler(function (Throwable $e) {
    logError('Uncaught exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    if (getenv('APP_ENV') === 'local') {
        throw $e; // re-throw in development for full stack trace
    }
    http_response_code(500);
    render('errors/500');
    exit;
});

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    logError("PHP Error [{$errno}]: {$errstr} in {$errfile}:{$errline}");
    return true;
});
```

---

## 7. Logging helper

Create `app/helpers/logger.php` with a minimal logging function:

```php
function logError(string $message): void
{
    $logFile = __DIR__ . '/../../storage/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[{$timestamp}] ERROR: {$message}" . PHP_EOL;
    error_log($entry, 3, $logFile);
}

function logSecurity(string $message): void
{
    $logFile = __DIR__ . '/../../storage/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[{$timestamp}] SECURITY: {$message}" . PHP_EOL;
    error_log($entry, 3, $logFile);
}
```

Register in `app/config/bootstrap.php`:
```php
require __DIR__ . '/../helpers/logger.php';
```

**Log the following events** (update affected files):
- CSRF validation failure: `logSecurity("CSRF failure for user_id={$userId} on " . $_SERVER['REQUEST_URI'])`
- Authorization failure (ownership mismatch): `logSecurity("Ownership check failed for user_id={$userId}")`
- Failed login attempt: `logSecurity("Failed login attempt for email=" . substr($email, 0, 3) . "***")` (do not log full email)

**Never log:**
- Passwords or password hashes
- Full POST payloads
- Session IDs
- Complete email addresses
- Player names or personal data beyond IDs

---

## 8. Session security audit

Review `app/config/bootstrap.php` session configuration.

Ensure session is configured **before** `session_start()`:

```php
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => getenv('APP_ENV') !== 'local',
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_name(getenv('SESSION_NAME') ?: 'barepitch_session');
session_start();
```

Verify that `AuthService::login()` calls `session_regenerate_id(true)` immediately after successful authentication.

Verify that `AuthService::logout()` fully destroys the session:
```php
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();
```

---

## 9. .htaccess for Apache

Create `public/.htaccess`:

```apache
# Disable directory listing
Options -Indexes

# Disable server signature
ServerSignature Off

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "DENY"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Permissions-Policy "camera=(), microphone=(), geolocation=()"
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; font-src 'self'; frame-ancestors 'none';"
</IfModule>

# Block access to sensitive files if they end up in public accidentally
<FilesMatch "\.(env|log|sql|md|gitignore)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# PHP error display off (belt and suspenders)
<IfModule mod_php.c>
    php_flag display_errors off
</IfModule>
```

Also create `app/.htaccess`, `database/.htaccess`, and `storage/.htaccess` with:

```apache
Order allow,deny
Deny from all
```

These files block direct web access to non-public directories on Apache. Document in README that Nginx users must configure equivalent rules in their server block.

---

## 10. .gitignore and .env.example audit

Verify `.gitignore` includes at minimum:

```gitignore
.env
*.log
/storage/logs/*
/storage/uploads/*
/vendor/
```

If missing entries, add them.

Verify `.env.example` is complete and accurate — it must document every key used in bootstrap and throughout the application:

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

Ensure `.env` is not tracked by Git. If it is, remove it from tracking:
```bash
git rm --cached .env
```

---

## 11. Secrets in Git audit

Run:
```bash
git log --all --full-history -- .env
git grep -r "password" -- "*.php" | grep -v "password_hash\|password_verify\|password_needs_rehash"
git grep -r "DB_PASS=" -- "*.php"
```

If any hardcoded credentials or secrets appear in tracked files, remove them and rotate the affected credentials immediately.

---

## 12. Security headers in PHP (fallback)

In `app/config/bootstrap.php`, after session start, add PHP-level security headers as a fallback for environments where `.htaccess` is not processed (e.g. Nginx, PHP built-in server):

```php
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}
```

Do not set Content-Security-Policy in PHP here — it is harder to maintain inline and easier to configure per environment at the server level.

---

## 13. Login brute-force awareness

BarePitch Version 1 does not implement rate limiting (too complex for scope). However, add a comment in `public/login.php` and in `AuthService.php` to make the gap visible:

```php
// Known limitation: no rate limiting on login attempts.
// Consider adding brute-force protection before exposing to the public internet.
// Options: server-level rate limiting (fail2ban, nginx limit_req), or application-level delay.
```

Also ensure failed login attempts are logged via `logSecurity()` (see section 7).

---

## 14. Final security checklist

After all changes, verify each item manually:

**Output escaping**
- [ ] Every dynamic value in views uses `e()`
- [ ] No bare `<?= $value ?>` on user-controlled data

**Prepared statements**
- [ ] Every model method uses prepare() + execute()
- [ ] No SQL string concatenation with variables

**CSRF**
- [ ] Every POST form includes `<?= csrfField() ?>`
- [ ] Every POST handler calls `validateCsrf()` first
- [ ] `validateCsrf()` uses `hash_equals()`

**Ownership**
- [ ] Every GET resource load uses an ownership-aware model method
- [ ] Every POST action repeats ownership check independently

**ID validation**
- [ ] Every URL/POST ID is validated as a positive integer before use

**Session**
- [ ] Session cookie is httponly and samesite=Lax
- [ ] `session_regenerate_id(true)` called on login
- [ ] Session fully destroyed on logout

**Error handling**
- [ ] Error views exist for 403, 404, 500
- [ ] Global exception handler is registered
- [ ] Technical errors are never shown to users in production

**Logging**
- [ ] `logError()` and `logSecurity()` helpers exist
- [ ] CSRF failures are logged
- [ ] Authorization failures are logged
- [ ] Failed logins are logged (without full email)
- [ ] No passwords or sensitive data in logs

**File protection**
- [ ] `.htaccess` files block access to non-public directories
- [ ] `.env` is not committed to Git
- [ ] `*.log` files are in `.gitignore`

**Security headers**
- [ ] X-Content-Type-Options set
- [ ] X-Frame-Options set
- [ ] Referrer-Policy set
- [ ] Permissions-Policy set

---

## After completing all changes

1. Test the following attack scenarios manually:

**URL tampering:** change a `team_id` or `id` in the URL to a value owned by another user → expect 404
**CSRF bypass:** submit a form with the csrf_token field removed → expect 403 with generic message
**XSS attempt:** enter `<script>alert('xss')</script>` as a team name, save it, view the team list → expect plain text output, no script execution
**SQL injection:** enter `' OR '1'='1` as a login email → expect login to fail safely
**Direct file access:** visit `/.env`, `/app/config/bootstrap.php`, `/storage/logs/app.log` directly in browser → expect access denied

2. Commit:
```bash
git add .
git commit -m "security: audit and harden output escaping, CSRF, ownership, session, headers, logging"
```

3. When all security checks pass:
```bash
git checkout main
git merge wip
git tag v0.8.0
git push
git push --tags
git checkout wip
git merge main
gh release create v0.8.0 --title "v0.8.0 Security Cleanup" --notes "Full security audit: output escaping, CSRF validation, ownership checks, ID validation, session hardening, security headers, error pages, structured logging, .htaccess protection."
```

4. Confirm:
- [ ] All checklist items above are green
- [ ] All five manual attack scenarios fail safely
- [ ] Error pages render correctly for 403, 404, and 500
- [ ] Logs are written to storage/logs/app.log
- [ ] No sensitive data appears in logs
- [ ] .env is not tracked by Git
- [ ] Security headers are present on responses (verify in browser DevTools → Network → response headers)
