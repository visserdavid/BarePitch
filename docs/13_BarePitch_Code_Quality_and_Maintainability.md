# BarePitch – Code Quality and Maintainability

## 1. Purpose

This document defines how BarePitch should keep its codebase clean, understandable and maintainable.

BarePitch is a small application, but small projects often become difficult to maintain when early shortcuts remain hidden in the code. The goal is not perfection. The goal is controlled simplicity.

Code quality in BarePitch means:

the code is readable;
the structure is predictable;
security rules are applied consistently;
features can be changed without fear;
the application can still be understood months later.

## 2. Core Principle

BarePitch should stay simple without becoming sloppy.

No frameworks are used, so the project must create its own discipline through clear file structure, naming, validation, access checks and documentation.

The guiding rule is:

If the code is easy to write but hard to understand later, it is not good code.

## 3. Separation of Concerns

BarePitch should separate different responsibilities.

Public files may coordinate a request.

Models handle database access.

Services handle reusable application logic.

Views render HTML.

Helpers contain small reusable functions.

Configuration belongs in config files and environment variables.

Avoid this pattern:

SQL, validation, session checks and HTML all mixed in one large PHP file.

Prefer this pattern:

```text
public/team.php
app/models/TeamModel.php
app/views/teams/show.php
app/helpers/view.php
```

Each file should have a clear reason to exist.

## 4. Public PHP Files

Public PHP files should remain thin.

They may:

load bootstrap;
require login;
read request parameters;
call services or models;
prepare data for the view;
redirect after successful POST;
include a view.

They should not contain:

large SQL queries;
large HTML blocks;
repeated validation logic;
complex business rules;
direct access-control shortcuts.

A public file is a coordinator, not the whole application.

## 5. Models

Models should contain database logic.

Examples:

```text
UserModel.php
TeamModel.php
PlayerModel.php
MatchModel.php
AttendanceModel.php
```

Model methods should be specific and safe.

Good:

```php
findTeamForUser(int $teamId, int $userId)
findMatchesForTeam(int $teamId)
createPlayer(int $teamId, array $data)
```

Risky:

```php
findById($id)
query($sql)
save($data)
```

Generic methods can become dangerous when ownership checks are forgotten.

## 6. Services

Services should contain reusable logic that does not belong to one database table.

Useful services:

```text
AuthService.php
CsrfService.php
ValidationService.php
FlashService.php
SessionService.php
```

Examples:

Authentication belongs in `AuthService`.

CSRF creation and validation belong in `CsrfService`.

Flash messages belong in `FlashService`.

This prevents repeated logic across pages.

## 7. Views

Views should render data.

Views may contain:

HTML;
escaped output;
simple display conditions;
loops over prepared data;
form fields;
error messages.

Views should not contain:

database queries;
permission decisions;
raw request handling;
complex business logic.

All dynamic output must be escaped.

Example:

```php
<?= e($player['display_name']) ?>
```

Do not rely on remembering which fields are safe. Escape by default.

## 8. Naming Conventions

Use consistent names.

Recommended PHP naming:

```text
classes: PascalCase
functions: camelCase
variables: camelCase
files: PascalCase.php for classes
```

Examples:

```php
class TeamModel
function findTeamForUser()
$currentUserId
```

Recommended database naming:

```text
tables: plural snake_case
columns: snake_case
foreign keys: entity_id
```

Examples:

```text
users
teams
match_players
user_id
team_id
created_at
```

Consistency matters more than personal preference.

## 9. Function Size

Functions should be small enough to understand quickly.

A good function does one thing.

If a function:

validates input;
runs queries;
checks permissions;
renders HTML;
redirects;

then it is probably doing too much.

Split it.

Not because rules demand it, but because future changes become safer.

## 10. Avoid Repetition

Repeated code is a warning sign.

Common duplication risks:

database connection code;
login checks;
CSRF validation;
form validation;
escaping;
flash messages;
ownership checks.

If the same logic appears in three places, create a helper, service or model method.

This is especially important for security logic. Repeated security code often becomes inconsistent.

## 11. Validation Consistency

Validation should not be improvised per page.

Create shared validation helpers or a small validation service.

Examples:

```php
required($value)
maxLength($value, 100)
validEmail($value)
validDate($value)
inAllowedValues($value, $allowed)
```

This keeps behavior predictable.

A team name should not be validated one way on create and another way on edit.

## 12. Access-Control Consistency

Access control must be centralized as much as possible.

Use ownership-aware model methods.

Good:

```php
$team = $teamModel->findTeamForUser($teamId, $currentUserId);
```

Risky:

```php
$team = $teamModel->findTeamById($teamId);
```

The method name should remind you whether access control is included.

This is a simple but powerful maintainability habit.

## 13. Error Handling Consistency

Use a predictable pattern for errors.

Validation errors:

return user to the form;
show field-specific messages;
preserve safe input.

Authorization errors:

return 404 or 403;
do not expose unnecessary details.

Unexpected errors:

log internally;
show generic message.

Do not invent a different error style per page.

## 14. Comments

Comments should explain why, not repeat what.

Weak comment:

```php
// Set team name
$teamName = trim($_POST['team_name']);
```

Useful comment:

```php
// Archived teams are hidden by default, but kept to preserve match history.
```

Most code should be readable through names and structure.

Use comments where the reason behind a decision may not be obvious later.

## 15. Documentation in the Codebase

BarePitch should include minimal but useful documentation.

Recommended files:

```text
README.md
CHANGELOG.md
docs/devlog.md
docs/decisions.md
docs/test-log.md
```

Purpose:

`README.md` explains setup and usage.

`CHANGELOG.md` records versions.

`devlog.md` captures development notes.

`decisions.md` explains important choices.

`test-log.md` records what has been tested.

Documentation should support memory, not become bureaucracy.

## 16. Technical Decisions

Important decisions should be written down.

Examples:

why no framework is used;
why player data is minimal;
why uploads are excluded;
why `match_players` stores attendance status;
why players are marked inactive instead of deleted.

Use a simple format:

```markdown
## Decision: No file uploads in Version 1

Context:
Uploads increase storage, privacy and security complexity.

Decision:
BarePitch Version 1 does not support uploads.

Reason:
The core match preparation flow does not need files.
```

This helps prevent old decisions from being reopened unnecessarily.

## 17. Security as Code Quality

In BarePitch, insecure code is low-quality code, even if it works.

A feature is not clean if:

SQL is built through string concatenation;
output is not escaped;
POST actions lack CSRF protection;
ownership is not checked;
passwords are handled carelessly;
errors reveal internals.

Security should not be a separate cleanup phase. It is part of the quality standard.

## 18. Managing Technical Debt

Technical debt is acceptable when it is conscious.

Bad technical debt:

unknown shortcuts;
messy files no one wants to touch;
security gaps hidden behind “later”;
duplicated logic everywhere.

Acceptable technical debt:

a known simplification;
documented in `devlog.md` or `decisions.md`;
not security-critical;
scheduled for later cleanup.

Use notes like:

```text
Known debt:
Attendance form works, but status rendering should be moved to a reusable view partial.
```

Make debt visible. Invisible debt becomes rot.

## 19. Refactoring

Refactoring means improving structure without changing behavior.

Refactor when:

a file becomes too large;
logic is duplicated;
a function has too many responsibilities;
security checks are repeated manually;
a feature works but is hard to understand;
future changes feel risky.

Before refactoring:

make a checkpoint commit.

After refactoring:

test the affected flow.

Do not refactor and add new features in the same commit unless the change is very small.

## 20. File Size Awareness

There is no strict rule, but size is a signal.

If a PHP file becomes hard to scan, split it.

Possible signs:

more than one major responsibility;
many nested conditionals;
mixed HTML and database logic;
long repeated validation blocks;
unclear top-to-bottom flow.

Small files are not automatically good. Clear files are.

## 21. Dependency Management

BarePitch avoids unnecessary dependencies.

A dependency may be added only when:

it solves a real problem;
it is maintained;
it is understandable;
it does not pull in excessive complexity;
it does not conflict with the no-framework direction.

Composer may still be used for autoloading or small utilities.

Do not add a package to avoid understanding a basic problem.

## 22. CSS Maintainability

CSS should remain structured.

Recommended files:

```text
base.css
layout.css
components.css
forms.css
pages.css
```

Rules:

use reusable classes;
avoid page-specific exceptions where possible;
keep spacing consistent;
avoid deeply nested selectors;
do not style by random IDs unless necessary;
do not rely on fragile source order.

A small design system is enough:

buttons;
cards;
forms;
alerts;
lists;
navigation.

BarePitch should look calm because the CSS is consistent, not because every screen is individually tuned.

## 23. JavaScript Maintainability

JavaScript should stay modest in Version 1.

Rules:

avoid large global scripts;
keep page-specific behavior isolated;
use clear function names;
do not duplicate server-side business logic;
do not use JavaScript for security;
prefer `textContent` over `innerHTML`.

If JavaScript becomes central to a feature, ask whether the design is becoming too complex for Version 1.

## 24. Database Maintainability

Database changes must be traceable.

Use migrations:

```text
001_create_users_table.sql
002_create_teams_table.sql
003_create_players_table.sql
004_create_matches_table.sql
005_create_match_players_table.sql
```

Rules:

do not manually change production without a migration;
do not rename columns casually;
do not store unnecessary personal data;
use foreign keys;
keep indexes intentional;
document non-obvious schema choices.

The database should reflect the product’s simplicity.

## 25. Git and Maintainability

Commit history should help you understand the project later.

Use meaningful commits:

```text
feat: add player creation flow
fix: prevent match access across users
refactor: move team queries into model
security: add csrf validation to match forms
```

Avoid:

```text
update
stuff
changes
final
```

During vibe coding, WIP commits are fine. Before merging to `main`, the code should be coherent.

## 26. Code Review for Solo Development

Even when working alone, review your own changes.

Before merging `wip` to `main`, check:

What changed?
Why did it change?
Is anything duplicated?
Are security checks present?
Are names clear?
Is the database still coherent?
Does the interface still match the concept?

Use `git diff` as your review tool.

The point is not formality. The point is not fooling yourself.

## 27. Maintainability Checklist per Feature

A feature is maintainable when:

the public file is thin;
database logic is in a model;
repeated logic is extracted;
validation is consistent;
access control is explicit;
output is escaped;
error handling matches the project pattern;
CSS uses existing components where possible;
JavaScript is limited and understandable;
the feature is documented if it introduces a new concept.

## 28. Signs the Codebase Is Drifting

Watch for these warning signs:

you hesitate to change a file;
you do not remember why something works;
one page has a different pattern than the others;
SQL appears in views;
HTML appears inside models;
security checks are copied manually;
CSS needs many exceptions;
new features require changes in too many places;
you avoid testing because you expect something will break.

These are not failures. They are signals to pause and clean up.

## 29. Practical Cleanup Rhythm

For BarePitch, use this rhythm:

Build one feature.
Test the full flow.
Commit.
Clean up obvious duplication.
Commit again.
Document any remaining debt.
Move on.

Do not clean endlessly.

A working, clear feature is better than an abstractly perfect structure.

## 30. Definition of Done for Code Quality

A feature meets the code quality standard when:

it is understandable;
it follows existing structure;
it has no unnecessary duplication;
it validates input;
it checks ownership;
it escapes output;
it handles errors safely;
it does not store unnecessary data;
it is committed with a useful message;
it can be changed later without fear.

## 31. Guiding Question

Every code quality decision should be tested against this:

Will I understand this choice three months from now?

If the honest answer is no, improve the name, split the code, add a short comment or document the decision.
