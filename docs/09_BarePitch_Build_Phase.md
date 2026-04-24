# BarePitch – Build Phase

## 1. Purpose

This document describes how BarePitch should be built during the first implementation phase.

The build phase translates the concept, scope, functional design, technical design, database design and security design into working software.

BarePitch is built with:

PHP
MySQL
HTML
CSS
Vanilla JavaScript

No frameworks are used.

The main goal is not to build fast, but to build in a controlled rhythm. Each part should be small, testable and understandable.

## 2. Build Principle

BarePitch should be built vertically, not horizontally.

That means:

Do not build the full database first.
Do not build all pages first.
Do not style everything before behavior works.
Do not add JavaScript before the server-side flow is correct.

Instead, build one complete user flow at a time.

Example:

Login form
Server-side validation
Database lookup
Session creation
Redirect
Logout
Error handling
Basic styling
Manual test

Only then move to the next flow.

## 3. Recommended Build Order

The recommended order for BarePitch Version 1 is:

Project bootstrap
Database connection
Authentication
Dashboard
Team management
Player management
Match management
Attendance management
Basic responsive interface
Security cleanup
Production readiness review

This order prevents later features from being built on an unstable foundation.

## 4. Phase 1: Project Bootstrap

Start with the skeleton.

Create:

```text
public/
app/
database/
storage/
docs/
```

Create the first reusable files:

```text
app/config/bootstrap.php
app/config/database.php
app/helpers/view.php
app/helpers/auth.php
app/helpers/csrf.php
```

The bootstrap file should handle:

environment loading
error settings
session start
database loading
helper loading

Every public PHP file should start from the same bootstrap.

Example:

```php
require __DIR__ . '/../app/config/bootstrap.php';
```

This avoids each page inventing its own setup.

## 5. Phase 2: Database Connection

Create the database connection before building features.

Use PDO.

The connection should:

use prepared statements;
throw exceptions;
use UTF-8;
use associative arrays by default;
come from environment variables.

No page should create its own raw database connection.

All pages use the shared connection.

## 6. Phase 3: HTML Foundation

HTML should be semantic and boring in the best sense.

Use:

```html
<header>
<nav>
<main>
<section>
<form>
<label>
<button>
```

Avoid unnecessary wrapper elements.

Every form field must have a label.

Example:

```html
<label for="team_name">Team name</label>
<input type="text" id="team_name" name="team_name" required>
```

Use buttons for actions.

Use links for navigation.

Do not use clickable `div` elements for important interactions.

## 7. Phase 4: CSS Foundation

CSS should support clarity, not visual decoration.

Start with:

```text
base.css
layout.css
components.css
forms.css
pages.css
```

Build simple reusable patterns:

page container
header
navigation
card
button
form group
error message
success message
table or list view

The first visual goal:

BarePitch should be readable and usable on a phone.

Not polished. Usable.

CSS should avoid one-off fixes as much as possible. If the same styling appears three times, turn it into a reusable class.

## 8. Phase 5: JavaScript Foundation

JavaScript should enhance the interface, not carry essential logic.

Version 1 should work mostly without JavaScript.

Use JavaScript for:

small interface improvements;
confirming destructive actions;
showing or hiding optional fields;
improving attendance interaction.

Do not use JavaScript for:

security;
authorization;
final validation;
database logic;
hidden business rules.

When inserting text into the page, use:

```js
element.textContent = value;
```

Avoid:

```js
element.innerHTML = value;
```

unless the HTML is fully controlled by the application.

## 9. Phase 6: Authentication Build

Build authentication before user-owned data.

Required files:

```text
public/login.php
public/logout.php
app/models/UserModel.php
app/services/AuthService.php
```

The login flow:

Show login form.
Validate email and password.
Find user by email.
Verify password.
Regenerate session ID.
Store user ID in session.
Redirect to dashboard.

Rules:

Use neutral error messages.
Never reveal whether an email exists.
Never store plain passwords.
Never log passwords.
Destroy session fully on logout.

Authentication must be tested before any team data is added.

## 10. Phase 7: Dashboard Build

The dashboard is the first logged-in page.

It should show:

user context;
list of teams;
create team action;
logout action.

For Version 1, keep it simple.

Do not add statistics, widgets or complex overviews.

The dashboard answers one question:

Where does the coach go next?

## 11. Phase 8: Team Management Build

Build team management as the first real data flow.

Required actions:

create team;
view teams;
open team;
edit team;
archive team.

Server-side checks:

user must be logged in;
team name is required;
team belongs to current user;
CSRF token is valid for POST actions.

Recommended build sequence:

team migration;
TeamModel;
team list page;
create team form;
team detail page;
edit team;
archive team.

Do not build player or match features until team ownership works correctly.

## 12. Phase 9: Player Management Build

Players belong to teams.

Required actions:

add player;
list players;
edit player;
mark player inactive.

Avoid permanent deletion when a player already has match history.

Version 1 player fields:

display name;
shirt number, optional;
status.

Do not add:

date of birth;
address;
phone number;
email;
medical information;
free-text notes.

Every player action must first confirm that the team belongs to the logged-in user.

## 13. Phase 10: Match Management Build

Matches belong to teams.

Required actions:

create match;
list matches;
open match;
edit match;
archive match.

Version 1 match fields:

opponent name;
match date;
kickoff time, optional;
home, away or neutral, optional;
location, optional.

The match list should prioritize practical use.

Upcoming matches should be easy to find.

The match detail page becomes the bridge to attendance and selection.

## 14. Phase 11: Attendance Build

Attendance is the first many-to-many flow.

A match has many players.
A player can appear in many matches.
The `match_players` table stores the match-specific status.

Version 1 statuses:

unknown
available
unavailable
selected

Build sequence:

load match with ownership check;
load active players from the match team;
load existing match player statuses;
show status controls;
validate submitted statuses;
save using upsert;
show confirmation.

Before saving, check:

the match belongs to the current user;
each player belongs to the same team as the match;
each status is allowed.

This is important. Never trust player IDs submitted by the form.

## 15. PHP Build Rules

Use PHP for server-side flow, validation, security and rendering.

Rules:

Keep public files thin.
Move database logic into models.
Move repeated logic into services or helpers.
Use prepared statements everywhere.
Escape output in views.
Validate every POST request.
Check ownership on every protected action.
Do not mix large SQL blocks into HTML templates.

A public page may coordinate.

A model may query.

A service may decide.

A view may display.

This separation keeps the application understandable.

## 16. MySQL Build Rules

Use migrations for every database change.

Do not change the database manually and forget to record it.

Rules:

Use InnoDB.
Use utf8mb4.
Use foreign keys.
Use indexes for ownership and common lookups.
Use clear column names.
Use `created_at`, `updated_at` and `deleted_at` where useful.

Every query that fetches user-owned data must include ownership logic, directly or through a checked parent entity.

## 17. HTML Build Rules

HTML must be accessible by default.

Rules:

Use one clear `main` area.
Use logical headings.
Use labels for all inputs.
Use field errors near the field.
Use buttons for submissions.
Use links for navigation.
Do not rely on icons alone.
Do not communicate meaning only through color.

Good HTML reduces the need for complex JavaScript later.

## 18. CSS Build Rules

CSS should support BarePitch’s product philosophy.

The interface should feel:

calm;
minimal;
direct;
usable during real coaching situations.

Practical rules:

mobile first;
large touch targets;
readable font sizes;
clear spacing;
limited visual hierarchy;
consistent buttons;
consistent form layout;
no decorative clutter.

Avoid premature visual perfection. First make it clear. Then make it refined.

## 19. JavaScript Build Rules

JavaScript should remain modest in Version 1.

Possible use cases:

toggle attendance status quickly;
confirm archive actions;
highlight unsaved changes;
improve mobile navigation.

Rules:

all submitted data must still be validated in PHP;
all authorization remains server-side;
avoid global scripts that affect unrelated pages;
keep scripts small and page-specific where useful.

If JavaScript becomes necessary for the main feature to work, reconsider the design.

## 20. Error and Feedback Build

Every action should produce clear feedback.

Examples:

Team created.
Player updated.
Match saved.
Attendance saved.
Please enter a team name.
You do not have access to this page.

Do not show technical messages to users.

Internally, log the technical problem.

A good BarePitch error message should be calm and useful, not dramatic.

## 21. Security During Build

Security must be added while building each feature, not afterwards.

For every form:

server-side validation;
CSRF token;
ownership check;
safe redirect after success;
safe error handling.

For every output:

escape dynamic values.

For every database action:

prepared statement.

For every protected page:

authentication check.

Security that is postponed becomes rework.

## 22. Testing While Building

Test each flow immediately after building it.

For every feature, test:

happy path;
empty fields;
invalid values;
unauthenticated access;
wrong user access;
CSRF missing or invalid;
XSS input;
changed URL ID;
mobile display.

Do not wait until the end.

Small tests during building prevent large uncertainty later.

## 23. Version Control During Build

Use the solo workflow:

```text
main
wip
spike/*
```

During build, commit at natural checkpoints.

Examples:

```text
wip: create team form
wip: connect team form to database
feat: add team creation flow
security: add csrf validation to team forms
fix: prevent access to other user teams
```

Before using AI-generated changes, make a checkpoint commit.

This protects your own direction.

## 24. Build Milestones

Suggested milestones:

```text
v0.1.0 project structure and database connection
v0.2.0 authentication
v0.3.0 teams
v0.4.0 players
v0.5.0 matches
v0.6.0 attendance
v0.7.0 responsive cleanup
v0.8.0 security cleanup
v1.0.0 first production-ready release
```

Each milestone should represent a working state.

## 25. When Not to Continue Building

Stop and clean up when:

a file becomes too large;
SQL appears inside multiple pages;
the same validation appears in several places;
ownership checks feel unclear;
you cannot explain the current flow;
styling depends on many exceptions;
you are adding features to avoid fixing structure.

This is especially important during vibe coding.

Momentum is useful, but not when it creates fog.

## 26. Build Definition of Done

A feature is done when:

the user can complete the intended action;
input is validated server-side;
database queries use prepared statements;
output is escaped;
CSRF protection exists for POST actions;
ownership is checked;
errors are handled cleanly;
the mobile layout is usable;
the code is committed;
the feature still works after logout and login.

If one of these is missing, the feature works, but is not done.

## 27. Guiding Build Question

Every build decision should be tested against this question:

Does this make BarePitch clearer, safer or more useful for the coach in the moment?

If not, postpone it.
