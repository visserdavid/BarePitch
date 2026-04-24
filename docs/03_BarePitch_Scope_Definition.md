# BarePitch – Scope Definition

## 1. Context

BarePitch aims to be a minimal, functional tool for football coaches.
The purpose of this scope is not to define everything the system will do, but to define what it will **not** do, and what is **necessary for a first usable version**.

Scope is treated as a boundary, not as a wishlist.

---

## 2. Core Principle

> Version 1 is complete when a coach can prepare and manage a match using only BarePitch.

Everything that does not directly contribute to that outcome is outside scope.

---

## 3. Definition of Version 1 (MVP)

Version 1 is the smallest version that is:

* usable in practice
* technically stable
* conceptually coherent

It is not feature-complete. It is functionally sufficient.

---

## 4. In-Scope Functional Areas

### 4.1 Authentication

The system must support:

* user login with email and password
* secure session handling
* logout functionality

Purpose: restrict access to personal team data.

---

### 4.2 Team Management

The user must be able to:

* create a team
* view a list of teams
* select an active team

Purpose: provide structure for all other data.

---

### 4.3 Player Management

The user must be able to:

* add players to a team
* edit player names
* remove players from a team
* view the player list

Constraints:

* minimal data per player (name only, unless strictly needed otherwise)

Purpose: define the pool of participants.

---

### 4.4 Match Management

The user must be able to:

* create a match
* define opponent name
* set date (optional in early version)
* view match overview

Purpose: create context for selection and attendance.

---

### 4.5 Attendance Tracking

The user must be able to:

* select players for a match
* mark attendance or availability

Purpose: support match preparation decisions.

---

## 5. Out-of-Scope (Explicit Exclusions)

These features are intentionally excluded from Version 1:

### 5.1 Communication Features

* messaging
* notifications
* email integrations

Reason: adds complexity without improving core flow.

---

### 5.2 Advanced Data and Analytics

* statistics (goals, assists, performance metrics)
* historical comparisons
* dashboards

Reason: not required for match preparation.

---

### 5.3 Tactical Tools

* formation editors
* visual lineup builders
* drawing tools

Reason: tempting, but not essential for first version.

---

### 5.4 Multi-User Collaboration

* multiple roles (assistant, player access)
* shared editing
* permission hierarchies

Reason: significantly increases complexity in logic and security.

---

### 5.5 External Integrations

* federation systems
* calendars
* third-party APIs

Reason: introduces dependencies and failure points.

---

### 5.6 Media and File Storage

* photos
* videos
* documents

Reason: increases storage, privacy risk, and technical complexity.

---

### 5.7 Personal Data Expansion

* date of birth
* contact details
* health or injury data

Reason: privacy risk and not required for core functionality.

---

## 6. Functional Boundaries

### 6.1 Single-user focus

Version 1 assumes:

* one user manages one or more teams
* no concurrent editing
* no shared ownership

---

### 6.2 Web-only interface

* no mobile app
* responsive web design only

---

### 6.3 Manual workflow

* no automation
* no background processes
* no notifications

The system responds only to user actions.

---

## 7. Data Scope

### 7.1 Included data

* user account (email, password)
* team names
* player names
* match identifiers (opponent, date)
* attendance status

### 7.2 Excluded data

* sensitive personal data
* behavioral tracking
* free-text notes
* metadata not directly needed

---

## 8. Technical Scope

### 8.1 Stack

* PHP (no frameworks)
* MySQL
* JavaScript (vanilla)
* HTML / CSS

### 8.2 Architecture constraints

* simple, modular structure
* no external dependencies unless strictly necessary
* no build tools required

---

## 9. Complexity Guardrails

To protect the scope, the following rules apply:

* No feature is added without removing or postponing something else
* If a feature introduces roles or permissions, it is postponed
* If a feature requires storing additional personal data, it is postponed
* If a feature cannot be explained in one sentence, it is likely too complex

---

## 10. Definition of Done (Version 1)

Version 1 is complete when:

* a user can log in
* a user can create a team
* a user can add players
* a user can create a match
* a user can select players for that match
* all actions are persisted and retrievable
* access is restricted per user
* the system works without manual database intervention

---

## 11. Known Future Pressure Points

Even within this scope, certain features will create pressure:

* visual lineup (strong user desire)
* multiple users per team
* richer player data
* statistics

These are not part of Version 1, but should be acknowledged early.

---

## 12. Guiding Question

Throughout development:

> Does this directly help the coach prepare or manage a match?

If the answer is no, it does not belong in Version 1.

---

## Closing Reflection

There is a predictable pattern in projects like this.

The first version feels “too simple.”
Then features are added to make it “more useful.”
And slowly, the original clarity disappears.

The real challenge is not deciding what to build.
It is deciding what to leave out, even when it feels useful.

Where do you feel the strongest pull to add something that is not strictly necessary?
