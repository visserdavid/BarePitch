# BarePitch — Backend Rules and Validation Guide

**Version 1.0 — April 2026**

---

## 1. Purpose

This document defines the backend behavior for BarePitch.

It covers:

- match locking
- transaction handling
- validation responsibilities
- correction handling
- score recalculation
- error handling rules

The goal is:

- predictable behavior
- data integrity
- low operational overhead
- suitability for shared hosting with PHP and MySQL

---

## 2. General Principles

- The database protects structure and relational integrity
- PHP protects business rules and authorization
- Match event tables are the source of truth
- Cached values on `match` exist for read performance only
- Editing must be safe for concurrent users
- Corrections must be traceable

---

## 3. Locking

### 3.1 Scope

Locking applies at match level.

A lock exists to prevent simultaneous edits to the same match.

Locking is not implemented through long-running database transactions.

Locking is implemented through:

- `match.locked_by_user_id`
- `match.locked_at`

---

### 3.2 Lock Acquisition

When a user enters match edit mode:

1. Read current lock state
2. A lock may be acquired when:
   - `locked_by_user_id` is `NULL`
   - or `locked_at` is older than the lock timeout
   - or the current user already owns the lock
3. If lock acquisition is allowed:
   - set `locked_by_user_id = current_user_id`
   - set `locked_at = NOW()`

---

### 3.3 Lock Timeout

Default lock timeout:

- 2 minutes

A lock is considered expired when:

- `NOW() - locked_at > 2 minutes`

---

### 3.4 Lock Refresh

While a user actively edits a match:

- refresh `locked_at` every 30 seconds

This keeps the lock alive without opening long-running transactions.

---

### 3.5 Lock Release

A lock must be released when:

- the user saves
- the user cancels editing
- the match editor is explicitly closed
- the lock expires by timeout

Release behavior:

- set `locked_by_user_id = NULL`
- set `locked_at = NULL`

---

### 3.6 Lock Conflict

When another user tries to edit a locked match:

- deny edit access
- allow read-only access if desired
- return a clear message that the match is currently being edited by another user

No silent overwrite is allowed.

---

## 4. Transactions

### 4.1 General Rule

Any mutation that changes match state or match-derived aggregates must run inside a database transaction.

Examples:

- add match event
- edit match event
- delete match event
- add penalty shootout attempt
- edit penalty shootout attempt
- delete penalty shootout attempt
- perform substitution
- mark red card
- correct finished match data

---

### 4.2 Transaction Scope

A transaction must include:

1. write to source table
2. recalculate affected aggregates
3. update `match`
4. write audit log when applicable
5. commit

If any step fails:

- rollback everything

---

### 4.3 Score Recalculation

Cached score values on `match` must never be manually trusted without recalculation.

Source of truth:

- `match_event` for:
  - regular goals
  - goals in extra time
  - scored penalties during match
- `penalty_shootout_attempt` for shootout goals

After every score-relevant mutation:

- recalculate `goals_scored`
- recalculate `goals_conceded`
- recalculate `shootout_goals_scored`
- recalculate `shootout_goals_conceded`

Then update `match`.

---

### 4.4 Recalculation Strategy

Recommended strategy:

- always recalculate from source rows for the affected match
- do not use naive increment/decrement logic as the only mechanism

Reason:

- corrections and deletions must remain reliable
- recalculation from source is more robust than differential patching

This is acceptable for BarePitch because:

- match-level data volume is limited
- shared hosting still supports this scale comfortably

---

### 4.5 Finished Match Corrections

When correcting a finished match:

1. start transaction
2. update source data
3. recalculate all relevant cached values
4. write audit entries
5. commit

The match keeps status `finished`.

The match must not automatically revert to `active` or `prepared`.

---

## 5. Validation Responsibility

### 5.1 Database Responsibilities

The database is responsible for:

- primary keys
- foreign keys
- uniqueness
- not-null requirements
- structural check constraints
- simple range validation

Examples:

- valid foreign references
- unique user email
- unique player per season context
- grid row between 1 and 10
- grid col between 1 and 11
- valid enumerated string keys where defined

---

### 5.2 PHP Responsibilities

PHP is responsible for all business logic, workflow rules, and authorization.

Examples:

- role checks
- access checks per team
- match state transitions
- minimum and maximum player counts
- all starting positions filled before `prepared`
- only coach may change lineup
- no assist on penalty
- injured excluded from attendance percentage
- only complete ratings count
- sent-off player cannot re-enter
- livestream expiration rules
- correction permissions after `finished`

---

### 5.3 Rule of Separation

Use this rule consistently:

- database = structural truth
- PHP = behavioral truth

Business logic must not be spread unpredictably between SQL and PHP.

---

## 6. Authorization Rules

### 6.1 Principle

Authorization is always checked server-side.

Hiding buttons in the UI is not sufficient.

Every write action must validate:

- authenticated user
- team relationship
- role
- intended action
- target match or record

---

### 6.2 Finished Match Corrections

Only these roles may edit finished matches:

- coach of the relevant team
- administrator

No other role may correct finished match data.

---

### 6.3 Tactical vs Administrative Actions

Tactical actions:

- lineup changes
- substitutions
- tactical match control

Administrative actions:

- player management
- attendance
- data support

The backend must enforce this distinction.

---

## 7. Match State Rules

### 7.1 planned → prepared

Allowed only when:

- number of present players is at least 11
- number of present players does not exceed team limit
- formation is selected
- every starting position is filled

Players not assigned to the field become bench players automatically.

---

### 7.2 prepared → active

Allowed only by coach.

When activated:

- first half starts immediately
- score becomes 0–0
- active phase becomes `regular_time`
- livestream starts
- match events become available

---

### 7.3 Half Start and End

Half transitions are manual.

Recommended interaction:

- swipe + confirmation for start
- swipe + confirmation for end

No half ends automatically based on duration.

Configured duration is contextual only.

---

### 7.4 Extra Time

After regular time, coach may choose:

- finish match
- start extra time
- start penalty shootout

Extra time consists of:

- two periods
- configurable duration per period

---

### 7.5 Penalty Shootout

Penalty shootout is a separate active phase.

It has its own logic and its own table.

It does not modify the regular match score.

It may end:

- automatically when mathematically decided
- manually with confirmation

Both ending modes must require confirmation before the match is finalized.

---

## 8. Red Card Rules

When a player receives a red card:

- player leaves the field immediately
- playing time stops at red card time
- team field count is reduced by one
- player is moved to bench context
- player cannot re-enter:
  - regular time
  - extra time
  - penalty shootout

Backend must reject any attempt to reinsert that player.

---

## 9. Attendance Rules

Statuses:

- `present`
- `absent`
- `injured`

Rules:

- `injured` is not counted as present
- `injured` is not counted as absent
- attendance percentage excludes activities where player status is `injured`

Example:

If a player has 9 present sessions and 1 injured session,
attendance percentage is calculated over 9 counted sessions, not 10.

---

## 10. Rating Rules

A rating counts only if fully completed.

Fully completed means:

- all rating fields required by the rating model contain valid values

Partial ratings:

- are stored if desired
- do not count in average calculations
- are treated as incomplete

Use `is_complete` as the backend flag for rating eligibility.

---

## 11. Livestream Rules

### 11.1 Start

Livestream starts when match becomes `active`.

---

### 11.2 End

Livestream remains available after match finish for a configured period.

Defaults:

- default duration: 24 hours
- hard maximum: 72 hours

Coach may manually stop livestream earlier.

---

### 11.3 Correction Reflection

While livestream is still active:

- corrected match data must also be reflected in livestream output

Livestream must not freeze old incorrect values while still active.

---

## 12. Error Handling

### 12.1 General Rule

Errors must be:

- explicit
- user-safe
- non-technical in user-facing messages
- detailed in server logs

---

### 12.2 User-Facing Messages

User messages must not expose:

- SQL details
- stack traces
- table names
- internal exception messages

Examples of acceptable user messages:

- "This match is currently being edited by another user."
- "You do not have permission to perform this action."
- "The lineup is incomplete."
- "The match could not be saved. Please try again."

---

### 12.3 Server Logs

Server logs should contain:

- timestamp
- user id where available
- route or action
- error class
- internal message
- stack trace where available

Sensitive data such as raw tokens must not be logged.

---

## 13. Recommended PHP Implementation Structure

Suggested separation:

- Controller
- Service
- Repository
- Validator
- Policy / Authorization layer

Recommended responsibility split:

### Controller

- request parsing
- response formatting

### Service

- business workflow
- transaction orchestration

### Repository

- database read/write logic

### Validator

- payload and rule validation

### Policy / Authorization

- role and team access checks

---

## 14. Operational Recommendations

### 14.1 Use PDO

Use PDO with prepared statements for all queries.

### 14.2 Keep transactions short

Transactions should wrap one atomic business operation only.

### 14.3 Recalculate instead of patching blindly

Always favor deterministic recalculation from source rows.

### 14.4 Use server time only

All timestamps must come from server-side logic.

### 14.5 Lock editor routes only

Do not lock read-only routes.

---

## 15. Summary

Recommended backend strategy for BarePitch:

- application-level match locking
- 2-minute lock timeout
- 30-second lock refresh
- transaction per score-relevant mutation
- event tables as source of truth
- cached match scores for fast reads
- structural validation in MySQL
- business validation in PHP
- strict server-side authorization
- audit logging for finished match corrections

---

## End
