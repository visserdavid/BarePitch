# BarePitch — SQL DDL and Performance Notes

**Version 1.0 — April 2026**

## 1. Purpose

This document accompanies the SQL DDL for BarePitch.

The schema is designed for:

- shared hosting
- MySQL 8.0+
- PHP + PDO
- predictable write behavior
- fast match, livestream, and dashboard reads

## 2. Key Design Decisions

### Player season context

A player has exactly one season context per season.

This means:

- one player
- one season
- one team or no team

`team_id = NULL` represents an external guest player.

### Cached score fields on match

The columns below are cached aggregates:

- `goals_scored`
- `goals_conceded`
- `shootout_goals_scored`
- `shootout_goals_conceded`

The source of truth remains:

- `match_event`
- `penalty_shootout_attempt`

During normal live input:

- update cached values transactionally

After corrections on finished matches:

- recalculate cached values from source tables

### Locking

`match.locked_by_user_id` and `match.locked_at` support optimistic operational locking at application level.

Recommended behavior:

- acquire lock when entering edit mode
- refresh lock timestamp while active
- release on save, cancel, or timeout

### No ORM-first design

The schema is optimized for direct SQL through PDO.
This is intentional for:

- lower overhead
- better control on shared hosting
- easier profiling

## 3. Performance Recommendations

### Frequently read screens

Use cached match aggregates for:

- match list
- dashboard
- livestream header

Do not calculate score from raw events on every read.

### Recommended query pattern

For heavy read screens:

- fetch match row first
- fetch related collections separately
- avoid deep join trees for mobile views

### Index strategy

Indexes are added for:

- team/date navigation
- match event ordering
- shootout ordering
- audit lookups
- uniqueness rules

### Recalculation rule

After editing a finished match:

1. recalculate regular goals from `match_event`
2. recalculate shootout goals from `penalty_shootout_attempt`
3. update cached columns on `match`

## 4. What is intentionally not in the database

The schema does not store:

- translated UI text
- player photos
- derived season statistics tables
- historical lineup snapshots
- websocket/session state

## 5. Files

This package contains:

- `BarePitch-schema-v1.0.sql`
- `BarePitch-sql-notes-v1.0.md`
