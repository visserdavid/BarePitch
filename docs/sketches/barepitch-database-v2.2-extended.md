# BarePitch — Database Structure

Version 2.2 — Extended — April 2026

---

## 1. Overview

This document defines the full database schema for BarePitch.

Design goals:

- predictable structure
- minimal redundancy
- optimized for shared hosting
- clear separation of concerns
- fast read operations

---

## 2. Core Principles

- Single source of truth = event tables
- Aggregates stored for performance only
- One player per season context
- No unnecessary joins for frequent queries
- All relations explicit via foreign keys

---

## 3. Tables

---

### club

| Column     | Type         | Null | Notes |
| ---------- | ------------ | ---- | ----- |
| id         | INT UNSIGNED | No   | PK    |
| name       | VARCHAR(150) | No   |       |
| active     | TINYINT(1)   | No   |       |
| created_at | TIMESTAMP    | No   |       |
| updated_at | TIMESTAMP    | No   |       |

---

### season

| Column     | Type         | Null | Notes  |
| ---------- | ------------ | ---- | ------ |
| id         | INT UNSIGNED | No   | PK     |
| label      | VARCHAR(20)  | No   | UNIQUE |
| starts_on  | DATE         | No   |        |
| ends_on    | DATE         | No   |        |
| created_at | TIMESTAMP    | No   |        |

---

### phase

| Column    | Type         | Null | Notes |
| --------- | ------------ | ---- | ----- |
| id        | INT UNSIGNED | No   | PK    |
| season_id | INT UNSIGNED | No   | FK    |
| number    | TINYINT      | No   |       |
| label     | VARCHAR(100) | No   |       |

---

### team

| Column                       | Type         | Null | Notes      |
| ---------------------------- | ------------ | ---- | ---------- |
| id                           | INT UNSIGNED | No   | PK         |
| club_id                      | INT UNSIGNED | No   | FK         |
| season_id                    | INT UNSIGNED | No   | FK         |
| name                         | VARCHAR(100) | No   |            |
| max_match_players            | TINYINT      | No   | Default 18 |
| livestream_hours_after_match | TINYINT      | No   | Default 24 |

---

### user

| Column           | Type         | Null | Notes  |
| ---------------- | ------------ | ---- | ------ |
| id               | INT UNSIGNED | No   | PK     |
| email            | VARCHAR(255) | No   | UNIQUE |
| locale           | VARCHAR(10)  | No   |        |
| is_administrator | TINYINT(1)   | No   |        |

---

### user_team_role

| Column   | Type        | Null | Notes |
| -------- | ----------- | ---- | ----- |
| id       | INT         | No   | PK    |
| user_id  | INT         | No   | FK    |
| team_id  | INT         | No   | FK    |
| role_key | VARCHAR(30) | No   |       |

---

### player

| Column     | Type         | Null | Notes |
| ---------- | ------------ | ---- | ----- |
| id         | INT          | No   | PK    |
| first_name | VARCHAR(100) | No   |       |
| last_name  | VARCHAR(100) | Yes  |       |

---

### player_season_context

| Column    | Type | Null | Notes           |
| --------- | ---- | ---- | --------------- |
| id        | INT  | No   | PK              |
| player_id | INT  | No   | FK              |
| season_id | INT  | No   | FK              |
| team_id   | INT  | Yes  | NULL = external |

UNIQUE(player_id, season_id)

---

### match

| Column                  | Type         | Null | Notes  |
| ----------------------- | ------------ | ---- | ------ |
| id                      | INT          | No   | PK     |
| team_id                 | INT          | No   | FK     |
| phase_id                | INT          | No   | FK     |
| opponent                | VARCHAR(150) | No   |        |
| status                  | VARCHAR(10)  | No   |        |
| active_phase            | VARCHAR(20)  | Yes  |        |
| goals_scored            | TINYINT      | No   | cached |
| goals_conceded          | TINYINT      | No   | cached |
| shootout_goals_scored   | TINYINT      | No   | cached |
| shootout_goals_conceded | TINYINT      | No   | cached |

---

### match_selection

| Column            | Type        | Null | Notes |
| ----------------- | ----------- | ---- | ----- |
| id                | INT         | No   | PK    |
| match_id          | INT         | No   | FK    |
| player_id         | INT         | No   | FK    |
| season_context_id | INT         | Yes  | FK    |
| is_guest          | TINYINT     | No   |       |
| guest_type        | VARCHAR(20) | Yes  |       |

---

### match_event

| Column       | Type        | Null | Notes |
| ------------ | ----------- | ---- | ----- |
| id           | INT         | No   | PK    |
| match_id     | INT         | No   | FK    |
| match_second | INT         | No   |       |
| event_type   | VARCHAR(15) | No   |       |
| team_side    | VARCHAR(10) | No   |       |

---

### penalty_shootout_attempt

| Column        | Type        | Null | Notes |
| ------------- | ----------- | ---- | ----- |
| id            | INT         | No   | PK    |
| match_id      | INT         | No   | FK    |
| attempt_order | TINYINT     | No   |       |
| outcome       | VARCHAR(10) | No   |       |

---

### audit_log

| Column             | Type         | Null | Notes |
| ------------------ | ------------ | ---- | ----- |
| id                 | BIGINT       | No   | PK    |
| entity_type        | VARCHAR(50)  | No   |       |
| entity_id          | INT          | No   |       |
| changed_by_user_id | INT          | No   |       |
| field_name         | VARCHAR(100) | No   |       |
| old_value          | TEXT         | Yes  |       |
| new_value          | TEXT         | Yes  |       |

---

## 4. Rules

### Player Integrity

One player per season.

### Score Integrity

Events = source of truth.

### Locking

Match editing is exclusive.

---

## End
