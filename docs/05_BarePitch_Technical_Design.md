# BarePitch – Technical Design

## 1. Purpose

This document defines the technical foundation for BarePitch Version 1.

BarePitch will be built as a small, minimal web application using:

PHP
MySQL
Vanilla JavaScript
HTML
CSS

No frameworks will be used.

The goal is not to imitate a framework, but to create enough structure to keep the application safe, understandable and maintainable.

## 2. Technical Principles

BarePitch follows five technical principles.

First, simplicity. The codebase should stay small and readable.

Second, separation of concerns. Database logic, page logic, templates and configuration should not be mixed randomly.

Third, security by default. Input, output, sessions, passwords and database access must be handled safely from the start.

Fourth, minimal dependencies. External packages are only added when they solve a real problem.

Fifth, recoverability. The application should be easy to reinstall, restore and debug.

## 3. Recommended Project Structure

```text
barepitch/
├── app/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   ├── services/
│   ├── views/
│   └── helpers/
│
├── database/
│   ├── migrations/
│   └── seeds/
│
├── public/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── teams.php
│   ├── team.php
│   ├── players.php
│   ├── matches.php
│   ├── match.php
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/
│
├── storage/
│   ├── logs/
│   └── uploads/
│
├── docs/
│   └── devlog.md
│
├── .env
├── .env.example
├── .gitignore
├── README.md
└── CHANGELOG.md
```

Only the `public/` directory should be directly accessible through the browser.

Everything else must remain outside the public web root.

## 4. Application Entry Model

For Version 1, BarePitch can use a simple page-based structure.

Each public PHP file represents a functional screen:

```text
login.php
dashboard.php
teams.php
team.php
players.php
matches.php
match.php
```

This is simpler than building a custom router too early.

However, each file should remain thin. It should mainly:

load configuration;
check authentication;
call the relevant model or service;
prepare data;
include the view.

The page file should not contain large blocks of SQL, HTML and business logic mixed together.

## 5. Configuration

Configuration should be loaded from environment variables.

Sensitive values must not be committed to Git.

Example `.env.example`:

```text
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_HOST=localhost
DB_NAME=barepitch
DB_USER=root
DB_PASS=

SESSION_NAME=barepitch_session
```

The real `.env` file stays local or on the server.

Recommended rule:

`.env.example` explains what is needed.
`.env` contains the real values.
`.env` is ignored by Git.

## 6. Database Connection

Use PDO for all database access.

The database connection should exist in one central place, for example:

```text
app/config/database.php
```

The connection should:

use UTF-8;
throw exceptions;
disable emulated prepares where appropriate;
return one reusable PDO instance.

Example direction:

```php
$pdo = new PDO(
    $dsn,
    $username,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);
```

All database queries must use prepared statements.

## 7. Models

Models handle database interaction.

Recommended models for Version 1:

```text
UserModel.php
TeamModel.php
PlayerModel.php
MatchModel.php
AttendanceModel.php
```

A model may contain methods such as:

```php
findUserByEmail($email)
findTeamsByUserId($userId)
findTeamByIdAndUserId($teamId, $userId)
createTeam($userId, $name, $season)
createPlayer($teamId, $name)
findPlayersByTeamId($teamId)
createMatch($teamId, $opponent, $matchDate)
saveAttendance($matchId, $playerId, $status)
```

Important rule:

Every query that retrieves user-owned data must include ownership checks directly or indirectly.

For example:

Do not only fetch a team by `team_id`.
Fetch it by `team_id` and `user_id`.

## 8. Services

Services contain logic that does not belong purely to one database table.

Recommended services:

```text
AuthService.php
CsrfService.php
SessionService.php
ValidationService.php
```

Examples:

`AuthService` handles login and logout logic.
`CsrfService` creates and validates form tokens.
`SessionService` configures and protects sessions.
`ValidationService` centralizes recurring validation rules.

This prevents every page from inventing its own logic.

## 9. Views

Views contain HTML templates.

Recommended structure:

```text
app/views/
├── layouts/
│   ├── header.php
│   ├── footer.php
│   └── navigation.php
│
├── auth/
│   └── login.php
│
├── dashboard/
│   └── index.php
│
├── teams/
│   ├── index.php
│   ├── show.php
│   └── form.php
│
├── players/
│   ├── index.php
│   └── form.php
│
└── matches/
    ├── index.php
    ├── show.php
    └── form.php
```

Views should not run database queries.

Views may display data, forms and messages.

All dynamic output must be escaped.

Use a helper such as:

```php
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
```

Then in views:

```php
<?= e($player['name']) ?>
```

## 10. Authentication

Version 1 uses email and password login.

Password requirements:

passwords are never stored directly;
use `password_hash`;
verify with `password_verify`;
use neutral error messages.

Login flow:

User submits email and password.
Server validates input.
User is fetched by email.
Password is verified.
Session ID is regenerated.
User ID is stored in session.
User is redirected to dashboard.

Logout flow:

Session data is cleared.
Session cookie is invalidated.
Session is destroyed.
User is redirected to login.

## 11. Session Security

Sessions must be configured before `session_start()`.

Recommended settings:

`httponly = true`
`secure = true` in production
`samesite = Lax`
custom session name
session regeneration after login
session timeout

Session should store only minimal data:

```text
user_id
last_activity
```

Do not store full user records in the session.

## 12. Authorization

Authorization is based on ownership.

Version 1 does not need complex roles.

Rules:

A user owns teams.
Teams contain players and matches.
Players and matches are only accessible through teams owned by the user.
Attendance belongs to a match owned by the user.

Every read and write action must validate this chain.

Example:

To edit a match, the system must confirm:

match exists;
match belongs to a team;
team belongs to logged-in user.

Never trust IDs from URLs or hidden form fields.

## 13. CSRF Protection

Every state-changing form must include a CSRF token.

State-changing actions include:

create team;
edit team;
archive team;
create player;
edit player;
create match;
edit match;
save attendance;
logout, if implemented as POST.

Basic flow:

Generate token.
Store token in session.
Render token in hidden form field.
Validate token on POST.
Reject request if token is missing or invalid.

## 14. Input Validation

All input must be validated server-side.

Client-side validation may improve user experience, but cannot be trusted.

Validation rules:

email must be valid email format;
text fields must be trimmed;
required fields may not be empty;
maximum lengths must be enforced;
dates must be valid;
IDs must be numeric and checked against ownership;
attendance status must be one of the allowed values.

Allowed attendance statuses for Version 1:

```text
unknown
available
unavailable
selected
```

## 15. Output Escaping

All user-controlled output must be escaped before rendering in HTML.

Examples of user-controlled output:

team names;
player names;
opponent names;
season labels.

Escaping must happen at the point of output.

Do not assume stored data is safe.

## 16. Error Handling

The user should never see technical errors.

In development:

errors may be shown locally if `APP_DEBUG=true`.

In production:

errors are logged;
generic messages are shown;
SQL errors are never exposed;
file paths are never exposed.

Recommended generic message:

“Something went wrong. Please try again.”

Logs should be written to:

```text
storage/logs/app.log
```

Logs must not contain unnecessary personal data.

## 17. Database Design Direction

Version 1 likely needs these tables:

```text
users
teams
players
matches
attendance
```

Optional technical tables:

```text
password_resets
audit_logs
```

For Version 1, avoid overbuilding.

Core relationships:

One user has many teams.
One team has many players.
One team has many matches.
One match has many attendance records.
One player can have many attendance records.

## 18. Database Migrations

Even without a framework, schema changes should be versioned.

Use plain SQL files:

```text
database/migrations/
├── 001_create_users_table.sql
├── 002_create_teams_table.sql
├── 003_create_players_table.sql
├── 004_create_matches_table.sql
└── 005_create_attendance_table.sql
```

Every migration file should be committed to Git.

Never change production database structure manually without recording it.

## 19. Front-End Structure

HTML should be semantic and simple.

CSS should be organized around reusable layout and component patterns.

Recommended CSS files:

```text
public/assets/css/base.css
public/assets/css/layout.css
public/assets/css/components.css
public/assets/css/forms.css
public/assets/css/pages.css
```

JavaScript should be minimal.

Recommended JavaScript files:

```text
public/assets/js/app.js
public/assets/js/forms.js
public/assets/js/match.js
```

JavaScript should enhance usability, not carry core business logic.

The application must remain functionally reliable even if JavaScript fails, wherever possible.

## 20. Responsive Design

BarePitch should be usable on mobile screens.

This matters because coaches may use it near training or match situations.

Design priorities:

large tap targets;
clear lists;
minimal navigation;
simple forms;
no dense dashboards;
no unnecessary modals.

Mobile first is recommended.

## 21. Accessibility

Technical accessibility expectations:

valid HTML;
labels connected to form fields;
buttons for actions;
links for navigation;
visible focus state;
sufficient contrast;
keyboard navigation;
error messages linked to fields where possible.

Do not communicate status only through color.

## 22. Security Headers

In production, configure security headers at server level where possible.

Recommended headers:

```text
Content-Security-Policy
X-Frame-Options
X-Content-Type-Options
Referrer-Policy
Permissions-Policy
Strict-Transport-Security
```

A strict Content Security Policy may be introduced gradually.

Avoid inline JavaScript where possible, because it makes stronger policies harder later.

## 23. File Uploads

Version 1 should not support uploads.

This is intentional.

Uploads introduce:

storage complexity;
privacy risk;
malware risk;
access-control complexity;
backup growth.

If uploads are ever added later, they need a separate design.

## 24. Logging

Logging should support debugging and security without becoming hidden data storage.

Log:

unexpected errors;
failed important operations;
security-relevant events;
login failures in limited form.

Do not log:

passwords;
full POST payloads;
unnecessary personal data;
session IDs.

## 25. Backups

Production must have a backup strategy before real use.

Minimum:

daily database backup;
secure storage;
limited access;
retention period;
tested restore process.

A backup that has never been restored is only an assumption.

## 26. Deployment

BarePitch should be deployed from Git, not edited directly on the server.

Deployment steps:

pull or upload tested code;
install dependencies if any;
update configuration;
run database migrations;
check file permissions;
verify login;
verify core flow;
check logs.

Production settings:

```text
APP_ENV=production
APP_DEBUG=false
HTTPS enabled
secure cookies enabled
```

## 27. Version Control

Recommended solo workflow:

```text
main      stable version
wip       daily development
spike/*   experiments
```

Rules:

`main` must always work.
`wip` may contain active development.
Experiments happen in `spike/*`.
Stable milestones receive tags.

Example tags:

```text
v0.1.0 project structure
v0.2.0 authentication
v0.3.0 teams and players
v0.4.0 matches
v0.5.0 attendance
v1.0.0 first production-ready release
```

## 28. Technical Definition of Done

A feature is technically done when:

server-side validation exists;
database operations use prepared statements;
output is escaped;
ownership is checked;
CSRF protection is present for POST actions;
errors are handled cleanly;
the feature works after logout and login;
mobile layout is acceptable;
no sensitive data is exposed;
the change is committed to Git.

## 29. Technical Risks

Main risks:

mixing logic, SQL and HTML;
adding too many features too early;
forgetting ownership checks;
trusting hidden form fields;
storing unnecessary personal data;
using JavaScript as a security layer;
editing production manually;
not testing restore from backup.

These risks are manageable if the structure stays simple and disciplined.

## 30. Guiding Technical Question

Every technical choice should be tested against this question:

Does this make BarePitch safer, clearer or easier to maintain?

If not, it probably does not belong in Version 1.
