# BarePitch – Security Design

## 1. Purpose

This document defines the security design for BarePitch Version 1.

BarePitch is a small web application, but it still processes personal data related to football teams and players. Some players may be minors. Security must therefore be treated as a basic design requirement, not as a final technical layer.

The goal is simple:

BarePitch must protect user accounts, team data and player information from avoidable exposure, manipulation or misuse.

## 2. Security Principles

BarePitch follows these principles:

Security by default.
Every feature is built with protection from the start.

Least privilege.
Users and database accounts only get the access they need.

Defense in depth.
No single measure is trusted as the only protection.

Server-side trust only.
The browser, URLs, hidden fields and JavaScript are never trusted.

Minimal data.
Data that is not stored cannot be leaked.

Simple structure.
Complexity is a security risk in small projects.

## 3. Main Security Risks

For BarePitch Version 1, the most relevant risks are:

unauthorized access to another user’s team data;
SQL injection;
cross-site scripting;
cross-site request forgery;
weak session handling;
password leakage;
exposing technical errors;
storing unnecessary personal data;
unsafe direct editing on production;
leaking secrets through Git.

The security design must actively reduce these risks.

## 4. Authentication

BarePitch Version 1 uses email and password login.

Required measures:

Passwords are never stored in plain text.
Passwords are hashed using `password_hash()`.
Passwords are verified using `password_verify()`.
Login errors are neutral.
Session ID is regenerated after successful login.
Logout fully destroys the session.

Neutral login message:

```text
Invalid email or password.
```

Do not reveal whether the email address exists.

## 5. Password Handling

Password storage:

```php
$hash = password_hash($password, PASSWORD_DEFAULT);
```

Password verification:

```php
if (password_verify($password, $user['password_hash'])) {
    // login successful
}
```

Rules:

Never log passwords.
Never email passwords.
Never store temporary passwords in plain text.
Never expose password hashes in error output.
Do not invent a custom hashing method.

For Version 1, password complexity should be reasonable but not absurd. A minimum length is more useful than forcing obscure combinations.

Recommended minimum:

```text
at least 10 characters
```

## 6. Session Security

Sessions must be configured before `session_start()`.

Recommended settings:

```php
session_name('barepitch_session');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();
```

In local development, `secure` may be false if HTTPS is not available. In production it must be true.

After login:

```php
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['last_activity'] = time();
```

Session rules:

Store only `user_id` and minimal session metadata.
Do not store full user records.
Regenerate session ID after login.
Destroy session on logout.
Use inactivity timeout.

Recommended timeout:

```text
2 hours of inactivity
```

## 7. Authorization

Authentication answers:

Who is this user?

Authorization answers:

What is this user allowed to access?

BarePitch Version 1 uses ownership-based authorization.

Rules:

A user owns teams.
Teams contain players and matches.
Matches contain match player records.
A user may only access data through teams they own.

Unsafe query:

```sql
SELECT * FROM matches WHERE id = :match_id;
```

Safer query:

```sql
SELECT m.*
FROM matches m
JOIN teams t ON t.id = m.team_id
WHERE m.id = :match_id
  AND t.user_id = :user_id
  AND m.deleted_at IS NULL
  AND t.deleted_at IS NULL;
```

Every page and every save action must check ownership server-side.

Hidden form fields are not security.

JavaScript is not security.

## 8. SQL Injection Protection

All database queries must use prepared statements with PDO.

Correct:

```php
$stmt = $pdo->prepare(
    'SELECT * FROM users WHERE email = :email'
);

$stmt->execute([
    'email' => $email,
]);
```

Incorrect:

```php
$sql = "SELECT * FROM users WHERE email = '$email'";
```

Rules:

Never concatenate user input into SQL.
Validate IDs before use.
Use placeholders for values.
Do not dynamically build SQL unless strictly controlled.
If sorting is dynamic, whitelist allowed column names.

Example whitelist:

```php
$allowedSorts = ['display_name', 'shirt_number'];
$sort = in_array($sort, $allowedSorts, true) ? $sort : 'display_name';
```

## 9. Cross-Site Scripting Protection

All dynamic output must be escaped in views.

Use one helper:

```php
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
```

Use it everywhere output comes from the database or user input:

```php
<?= e($player['display_name']) ?>
```

Risk fields:

team names;
player names;
opponent names;
season labels;
location names.

Never assume stored data is safe.

Avoid rendering raw HTML from user input. BarePitch Version 1 should not allow rich text fields.

## 10. CSRF Protection

Every state-changing request must include a CSRF token.

State-changing actions include:

create team;
edit team;
archive team;
create player;
edit player;
set player inactive;
create match;
edit match;
save match player status;
logout, if done by POST.

Basic token creation:

```php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
```

In the form:

```php
<input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
```

Validation:

```php
if (
    empty($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('Invalid request.');
}
```

Use `hash_equals()` to avoid timing attacks.

## 11. Input Validation

All input must be validated server-side.

Client-side validation is only for user convenience.

Validation rules:

Email must be a valid email address.
Text fields must be trimmed.
Required fields cannot be empty.
Maximum lengths must match database limits.
Dates must be valid dates.
IDs must be numeric.
IDs must be checked against ownership.
Status values must be whitelisted.

Example status whitelist:

```php
$allowedStatuses = [
    'unknown',
    'available',
    'unavailable',
    'selected',
];

if (!in_array($status, $allowedStatuses, true)) {
    throw new InvalidArgumentException('Invalid status.');
}
```

## 12. Safe Error Handling

Users should never see technical errors.

Development:

technical errors may be visible locally.

Production:

technical errors are logged;
users see a generic message;
SQL errors are never shown;
file paths are never shown;
stack traces are never shown.

Production setting:

```text
APP_DEBUG=false
```

Generic message:

```text
Something went wrong. Please try again.
```

Log detail internally, but avoid logging unnecessary personal data.

## 13. Logging

Logging should help detect errors and suspicious behavior without becoming hidden personal data storage.

Log:

unexpected application errors;
failed login attempts in limited form;
authorization failures;
important state-changing failures.

Do not log:

passwords;
password hashes;
session IDs;
full POST payloads;
personal notes;
unnecessary player data.

Example log entry:

```text
[2026-04-24 14:32:10] Authorization failed for user_id=4 on match_id=91
```

This is useful without exposing more than needed.

## 14. Secrets Management

Secrets must never be committed to Git.

Secrets include:

database passwords;
mail credentials;
API keys;
session secrets;
production URLs if sensitive.

Use:

```text
.env
```

Commit only:

```text
.env.example
```

`.gitignore` must include:

```gitignore
.env
*.log
/storage/logs/*
/storage/uploads/*
```

If a secret is accidentally committed, treat it as leaked. Replace it immediately.

## 15. Database Security

Use a dedicated database user for BarePitch.

Do not use the MySQL root user in production.

Recommended permissions:

```text
SELECT
INSERT
UPDATE
DELETE
```

Only grant schema-changing permissions if migrations are run from the application user. Prefer a separate migration process if possible.

Database rules:

Use foreign keys.
Use prepared statements.
Use least privilege.
Make backups.
Test restore.
Do not store unnecessary personal data.
Do not store production exports in the repository.

## 16. Production Configuration

Production must be stricter than local development.

Required production settings:

```text
APP_ENV=production
APP_DEBUG=false
HTTPS enabled
secure cookies enabled
directory listing disabled
public web root set to /public
```

The server must not expose:

`.env` files;
`/app`;
`/database`;
`/storage`;
Git metadata;
log files.

Only `/public` should be web-accessible.

## 17. HTTPS

BarePitch must run over HTTPS in production.

Reasons:

protect login credentials;
protect session cookies;
protect personal data in transit;
enable secure cookies.

Without HTTPS, the application is not production-ready.

## 18. Security Headers

Recommended production headers:

```text
Strict-Transport-Security
X-Content-Type-Options
X-Frame-Options
Referrer-Policy
Permissions-Policy
Content-Security-Policy
```

A practical starting point:

```text
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
```

For Content Security Policy, start carefully and tighten gradually.

Avoid inline JavaScript where possible.

## 19. File Uploads

BarePitch Version 1 must not support file uploads.

This is a deliberate security decision.

Uploads introduce risks:

malware;
privacy leakage;
storage growth;
incorrect public access;
complex validation;
backup complications.

If uploads are added later, they require a separate security design.

## 20. Front-End Security

JavaScript may improve usability, but must not enforce security.

Rules:

Do not trust client-side validation.
Do not store secrets in JavaScript.
Do not expose unnecessary IDs or data.
Do not rely on hidden fields for authorization.
Escape output rendered dynamically into the DOM.

If JavaScript inserts user-controlled data, use text-based insertion rather than raw HTML.

Prefer:

```js
element.textContent = playerName;
```

Avoid:

```js
element.innerHTML = playerName;
```

## 21. Privacy and Security Boundary

Security and privacy overlap.

BarePitch reduces risk by not storing:

player addresses;
phone numbers;
birth dates;
medical information;
photos;
free-text behavioral notes.

This is one of the strongest security measures.

The safest sensitive field is the field that does not exist.

## 22. Backup Security

Backups contain personal data and must be protected.

Backup rules:

store backups outside the public web root;
restrict access;
encrypt where possible;
define retention;
test restore;
delete old backups;
do not email database dumps.

A backup without access control is a data leak waiting to happen.

## 23. Deployment Security

Never edit production files manually as a normal workflow.

Safe deployment flow:

develop locally;
test locally;
commit changes;
push to repository;
deploy from known version;
run migrations;
verify core flows;
check logs.

Production should always correspond to a Git commit or tag.

This makes incidents traceable.

## 24. Security Testing Checklist

Before a feature is accepted, test:

Can an unauthenticated user access the page?
Can another user access the data by changing an ID?
Does every POST action have CSRF protection?
Are all database queries prepared?
Is all user output escaped?
Are invalid status values rejected?
Are errors handled safely?
Does logout really end the session?
Does the feature work with JavaScript disabled where possible?
Are no secrets included in Git?

Basic manual test inputs:

```html
<script>alert('xss')</script>
```

```sql
' OR '1'='1
```

```text
../../.env
```

These should not break or expose the application.

## 25. Incident Response

Even small projects need a basic incident plan.

If something goes wrong:

identify what happened;
take the application offline if needed;
preserve relevant logs;
change exposed passwords or secrets;
restore from clean backup if needed;
check whether personal data was exposed;
document the incident;
assess whether a data breach notification is required.

For Dutch and EU contexts, possible data breaches must be assessed seriously under the GDPR.

## 26. Security Definition of Done

A feature is security-ready when:

authentication is required where needed;
ownership is checked server-side;
all SQL uses prepared statements;
all dynamic output is escaped;
all POST actions have CSRF protection;
input is validated server-side;
errors are not exposed to users;
no secrets are committed;
no unnecessary personal data is stored;
the feature works under production-like settings.

## 27. Version 1 Security Scope

Included in Version 1:

secure login;
password hashing;
session protection;
ownership checks;
CSRF protection;
prepared statements;
output escaping;
safe error handling;
minimal logging;
HTTPS in production;
basic security headers.

Excluded from Version 1:

multi-factor authentication;
role hierarchy;
single sign-on;
file uploads;
public sharing links;
advanced audit logs;
external integrations.

These exclusions are intentional. BarePitch should first become small, safe and understandable.

## 28. Guiding Security Question

Every security decision should be tested against this question:

Could this expose, alter or misuse team or player data if someone behaves unexpectedly?

If the answer is yes, the design must be tightened before the feature is considered done.
