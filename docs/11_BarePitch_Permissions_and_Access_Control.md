# BarePitch – Permissions and Access Control

## 1. Purpose

This document defines how BarePitch controls access to data and actions.

For Version 1, BarePitch does not need a complex role system. It needs something simpler and stricter:

A logged-in user can only access the teams, players, matches and attendance records that belong to that user.

The goal is to prevent accidental or deliberate access to another user’s data.

## 2. Core Access Principle

The central rule is:

A user may only see or change data that belongs to their own account.

This applies to:

teams
players
matches
match player records
attendance and availability statuses

The browser must never be trusted. URLs, hidden fields and JavaScript can all be manipulated.

Therefore, access control must always happen in PHP on the server.

## 3. Version 1 Access Model

Version 1 uses a single-owner model.

```text
User
  owns Teams
    contain Players
    contain Matches
      contain Match Player records
```

This means:

A team belongs to one user.
A player belongs to one team.
A match belongs to one team.
A match player record belongs to one match and one player.

Access is granted only by following this ownership chain.

## 4. What BarePitch Does Not Use in Version 1

Version 1 does not include:

admin roles
assistant coach roles
player accounts
team sharing
club-level permissions
public links
guest access
role hierarchy

These are deliberately postponed.

Adding them too early would increase complexity, privacy risk and the chance of access-control mistakes.

## 5. Authentication vs Authorization

Authentication answers:

Who is logged in?

Authorization answers:

What is this person allowed to do?

A user being logged in is not enough.

Every protected action must check both:

is there a logged-in user?
does the requested resource belong to that user?

Example:

A logged-in user may open `/match.php?id=15` only if match 15 belongs to a team owned by that user.

## 6. Protected Pages

The following pages require authentication:

```text
/dashboard.php
/teams.php
/team.php
/players.php
/matches.php
/match.php
```

If the user is not logged in:

redirect to login;
do not show protected content;
do not run protected queries.

The following pages may be public:

```text
/login.php
```

Logout should require a valid session and preferably be handled as a POST action with CSRF protection.

## 7. Resource Ownership Checks

Every resource must be loaded with ownership validation.

### 7.1 Team access

Unsafe:

```sql
SELECT * FROM teams WHERE id = :team_id;
```

Safe:

```sql
SELECT *
FROM teams
WHERE id = :team_id
  AND user_id = :user_id
  AND deleted_at IS NULL;
```

### 7.2 Player access

Unsafe:

```sql
SELECT * FROM players WHERE id = :player_id;
```

Safe:

```sql
SELECT p.*
FROM players p
JOIN teams t ON t.id = p.team_id
WHERE p.id = :player_id
  AND t.user_id = :user_id
  AND p.deleted_at IS NULL
  AND t.deleted_at IS NULL;
```

### 7.3 Match access

Unsafe:

```sql
SELECT * FROM matches WHERE id = :match_id;
```

Safe:

```sql
SELECT m.*
FROM matches m
JOIN teams t ON t.id = m.team_id
WHERE m.id = :match_id
  AND t.user_id = :user_id
  AND m.deleted_at IS NULL
  AND t.deleted_at IS NULL;
```

### 7.4 Match player access

Unsafe:

```sql
SELECT * FROM match_players WHERE id = :match_player_id;
```

Safe:

```sql
SELECT mp.*
FROM match_players mp
JOIN matches m ON m.id = mp.match_id
JOIN teams t ON t.id = m.team_id
WHERE mp.id = :match_player_id
  AND t.user_id = :user_id
  AND m.deleted_at IS NULL
  AND t.deleted_at IS NULL;
```

The rule is simple:

Never load a protected record by its own ID alone.

## 8. Form Submission Access Control

Every POST action must repeat access checks.

It is not enough that the form was shown to the right user.

Submitted data may be manipulated.

For example, when saving attendance:

The match ID must belong to the logged-in user.
Every submitted player ID must belong to the same team as the match.
Every submitted status must be one of the allowed values.

Unsafe assumption:

```text
Only valid players were shown in the form, so the submitted players are valid.
```

Correct assumption:

```text
The submitted player IDs may have been changed.
```

## 9. Access Control by Functional Area

## 9.1 Dashboard

User may see:

their own teams only.

User may not see:

global team lists;
other users;
other users’ team counts;
system-wide data.

## 9.2 Teams

User may:

create their own teams;
view their own teams;
edit their own teams;
archive their own teams.

User may not:

view another user’s team;
edit another user’s team;
guess team IDs to access data.

## 9.3 Players

User may:

add players to their own teams;
view players in their own teams;
edit players in their own teams;
mark players inactive in their own teams.

User may not:

access a player without first passing team ownership;
move a player to another user’s team;
submit a manipulated team ID.

## 9.4 Matches

User may:

create matches for their own teams;
view matches from their own teams;
edit matches from their own teams;
archive matches from their own teams.

User may not:

create matches under another user’s team;
open a match from another user’s team;
edit match details by changing a URL ID.

## 9.5 Attendance

User may:

set player status for matches belonging to their own teams;
select players from the match’s own team;
change status values within allowed options.

User may not:

add players from another team;
add players from another user;
submit arbitrary status values;
change attendance for another user’s match.

## 10. Handling Unauthorized Access

When a user requests a resource they may not access, BarePitch should usually behave as if the resource does not exist.

Preferred response:

```text
404 Not Found
```

Reason:

A clear “forbidden” message may reveal that the resource exists.

Use `403 Forbidden` when:

the user is authenticated;
the action is clearly forbidden;
revealing the existence of the page or action is not sensitive.

For most ID-based resources, prefer 404.

## 11. Access Control Helpers

Create reusable helper functions.

Examples:

```php
function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}
```

```php
function currentUserId(): int
{
    return (int) $_SESSION['user_id'];
}
```

Models should include ownership-aware methods:

```php
findTeamForUser(int $teamId, int $userId)
findPlayerForUser(int $playerId, int $userId)
findMatchForUser(int $matchId, int $userId)
```

Avoid generic methods in protected contexts, such as:

```php
findTeamById($teamId)
```

unless they are used only internally and safely.

## 12. Deny by Default

BarePitch should deny access unless permission is proven.

Do not use this pattern:

```php
$allowed = true;
// later check conditions
```

Use this pattern:

```php
$allowed = false;
// grant only after ownership check succeeds
```

If a resource cannot be verified, reject the request.

Failure should be safe.

## 13. Hidden Fields Are Not Permissions

Hidden fields may be useful for passing IDs.

They are not proof of permission.

Example:

```html
<input type="hidden" name="team_id" value="12">
```

A user can change `12` to another value before submitting.

Therefore:

validate the ID;
load the team by `team_id` and `user_id`;
reject the request if the relationship is invalid.

## 14. JavaScript Is Not Permissions

JavaScript may hide buttons or improve interface flow.

It must not decide access.

For example, hiding an edit button is fine for usability.
But the server must still reject the edit request if the user is not allowed.

Every permission rule must exist in PHP.

## 15. URL Tampering Tests

For every ID-based page, test manual changes.

Examples:

```text
/team.php?id=1
/team.php?id=2
/match.php?id=7
/player_edit.php?id=9
```

Expected result:

the user only sees records they own;
non-owned records return 404 or 403;
the application does not crash;
nothing is leaked.

## 16. Multi-User Future Compatibility

Version 1 is single-owner.

However, the design should avoid blocking future shared team access.

Future possibilities:

assistant coach access;
read-only access;
team manager role;
player account;
club-level grouping.

Do not build this now.

But avoid hardcoding assumptions everywhere that would make future access impossible.

For example:

Good:

```text
access is checked through a central helper or model method
```

Risky:

```text
ownership logic copied manually across every page
```

Centralized access checks make later expansion easier.

## 17. Database Support for Access Control

The database must support ownership checks.

Required columns:

```text
teams.user_id
players.team_id
matches.team_id
match_players.match_id
match_players.player_id
```

Required indexes:

```text
teams.user_id
players.team_id
matches.team_id
match_players.match_id
match_players.player_id
```

Access control becomes harder if relationships are vague or missing.

The database should make correct access checks natural.

## 18. Audit Awareness

Version 1 does not need a full audit log.

However, security-relevant events may be logged lightly:

failed access attempt;
repeated authorization failure;
unexpected ownership mismatch;
CSRF failure.

Example:

```text
Authorization failed: user_id=4 tried to access match_id=91
```

Do not log unnecessary personal data.

## 19. Common Access Control Mistakes

Avoid these mistakes:

checking login but not ownership;
checking ownership only on page load, not on POST;
trusting IDs from hidden fields;
using JavaScript to hide actions without server checks;
fetching records by ID alone;
copying permission logic inconsistently;
showing different errors that reveal whether a resource exists;
allowing inactive or archived records to be modified unintentionally.

## 20. Access Control Checklist

For every protected feature, check:

Does the page require login?
Does every loaded resource belong to the user?
Does every POST action repeat ownership checks?
Are all IDs validated?
Are hidden fields treated as untrusted?
Are JavaScript controls treated as usability only?
Does unauthorized access fail safely?
Are error messages not revealing too much?
Are ownership-aware model methods used?

## 21. Definition of Done

Permissions and access control are complete when:

unauthenticated users cannot access protected pages;
users can only see their own teams;
users can only manage players in their own teams;
users can only manage matches in their own teams;
attendance can only be saved for valid players in the match team;
all resource queries include ownership checks;
all POST actions repeat authorization checks;
URL tampering does not expose data;
hidden fields are not trusted;
unauthorized access returns safe errors;
access logic is not scattered randomly.

## 22. Guiding Question

Every access-control decision should be tested against this:

What happens if the user changes the ID manually?

If that creates access to data they should not see or change, the design is not safe enough.
