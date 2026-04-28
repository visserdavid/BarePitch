# BarePitch — Application Architecture & API Design

**Version 1.0 — April 2026**

---

## 1. Purpose

This document defines the recommended application architecture and API design for BarePitch.

The design is optimized for:

- shared hosting
- PHP + MySQL
- server-side rendering
- minimal runtime overhead
- predictable behavior
- low operational complexity

This document assumes:

- no Node.js runtime
- no heavy PHP framework
- no SPA architecture
- no WebSocket infrastructure

---

## 2. Architectural Principles

| Principle               | Application                                                        |
| ----------------------- | ------------------------------------------------------------------ |
| Runtime simplicity      | PHP renders HTML on the server                                     |
| Hosting compatibility   | No daemon processes required                                       |
| Low memory usage        | No framework bootstrapping overhead                                |
| Predictable routing     | Single front controller with explicit route map                    |
| Progressive enhancement | JavaScript only where interaction requires it                      |
| Source of truth         | MySQL is authoritative for persistent data                         |
| Read efficiency         | Frequently used match aggregates stored on `match`                 |
| Write safety            | Transactions for match-critical writes                             |
| Security                | Server-side authorization on every state-changing action           |
| Maintainability         | Clear separation between controller, service, repository, and view |

---

## 3. Recommended Stack

| Layer          | Recommendation                                                  |
| -------------- | --------------------------------------------------------------- |
| Language       | PHP 8.x                                                         |
| Database       | MySQL 8.x or compatible                                         |
| DB access      | PDO with prepared statements                                    |
| HTML rendering | Native PHP templates                                            |
| CSS            | Plain CSS split into `base.css`, `layout.css`, `components.css` |
| JavaScript     | Small vanilla JavaScript modules                                |
| Authentication | Magic links via email                                           |
| Session        | Native PHP session handling                                     |
| Localization   | JSON language files loaded server-side                          |
| Polling        | Lightweight HTTP polling for livestream and live match updates  |

---

## 4. Application Structure

Recommended project structure:

```text
/public
  index.php
  /css
    base.css
    layout.css
    components.css
  /js
    match-live.js
    lineup-grid.js
    swipe-actions.js
  /uploads
    (unused in v1 because no photos)

/app
  /Config
    app.php
    database.php
    mail.php
  /Core
    Router.php
    Request.php
    Response.php
    Session.php
    Csrf.php
    Auth.php
    View.php
    I18n.php
    Lock.php
  /Controller
    AuthController.php
    DashboardController.php
    TeamController.php
    PlayerController.php
    TrainingController.php
    MatchController.php
    LivestreamController.php
    SettingsController.php
  /Service
    AuthService.php
    TeamService.php
    PlayerService.php
    TrainingService.php
    MatchService.php
    LivestreamService.php
    StatisticsService.php
    AuditService.php
  /Repository
    UserRepository.php
    TeamRepository.php
    PlayerRepository.php
    TrainingRepository.php
    MatchRepository.php
    AttendanceRepository.php
    AuditRepository.php
  /Validation
    PlayerValidator.php
    MatchValidator.php
    TrainingValidator.php
  /View
    /layout
    /auth
    /dashboard
    /team
    /player
    /training
    /match
    /settings
  /Lang
    en.json
    nl.json

/storage
  /logs
  /cache
```

---

## 5. Request Lifecycle

Every request follows this flow:

1. Request enters through `/public/index.php`
2. Router resolves route
3. Middleware-equivalent checks run:
   - session/authentication
   - CSRF for state-changing requests
   - role authorization
   - team access validation
4. Controller receives validated request
5. Controller calls service layer
6. Service performs domain logic
7. Repository performs database reads/writes
8. Response is returned:
   - HTML for normal pages
   - JSON for polling and interaction endpoints

---

## 6. Rendering Strategy

### 6.1 HTML Pages

Use server-side rendered HTML for:

- login
- dashboard
- team pages
- player pages
- training pages
- match preparation
- match review
- settings

### 6.2 JSON Endpoints

Use JSON only for:

- live polling
- dynamic lineup interactions
- quick event registration
- lock status checks
- small partial updates

This avoids building a full client-side application.

---

## 7. Authorization Model

Authorization is enforced server-side.

Rules:

- hidden UI is not sufficient protection
- every state-changing endpoint must verify role and team access
- administrator bypasses team restrictions
- all other roles are team-bound
- roles are cumulative

### 7.1 Role Summary

| Role          | Scope  |
| ------------- | ------ |
| administrator | global |
| trainer       | team   |
| coach         | team   |
| team_manager  | team   |

---

## 8. Route Design

Use readable server routes.

### 8.1 Authentication

| Method | Path                  | Purpose                  |
| ------ | --------------------- | ------------------------ |
| GET    | `/login`              | Login page               |
| POST   | `/login/request-link` | Request magic link       |
| GET    | `/login/consume`      | Consume token and log in |
| POST   | `/logout`             | End session              |

### 8.2 Dashboard

| Method | Path         | Purpose                |
| ------ | ------------ | ---------------------- |
| GET    | `/dashboard` | Current user dashboard |

### 8.3 Teams

| Method | Path                        | Purpose       |
| ------ | --------------------------- | ------------- |
| GET    | `/teams/{teamId}`           | Team overview |
| GET    | `/teams/{teamId}/players`   | Player list   |
| GET    | `/teams/{teamId}/trainings` | Training list |
| GET    | `/teams/{teamId}/matches`   | Match list    |

### 8.4 Players

| Method | Path                             | Purpose           |
| ------ | -------------------------------- | ----------------- |
| GET    | `/teams/{teamId}/players/create` | New player form   |
| POST   | `/teams/{teamId}/players`        | Create player     |
| GET    | `/players/{playerId}`            | Player profile    |
| GET    | `/players/{playerId}/edit`       | Edit player form  |
| POST   | `/players/{playerId}`            | Update player     |
| POST   | `/players/{playerId}/deactivate` | Deactivate player |

### 8.5 Trainings

| Method | Path                                     | Purpose         |
| ------ | ---------------------------------------- | --------------- |
| GET    | `/teams/{teamId}/trainings/{trainingId}` | Training detail |
| POST   | `/teams/{teamId}/trainings`              | Create training |
| POST   | `/trainings/{trainingId}`                | Update training |
| POST   | `/trainings/{trainingId}/attendance`     | Save attendance |
| POST   | `/trainings/{trainingId}/delete`         | Delete training |

### 8.6 Matches

| Method | Path                                   | Purpose                                   |
| ------ | -------------------------------------- | ----------------------------------------- |
| GET    | `/teams/{teamId}/matches/{matchId}`    | Match overview                            |
| GET    | `/teams/{teamId}/matches/create`       | Create match form                         |
| POST   | `/teams/{teamId}/matches`              | Create match                              |
| POST   | `/matches/{matchId}`                   | Update match metadata                     |
| POST   | `/matches/{matchId}/prepare`           | Transition to prepared                    |
| POST   | `/matches/{matchId}/start`             | Transition to active and start period     |
| POST   | `/matches/{matchId}/end-period`        | End active period                         |
| POST   | `/matches/{matchId}/start-next-phase`  | Start second half / extra time / shootout |
| POST   | `/matches/{matchId}/finish`            | Finish match                              |
| POST   | `/matches/{matchId}/lineup`            | Save current lineup                       |
| POST   | `/matches/{matchId}/substitutions`     | Register substitution                     |
| POST   | `/matches/{matchId}/events`            | Register match event                      |
| POST   | `/matches/{matchId}/shootout-attempts` | Register shootout attempt                 |
| POST   | `/matches/{matchId}/ratings`           | Save ratings                              |

### 8.7 Livestream

| Method | Path                                 | Purpose                            |
| ------ | ------------------------------------ | ---------------------------------- |
| GET    | `/live/{token}`                      | Public livestream page             |
| GET    | `/live/{token}/poll`                 | Public livestream polling endpoint |
| POST   | `/matches/{matchId}/livestream/stop` | Manual early stop                  |

### 8.8 Settings

| Method | Path                   | Purpose                  |
| ------ | ---------------------- | ------------------------ |
| GET    | `/settings`            | Settings overview        |
| GET    | `/settings/clubs`      | Global admin only        |
| GET    | `/settings/seasons`    | Global admin only        |
| GET    | `/settings/formations` | Formation settings       |
| GET    | `/settings/users`      | User assignment overview |

---

## 9. JSON Endpoint Rules

JSON endpoints must:

- return only required fields
- never return full internal records unnecessarily
- validate all input server-side
- return predictable error shapes

Recommended error format:

```json
{
  "success": false,
  "error": {
    "code": "forbidden",
    "message": "You do not have permission to perform this action."
  }
}
```

Recommended success format:

```json
{
  "success": true,
  "data": {}
}
```

---

## 10. Match Editing Lock

A match uses an explicit edit lock.

### 10.1 Lock Acquisition

When a user opens a match editing screen:

- the system attempts to acquire a lock
- if no lock exists, lock is assigned to current user
- if lock exists and is still valid, editing is blocked for others

### 10.2 Lock Release

A lock is released when:

- the user saves successfully
- the user exits editing explicitly
- the lock expires by timeout

### 10.3 Lock Timeout

Recommended default timeout:

- 5 minutes of inactivity

---

## 11. Service Layer Responsibilities

### AuthService

- request magic links
- validate tokens
- create authenticated sessions
- logout

### TeamService

- load team dashboards
- validate team access
- manage team-level settings

### PlayerService

- create and update players
- manage season context
- resolve internal and external guest player selection

### TrainingService

- create and update trainings
- save attendance
- compute training attendance summaries

### MatchService

- prepare match
- validate lineup
- start and end periods
- register substitutions
- register events
- manage extra time and shootout transitions
- maintain aggregate score fields
- enforce red card rules
- finish match
- save corrections to finished matches

### LivestreamService

- validate public token access
- compute livestream expiration
- provide polling payload
- stop livestream manually

### StatisticsService

- derive player and team statistics
- calculate attendance percentages
- apply rating completeness rules

### AuditService

- log post-finish changes
- store old and new values

---

## 12. View Rules

Views must:

- contain presentation logic only
- never contain database queries
- never contain authorization logic
- receive already prepared view data from controllers/services

Views may contain:

- loops
- conditional rendering based on provided flags
- localized labels

---

## 13. Localization Strategy

### 13.1 Language Files

All UI text is stored in JSON files.

Examples:

- `/app/Lang/en.json`
- `/app/Lang/nl.json`

### 13.2 Translation Access

Use a helper function:

```php
__('match.start')
```

Rules:

- no hardcoded UI text in templates
- translation keys are stable
- fallback language is mandatory

### 13.3 Data vs UI

Translated:

- button labels
- navigation labels
- form labels
- messages
- event labels

Not translated in database:

- team names
- opponent names
- player names
- notes entered by users

---

## 14. Match Screen Flows

### 14.1 Planned Match

Screen sections:

- match metadata
- attendance
- guest player selection
- formation selection
- lineup grid
- bench

Primary actions:

- save metadata
- save attendance
- save lineup
- confirm preparation

### 14.2 Prepared Match

Screen sections:

- final lineup
- bench
- match summary
- livestream link

Primary actions:

- adjust lineup
- start match

### 14.3 Active Match

Screen sections:

- current score
- current phase
- lineup grid
- bench
- event timeline
- quick event buttons
- period control

Primary actions:

- substitute player
- move player
- register event
- end period
- start next phase
- finish match

### 14.4 Finished Match

Screen sections:

- final score
- event timeline
- playing time
- ratings
- audit-aware correction actions

Primary actions:

- edit match data (coach/admin only)
- save ratings
- review statistics

---

## 15. Public Livestream Flow

### 15.1 Access Rules

A livestream is available only when:

- match has a valid livestream token
- current time is before `livestream_expires_at`
- no manual stop has been recorded

### 15.2 Polling

Recommended interval:

- 30 to 60 seconds

Polling payload should include only:

- display team names
- score
- current lineup
- event timeline
- status text
- expiration state

---

## 16. Validation Principles

All validation is server-side.

Examples:

- player name required
- no more than max match players selected
- all lineup slots filled before preparation
- no re-entry after red card
- no assists for penalties
- no partial ratings included in averages

Client-side validation may be added for convenience only.

---

## 17. Transaction Boundaries

Use database transactions for:

- match preparation confirmation
- match start
- substitution registration
- event registration that affects score
- period end + state transition
- penalty shootout attempt registration
- post-finish corrections with audit entry

This avoids partial writes in critical flows.

---

## 18. Performance Guidelines

### 18.1 General

- prefer focused SQL over broad ORM-style loading
- select only required columns
- paginate lists when needed
- cache only lightweight derived values where operationally useful

### 18.2 Livestream

- use `match` aggregate score fields for reads
- pre-sort event payloads in SQL
- do not recompute heavy statistics on public livestream requests

### 18.3 Match Screens

- load current lineup state directly
- load only one match at a time
- calculate side summaries once per request in service layer

---

## 19. Security Rules

- PDO prepared statements only
- CSRF protection on all state-changing POST requests
- session cookies must be `HttpOnly`, `Secure`, `SameSite=Lax` or stricter
- role checks on every mutating request
- team access checks on every team-scoped request
- magic links stored as hashes, not plaintext
- neutral messaging for login link requests

---

## 20. Explicit Non-Goals

This architecture does not include:

- Node.js services
- WebSocket infrastructure
- SPA frontend architecture
- heavy PHP framework bootstrapping
- queue-dependent design
- external integration layer in v1

---

## 21. Recommended Next Technical Steps

1. Define SQL DDL from the approved schema
2. Define full route map with request/response contracts
3. Define view models per screen
4. Build authentication and authorization first
5. Build match flow before statistics
6. Add livestream polling after match flow is stable

---

## End
