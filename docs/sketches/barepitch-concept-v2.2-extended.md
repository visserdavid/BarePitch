# BarePitch — Concept Document

Version 2.2 — Extended — April 2026

---

## 1. Purpose

BarePitch is a lightweight web application for managing amateur football teams.

The system supports:

- team and season management
- training and match administration
- lineup management
- match events and statistics
- temporary livestream functionality

The application is designed for:

- fast input during matches
- minimal cognitive load
- predictable behavior
- mobile-first usage
- low-resource hosting environments

---

## 2. Core Design Principles

- Simplicity over completeness
- Speed over abstraction
- Explicit rules over implicit behavior
- Server-side logic over heavy client frameworks
- Minimal dependencies

---

## 3. Domain Model

### 3.1 Club

Top-level entity.

- Contains multiple teams
- Defines organizational boundary

---

### 3.2 Season

- Time container (e.g. 2025–2026)
- Contains teams, phases, matches, trainings

---

### 3.3 Phase

- Subdivision of season
- Represents competition periods
- Teams remain unchanged within a season

---

### 3.4 Team

- Belongs to one club and one season
- Recreated every season
- Name is editable

---

### 3.5 User

- Can belong to multiple teams
- Roles assigned per team
- No role = no access

---

### 3.6 Roles

Administrator:

- full system control

Trainer:

- training management

Coach:

- match control
- lineup management

Team Manager:

- administrative support

---

### 3.7 Player

- Persistent identity
- Exists across seasons

---

### 3.8 Player Season Context

Each player has exactly one record per season:

- linked to a team OR
- external (no team)

---

### 3.9 Guest Players

Internal:

- from another team

External:

- no team

Guest status is match-based.

---

## 4. Match Model

### 4.1 Match Structure

- belongs to team, season, phase
- opponent stored as text
- contains lineup, events, stats

---

### 4.2 Match Status Flow

- planned
- prepared
- active
- finished

---

### 4.3 Planned → Prepared

Requirements:

- minimum 11 players
- maximum players (default 18)
- formation selected
- all starting positions filled

---

### 4.4 Prepared → Active

- match starts immediately
- score = 0–0
- livestream starts
- events enabled

---

### 4.5 Active Phases

Regular Time:

- two halves

Extra Time:

- two halves
- configurable duration

Penalty Shootout:

- separate system

---

### 4.6 Match Completion

After regular time:

- finish
- extra time
- penalties

After extra time:

- finish
- penalties

After penalties:

- finish only

---

## 5. Match Events

### 5.1 Goals

Counted:

- regular play
- extra time
- penalties during match

---

### 5.2 Penalties

- outcome: scored/missed
- stored as event

---

### 5.3 Penalty Shootout

- separate structure
- sequential attempts
- supports sudden death
- does not affect match score

---

### 5.4 Assists

- only for regular goals
- optional
- editable after match

---

## 6. Statistics Rules

Goals:

- match + extra time included
- shootout excluded

Assists:

- optional
- only regular goals

---

## 7. Attendance

Statuses:

- present
- absent
- injured

Injured:

- excluded from calculations

---

## 8. Playing Time

- stored in seconds
- displayed in minutes

Rules:

- starts at kickoff or substitution
- stops at substitution or red card
- extra time included

---

## 9. Red Card

- player leaves field
- cannot return
- playing time stops

---

## 10. Livestream

Start:

- at match start

During:

- shows live data

After:

- remains active
- default 24h
- max 72h

Behavior:

- reflects corrections
- can be stopped manually

---

## 11. Corrections

Allowed after finish:

- coach
- administrator

---

## 12. Logging

Each change records:

- user
- timestamp
- old value
- new value

---

## 13. Concurrency

- editing lock applied
- prevents simultaneous edits

---

## 14. Internationalization

- UI text language-based
- user-defined language
- fallback language required

---

## 15. Constraints

Not supported:

- player photos
- realtime sockets
- push notifications
- external integrations

---

## End
