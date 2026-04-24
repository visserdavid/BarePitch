# BarePitch – Testing Strategy

## 1. Purpose

This document describes the testing approach for BarePitch.

BarePitch is a small application, but it still needs structured testing. Small projects often fail not because they lack complexity, but because basic assumptions are never checked properly.

Testing should answer one question:

Does BarePitch work safely and predictably for the coach, even when input, users or conditions are imperfect?

## 2. Testing Principles

BarePitch uses practical, lightweight testing.

The principles are:

Test early.
Test each feature while building it.

Test vertically.
A full user flow is more valuable than isolated fragments.

Test the unsafe path.
Do not only test what should happen. Test what should not happen.

Test security as part of functionality.
A feature is not working if it can be misused.

Test with minimal real-world pressure.
BarePitch must work on a phone, quickly, during match preparation.

## 3. Main Test Categories

BarePitch should be tested across these areas:

functional testing;
validation testing;
security testing;
permission and access testing;
database testing;
interface testing;
responsive testing;
accessibility testing;
error handling testing;
regression testing;
deployment testing;
privacy testing.

## 4. Functional Testing

Functional testing checks whether the application does what it is supposed to do.

For Version 1, test these flows:

User can log in.
User can log out.
User can create a team.
User can edit a team.
User can archive a team.
User can add players.
User can edit players.
User can mark players inactive.
User can create matches.
User can edit matches.
User can select players for a match.
User can save availability or selection.
Saved data remains available after logout and login.

Functional testing should follow real use.

Example test:

Create a team.
Add five players.
Create a match.
Mark three players as selected.
Log out.
Log in again.
Open the match.
Confirm the selection is still correct.

## 5. Authentication Testing

Authentication must be tested before protected data is added.

Test cases:

Valid email and password logs in.
Invalid password fails.
Unknown email fails with neutral message.
Empty email is rejected.
Empty password is rejected.
User is redirected to dashboard after login.
User is redirected to login when accessing dashboard without session.
Logout destroys the session.
Back button after logout does not expose protected data.

Expected login error:

```text
Invalid email or password.
```

The system must not reveal whether the email address exists.

## 6. Authorization Testing

Authorization testing checks whether users can only access their own data.

Create at least two test users:

```text
coach_a@example.test
coach_b@example.test
```

Create teams, players and matches for both users.

Then test:

Coach A cannot open Coach B’s team by changing the URL.
Coach A cannot edit Coach B’s players.
Coach A cannot open Coach B’s matches.
Coach A cannot save attendance for Coach B’s match.
Coach A cannot submit Coach B’s player ID in a hidden form field.

Expected behavior:

Return 404 or 403.
Do not show data.
Do not change data.
Log suspicious access if useful.

## 7. Validation Testing

Validation testing checks whether bad input is rejected safely.

Test every form with:

empty required fields;
too long values;
invalid dates;
invalid email format;
non-numeric IDs;
invalid attendance statuses;
unexpected characters;
leading and trailing spaces.

Examples:

Team name:

```text
empty
more than 100 characters
<script>alert('xss')</script>
```

Shirt number:

```text
abc
0
100
-5
```

Match date:

```text
not-a-date
2026-02-31
```

Attendance status:

```text
available
selected
injured
admin
```

Only allowed values may be accepted.

## 8. CSRF Testing

Every state-changing form must reject missing or invalid CSRF tokens.

Test:

Submit form normally.
Remove CSRF token and submit.
Change CSRF token and submit.
Reuse an old token if token rotation is used.

Expected behavior:

Request is rejected.
No data is saved.
User sees a generic invalid request message.
Technical details are not shown.

## 9. SQL Injection Testing

All database access must use prepared statements.

Manual test inputs:

```sql
' OR '1'='1
```

```sql
'; DROP TABLE users; --
```

Use these in:

login email;
team name;
player name;
opponent name;
URL ID parameters where possible.

Expected behavior:

No unintended database behavior.
No login bypass.
No SQL error visible to user.
Input is rejected or stored safely as text.

## 10. Cross-Site Scripting Testing

Test whether user input is safely escaped when displayed.

Input examples:

```html
<script>alert('xss')</script>
```

```html
<img src=x onerror=alert('xss')>
```

Test in:

team names;
player names;
opponent names;
season labels;
locations.

Expected behavior:

The text is displayed harmlessly or rejected.
No script runs.
No HTML is interpreted from user input.

## 11. Session Testing

Test whether sessions behave safely.

Test cases:

Session is created after login.
Session ID changes after login.
Session is destroyed after logout.
Protected pages require active session.
Inactive session expires after the configured timeout.
Session cookie has secure settings in production.
Multiple users in different browsers do not share data.

Important:

Do not only test the happy path. Test stale, missing and expired sessions.

## 12. Database Testing

Database testing checks whether data is stored correctly and relationships remain intact.

Test:

User owns teams.
Teams contain players.
Teams contain matches.
Matches contain match player records.
Deleting or archiving a team behaves as expected.
Inactive players do not appear in default match selection.
Existing match history does not break when a player becomes inactive.
A player cannot be linked to a match from another team.

Also test migration reproducibility:

Drop local database.
Recreate database.
Run migrations.
Confirm application still works.

If the database cannot be rebuilt from migrations, the project is not stable enough.

## 13. Interface Testing

Interface testing checks whether the application is understandable and usable.

Test:

Clear navigation.
Forms have labels.
Buttons describe actions.
Error messages appear near the problem.
Success messages are visible but not intrusive.
Empty states are helpful.
The dashboard does not become cluttered.
Main actions are easy to find.

BarePitch should feel calm and direct. If a screen needs explanation, the screen probably needs simplification.

## 14. Responsive Testing

BarePitch will likely be used on a phone near training or match situations.

Test on:

mobile width;
tablet width;
desktop width.

Check:

forms remain usable;
buttons are large enough;
lists are readable;
navigation does not collapse into confusion;
attendance controls are easy to tap;
no horizontal scrolling unless intentional;
important actions remain visible.

Priority:

The match detail and attendance screens must work well on mobile.

## 15. Accessibility Testing

Accessibility testing should remain practical but serious.

Test:

Can the application be used with keyboard only?
Is focus visible?
Are form labels connected to inputs?
Are headings logical?
Is contrast sufficient?
Are errors understandable without relying only on color?
Do buttons and links have clear names?
Is the page readable when zoomed?

BarePitch does not need complexity here. It needs clean HTML and disciplined interface choices.

## 16. Error Handling Testing

Test whether errors are handled safely.

Test:

invalid URL ID;
missing record;
database temporarily unavailable;
unauthorized access;
invalid CSRF token;
unexpected form value;
duplicate data where relevant.

Expected behavior:

User sees a calm, non-technical message.
Technical details are logged.
No SQL errors are shown.
No file paths are shown.
The application does not expose data.

## 17. Privacy Testing

Privacy testing checks whether the system respects its own boundaries.

Test:

No unnecessary player data fields exist.
No birth dates are collected.
No phone numbers are collected.
No medical data is collected.
No photos or uploads are used.
Logs do not contain unnecessary personal data.
Exports or backups are not stored publicly.
Archived or deleted data behaves as documented.

Guiding question:

Is BarePitch quietly becoming a player dossier?

If yes, the design is drifting.

## 18. Regression Testing

Regression testing checks whether old functionality still works after new changes.

Before merging `wip` into `main`, retest:

login;
logout;
team overview;
create team;
add player;
create match;
save attendance;
ownership protection;
mobile match screen.

This does not need to be heavy. It does need to be consistent.

A small regression checklist is better than trusting memory.

## 19. Deployment Testing

Before production release, test the production-like environment.

Check:

`APP_DEBUG=false`;
HTTPS works;
secure cookies work;
database connection works;
migrations are applied;
logs are writable;
non-public folders are inaccessible;
`.env` is not accessible;
Git files are not accessible;
login works;
core flow works;
error pages work.

Test URLs such as:

```text
/.env
/app/config/database.php
/storage/logs/app.log
/.git/config
```

Expected behavior:

not accessible.

## 20. Manual Test Script for Version 1

Use this as a recurring test before tagging a version.

Create user A.
Log in as user A.
Create team “Test United”.
Add five players.
Create match against “Sample FC”.
Mark two players available.
Mark three players selected.
Log out.
Log in again.
Open the match.
Confirm statuses are preserved.
Log out.

Create user B.
Create another team and match.
Try to access user B’s match while logged in as user A.
Confirm access is denied.

Submit XSS input in a player name.
Confirm it does not execute.

Submit SQL injection input in login.
Confirm login is not bypassed.

Submit attendance form without CSRF token.
Confirm request is rejected.

Test same flow on mobile width.

## 21. Testing During Vibe Coding

Because BarePitch may be built through solo, exploratory coding, testing needs rhythm.

Use this pattern:

Before risky change: checkpoint commit.
After change works: functional test.
After security-sensitive change: access and validation test.
Before merge to main: regression test.
Before tag: deployment checklist.

Do not wait until the end. Vibe coding without testing creates fog.

## 22. Testing Documentation

Keep a simple test log in:

```text
docs/test-log.md
```

Example:

```markdown
## 2026-04-24 – v0.3.0
- Login tested: passed
- Team creation tested: passed
- Player creation tested: passed
- URL tampering team ID: passed
- Mobile player list: acceptable
- Known issue: attendance screen not yet responsive
```

This does not need to be formal. It needs to be honest.

## 23. Feature Test Checklist

A feature is ready when:

happy path works;
required fields are validated;
invalid input is rejected;
CSRF protection works;
ownership is checked;
output is escaped;
technical errors are hidden;
data persists correctly;
mobile layout is usable;
old core flows still work.

## 24. Version 1 Acceptance Test

BarePitch Version 1 passes acceptance testing when:

A coach can log in.
A coach can create a team.
A coach can add players.
A coach can create a match.
A coach can set availability or selection.
The data persists.
The interface works on mobile.
Another user cannot access the data.
Common injection attempts fail.
No unnecessary personal data is stored.
Production configuration is safe.

## 25. Guiding Testing Question

Every test should connect to this question:

What could go wrong if this is used by a real coach with real team data?

If the answer reveals risk, test that risk before moving on.
