# BarePitch – Functional Design

## 1. Purpose

BarePitch is a minimal web application that helps a football coach manage teams, players, matches and attendance without unnecessary complexity.

The functional purpose of Version 1 is clear:

A coach must be able to log in, create a team, add players, create a match and select which players are available or present for that match.

## 2. Primary User

The primary user is the coach.

In Version 1, BarePitch assumes a single-user model:

One user owns and manages their own teams.
There are no assistant coaches, player accounts or shared editing rights yet.

This keeps the first version focused and reduces complexity in permissions, privacy and interface design.

## 3. Main User Flow

The core user flow is:

User logs in.
User opens dashboard.
User selects or creates a team.
User manages players.
User creates a match.
User selects players for that match.
User records availability or attendance.
User reviews the match overview.

This flow defines the functional backbone of BarePitch.

## 4. Functional Areas

## 4.1 Authentication

The system must allow a user to access their own environment securely.

Required functions:

User can log in with email and password.
User can log out.
User remains logged in during an active session.
User is redirected to login when not authenticated.
User cannot access another user’s data.

Functional rules:

Email is required.
Password is required.
Invalid login attempts show a neutral error message.
The system does not reveal whether the email address exists.
After login, the user is sent to the dashboard.
After logout, the session is destroyed.

## 4.2 Dashboard

The dashboard is the first screen after login.

Purpose:

Give the user quick access to teams and the next relevant action.

Required elements:

List of existing teams.
Button or link to create a new team.
Basic empty state when no team exists yet.
Clear navigation to logout.

Possible empty state:

“No teams yet. Create your first team to start using BarePitch.”

The dashboard should not become a statistics page in Version 1.

## 4.3 Team Management

The user must be able to create and manage teams.

Required functions:

Create team.
View team list.
Open team detail page.
Edit team name.
Archive or delete team.

Required fields:

Team name.
Season, optional for Version 1 but strongly recommended.

Functional rules:

Team name is required.
Team belongs to the logged-in user.
Only the owner can view or edit the team.
Archived teams are hidden from the default overview but remain retrievable if needed.
Deleting a team must not happen accidentally.

Preferred approach:

Use archive before permanent deletion.

## 4.4 Player Management

The user must be able to manage players within a team.

Required functions:

Add player.
View players in a team.
Edit player name.
Remove or archive player from team.

Required fields:

First name or display name.

Optional fields for later:

Shirt number.
Preferred position.
Status, active or inactive.

Explicitly excluded in Version 1:

Date of birth.
Address.
Phone number.
Email address.
Medical information.
Free-text personal notes.

Functional rules:

A player belongs to a team.
A player cannot be viewed outside the user’s own teams.
Player names must be escaped when displayed.
Removing a player should not break historical match records.

Preferred approach:

Use inactive status instead of hard deletion when a player has already been used in match records.

## 4.5 Match Management

The user must be able to create and view matches.

Required functions:

Create match.
View match list for a team.
Open match detail page.
Edit match details.
Archive or delete match.

Required fields:

Team.
Opponent name.
Match date.

Optional fields for later:

Home or away.
Kick-off time.
Location.
Competition phase.
Match type, such as league, friendly or tournament.

Functional rules:

A match belongs to one team.
Opponent name is required.
Match date is required in Version 1 if attendance is tied to planning.
Only matches from the user’s own teams are visible.
A match can exist without selected players at first.

## 4.6 Attendance and Availability

The user must be able to select players for a match and record attendance or availability.

Required functions:

View all active players for the selected team.
Mark player as selected.
Mark player as available, unavailable or unknown.
Save attendance state.
Review selection on match detail page.

Recommended statuses:

Unknown.
Available.
Unavailable.
Selected.
Present.
Absent.

For Version 1, keep this simpler:

Unknown.
Available.
Unavailable.
Selected.

Functional rules:

Only players from the match team can be selected.
A player can have one attendance status per match.
Changes are saved explicitly.
The interface should make clear which players are selected.

Important boundary:

BarePitch should not yet attempt to judge fairness, playing time or performance. Version 1 only records practical availability and selection.

## 5. Navigation Structure

Recommended Version 1 navigation:

Dashboard
Teams
Team detail
Players
Matches
Match detail
Logout

A possible route structure:

```text
/login
/logout
/dashboard
/teams
/teams/create
/teams/{id}
/teams/{id}/players
/teams/{id}/matches
/matches/{id}
```

In classic PHP this may translate to files or simple routing, for example:

```text
login.php
logout.php
dashboard.php
teams.php
team.php
players.php
matches.php
match.php
```

The structure may be simple, but the access checks must remain strict.

## 6. Screen Descriptions

## 6.1 Login Screen

Purpose:

Allow the user to authenticate.

Elements:

Email field.
Password field.
Login button.
Neutral error message area.

Functional behavior:

If login succeeds, redirect to dashboard.
If login fails, show generic error.
If already logged in, redirect to dashboard.

## 6.2 Dashboard Screen

Purpose:

Show the user’s teams and provide a clear starting point.

Elements:

Page title.
Team list.
Create team button.
Logout link.

Empty state:

If no teams exist, show a short message and one clear action.

## 6.3 Team Detail Screen

Purpose:

Show one team and its main actions.

Elements:

Team name.
Season, if used.
Player count.
Match count.
Links to players and matches.
Edit team action.

The team detail page should function as a small control panel for that team.

## 6.4 Player List Screen

Purpose:

Manage the team’s player pool.

Elements:

List of players.
Add player form or button.
Edit action per player.
Inactive indicator, if used.

Functional behavior:

Adding a player updates the list.
Editing a player does not affect historical records except display name.
Inactive players are hidden from default match selection unless explicitly shown.

## 6.5 Match List Screen

Purpose:

Show matches for a team.

Elements:

Upcoming matches.
Past matches.
Create match button.
Opponent name.
Date.
Selection status.

Functional behavior:

Newest or upcoming matches should be easiest to access.
A match can be opened for attendance management.

## 6.6 Match Detail Screen

Purpose:

Prepare and manage one match.

Elements:

Opponent.
Date.
Team name.
Player selection list.
Attendance or availability controls.
Save button.
Summary of selected players.

Functional behavior:

User can update player status.
System saves one status per player per match.
User receives confirmation after saving.

## 7. Validation Rules

General validation:

Required fields cannot be empty.
Text fields have maximum lengths.
Dates must be valid dates.
IDs from URLs must be checked against ownership.
Invalid input returns a clear error message.

Specific validation:

Email must be valid format.
Password must not be empty.
Team name must not be empty.
Player name must not be empty.
Opponent name must not be empty.
Match date must be valid.

Important:

Client-side validation may improve usability, but server-side validation is always required.

## 8. Error Handling

Functional error messages should be clear but not technical.

Examples:

“Please enter a team name.”
“Please enter a valid date.”
“This match could not be found.”
“You do not have access to this page.”
“Something went wrong. Please try again.”

Do not show:

SQL errors.
File paths.
Stack traces.
Raw exception messages.

## 9. Permissions

Version 1 uses a simple ownership model.

Rules:

A user can only see their own teams.
A user can only manage players within their own teams.
A user can only manage matches within their own teams.
A user can only update attendance for matches within their own teams.

Every page and every save action must check ownership server-side.

Do not rely on hidden form fields or JavaScript for access control.

## 10. Data Lifecycle

Data should have a simple lifecycle.

Teams:

Active.
Archived.
Deleted only when safe.

Players:

Active.
Inactive.
Deleted only if no relevant history exists.

Matches:

Planned.
Completed, optional for later.
Archived, optional.

Attendance:

Created when player status is saved for a match.
Updated when user changes status.
Deleted only if match is deleted.

## 11. Privacy Boundaries

BarePitch Version 1 must avoid unnecessary personal data.

Allowed:

Player display name.
Team membership.
Match participation status.

Avoid:

Birth dates.
Addresses.
Contact details.
Health information.
Behavioral notes.
Photos.

This keeps the system legally safer and conceptually cleaner.

## 12. Non-Functional Expectations

Although this is a functional design, some quality expectations shape the functions.

BarePitch must be:

Simple.
Responsive.
Readable on mobile.
Secure by default.
Fast enough for match-day use.
Understandable without instruction.

The interface should not feel like administration software. It should feel like a calm coaching tool.

## 13. Version 1 Acceptance Criteria

Version 1 can be accepted when:

A user can log in.
A user can log out.
A user can create a team.
A user can add players to that team.
A user can create a match for that team.
A user can mark player availability or selection for that match.
The data remains stored after logout and login.
A user cannot access another user’s data.
The application works on a mobile screen.
No unnecessary personal data is stored.

## 14. Explicit Non-Goals

Version 1 does not include:

Assistant coach accounts.
Player accounts.
Notifications.
Statistics.
Lineup builder.
Training planning.
Exports.
Calendar integration.
Photo storage.
Public team pages.
Complex permissions.

These are not rejected forever. They are deliberately postponed.

## 15. Guiding Functional Question

Every functional decision should be tested against this question:

Does this help the coach prepare or manage the next match?

If the answer is no, it does not belong in Version 1.
