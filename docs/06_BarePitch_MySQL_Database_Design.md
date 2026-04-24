# BarePitch – MySQL Database Design

## 1. Purpose

This document describes the MySQL database design for BarePitch Version 1.

The goal is a small, clear and safe database structure that supports the first usable version of the application without storing unnecessary personal data.

Version 1 supports:

user accounts
teams
players
matches
attendance and selection

## 2. Design Principles

The database follows these principles:

Store only what is needed.
Keep personal data minimal.
Use clear relationships.
Avoid free-text fields unless necessary.
Preserve match history where possible.
Use ownership checks through relational structure.
Prefer archiving or inactive status over unsafe deletion.

## 3. Core Entities

BarePitch Version 1 needs five core tables:

```text
users
teams
players
matches
match_players
```

The table `match_players` connects players to matches and stores their match-specific status.

## 4. Relationship Overview

```text
users
  └── teams
        ├── players
        └── matches
              └── match_players
                    └── players
```

Meaning:

One user can have many teams.
One team can have many players.
One team can have many matches.
One match can have many selected or tracked players.
One player can appear in many matches.

## 5. Users Table

The `users` table stores account data.

```sql
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Notes:

`email` is used for login.
`password_hash` stores the hashed password, never the original password.
`display_name` is used in the interface.
`deleted_at` allows soft deletion.

No unnecessary profile data is stored.

## 6. Teams Table

The `teams` table stores teams owned by a user.

```sql
CREATE TABLE teams (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    season VARCHAR(20) NULL,
    status ENUM('active', 'archived') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,

    CONSTRAINT fk_teams_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_teams_user_id (user_id),
    INDEX idx_teams_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Notes:

A team belongs to one user.
`season` is optional but useful, for example `2026/2027`.
`status` allows teams to be archived instead of deleted.

Recommended rule:

A user should not have two active teams with the same name and season.

Optional index:

```sql
CREATE UNIQUE INDEX uq_teams_user_name_season_active
ON teams (user_id, name, season, status);
```

Be careful: with `status` included, archived duplicates may still be possible depending on intended behavior.

## 7. Players Table

The `players` table stores the player pool for a team.

```sql
CREATE TABLE players (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_id INT UNSIGNED NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    shirt_number TINYINT UNSIGNED NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,

    CONSTRAINT fk_players_team
        FOREIGN KEY (team_id)
        REFERENCES teams(id)
        ON DELETE CASCADE,

    INDEX idx_players_team_id (team_id),
    INDEX idx_players_status (status),
    INDEX idx_players_shirt_number (shirt_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Notes:

`display_name` is enough for Version 1.
`shirt_number` is useful but optional.
No date of birth, address, email, phone number or medical information is stored.
`inactive` is preferred over deletion if the player has match history.

Possible future improvement:

A separate player identity table may be needed later if players move between teams or seasons.

For Version 1, keep it simple.

## 8. Matches Table

The `matches` table stores matches for a team.

```sql
CREATE TABLE matches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_id INT UNSIGNED NOT NULL,
    opponent_name VARCHAR(100) NOT NULL,
    match_date DATE NOT NULL,
    kickoff_time TIME NULL,
    location VARCHAR(150) NULL,
    home_away ENUM('home', 'away', 'neutral') NULL,
    status ENUM('planned', 'completed', 'archived') NOT NULL DEFAULT 'planned',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,

    CONSTRAINT fk_matches_team
        FOREIGN KEY (team_id)
        REFERENCES teams(id)
        ON DELETE CASCADE,

    INDEX idx_matches_team_id (team_id),
    INDEX idx_matches_match_date (match_date),
    INDEX idx_matches_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Notes:

`opponent_name` and `match_date` are required.
`kickoff_time`, `location` and `home_away` are useful but not essential.
`status` keeps the match lifecycle simple.

Version 1 does not include scores, competition phases or detailed statistics.

## 9. Match Players Table

The `match_players` table connects players to matches and stores availability, selection and attendance.

```sql
CREATE TABLE match_players (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    match_id INT UNSIGNED NOT NULL,
    player_id INT UNSIGNED NOT NULL,
    status ENUM('unknown', 'available', 'unavailable', 'selected', 'present', 'absent') NOT NULL DEFAULT 'unknown',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,

    CONSTRAINT fk_match_players_match
        FOREIGN KEY (match_id)
        REFERENCES matches(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_match_players_player
        FOREIGN KEY (player_id)
        REFERENCES players(id)
        ON DELETE RESTRICT,

    CONSTRAINT uq_match_players_match_player
        UNIQUE (match_id, player_id),

    INDEX idx_match_players_match_id (match_id),
    INDEX idx_match_players_player_id (player_id),
    INDEX idx_match_players_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Notes:

Each player can have only one status per match.
`ON DELETE RESTRICT` prevents deleting a player who has match history.
If a player leaves the team, set status to `inactive` instead.

Recommended Version 1 statuses:

```text
unknown
available
unavailable
selected
```

The additional statuses `present` and `absent` may be useful soon, but can also be postponed.

A stricter Version 1 design would use only:

```sql
status ENUM('unknown', 'available', 'unavailable', 'selected')
```

My advice: include `present` and `absent` now only if you genuinely expect to record attendance after the match. Otherwise keep the enum smaller.

## 10. Ownership and Access Control

Ownership is established through this chain:

```text
user owns team
team owns players
team owns matches
match owns match_players
```

Every query must respect this chain.

Example: fetching a match for a logged-in user should not query only by match ID.

Unsafe pattern:

```sql
SELECT * FROM matches WHERE id = :match_id;
```

Safer pattern:

```sql
SELECT m.*
FROM matches m
JOIN teams t ON t.id = m.team_id
WHERE m.id = :match_id
  AND t.user_id = :user_id
  AND m.deleted_at IS NULL
  AND t.deleted_at IS NULL;
```

This prevents a user from changing the URL and opening another user’s match.

## 11. Soft Deletion Strategy

BarePitch should avoid hard deletion where history matters.

Use `deleted_at` for:

users
teams
players
matches

For `match_players`, hard deletion is acceptable if the match itself is deleted, because it is dependent data.

Practical rules:

Archive teams before deleting them.
Set players to inactive when they leave.
Archive matches when they should no longer appear.
Only permanently delete when there is no meaningful history or when required for privacy reasons.

## 12. Timestamps

Every main table includes:

```text
created_at
updated_at
deleted_at
```

Use:

`created_at` for creation time.
`updated_at` for last meaningful change.
`deleted_at` for soft deletion.

MySQL does not automatically update `updated_at` unless configured.

Possible pattern:

```sql
updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
```

However, be careful: this updates on every database change, even minor technical ones. That may be fine for BarePitch.

## 13. Indexing

Indexes should support common queries.

Expected queries:

find user by email;
find teams by user;
find players by team;
find active players by team;
find matches by team and date;
find match players by match;
check ownership through joins.

Essential indexes:

```text
users.email
teams.user_id
players.team_id
matches.team_id
matches.match_date
match_players.match_id
match_players.player_id
```

Do not over-index too early. Indexes help reads but add overhead to writes.

## 14. Data Minimization

Version 1 should not store:

birth dates
addresses
phone numbers
email addresses of players
medical information
behavioral notes
photos
documents
free-text evaluations

This is both a privacy decision and a product decision.

BarePitch should remain a coaching support tool, not a player dossier.

## 15. Example Migration Order

Recommended migration files:

```text
001_create_users_table.sql
002_create_teams_table.sql
003_create_players_table.sql
004_create_matches_table.sql
005_create_match_players_table.sql
```

Use this order because foreign keys depend on earlier tables.

## 16. Optional Seed Data

For local development only:

```sql
INSERT INTO users (email, password_hash, display_name)
VALUES (
    'coach@example.test',
    '$2y$10$examplehash',
    'Test Coach'
);
```

Do not use real player names in seed data.

Use fictional data only.

## 17. Example Query: Team Overview

```sql
SELECT
    t.id,
    t.name,
    t.season,
    COUNT(DISTINCT p.id) AS player_count,
    COUNT(DISTINCT m.id) AS match_count
FROM teams t
LEFT JOIN players p
    ON p.team_id = t.id
    AND p.deleted_at IS NULL
LEFT JOIN matches m
    ON m.team_id = t.id
    AND m.deleted_at IS NULL
WHERE t.user_id = :user_id
  AND t.deleted_at IS NULL
  AND t.status = 'active'
GROUP BY t.id, t.name, t.season
ORDER BY t.name ASC;
```

## 18. Example Query: Match Detail with Players

```sql
SELECT
    p.id,
    p.display_name,
    p.shirt_number,
    COALESCE(mp.status, 'unknown') AS match_status
FROM players p
LEFT JOIN match_players mp
    ON mp.player_id = p.id
    AND mp.match_id = :match_id
WHERE p.team_id = :team_id
  AND p.status = 'active'
  AND p.deleted_at IS NULL
ORDER BY p.shirt_number IS NULL, p.shirt_number, p.display_name;
```

Before running this, the application must confirm that the match and team belong to the logged-in user.

## 19. Example Upsert for Match Player Status

```sql
INSERT INTO match_players (match_id, player_id, status)
VALUES (:match_id, :player_id, :status)
ON DUPLICATE KEY UPDATE
    status = VALUES(status),
    updated_at = CURRENT_TIMESTAMP;
```

This works because of:

```sql
UNIQUE (match_id, player_id)
```

Important:

Before saving, the application must verify that the player belongs to the same team as the match.

## 20. Deletion and Privacy Requests

If a user requests deletion, the system must be able to remove their data.

For a full account deletion:

teams are deleted through cascade;
players are deleted through cascade;
matches are deleted through cascade;
match_players are deleted through cascade through matches.

Because `match_players.player_id` uses `ON DELETE RESTRICT`, full user deletion may require deleting matches first or adjusting foreign key behavior.

For simplicity, if full deletion must be supported technically, consider:

```sql
FOREIGN KEY (player_id)
REFERENCES players(id)
ON DELETE CASCADE
```

Trade-off:

`RESTRICT` protects history during normal use.
`CASCADE` makes deletion easier.

For BarePitch Version 1, I would choose `RESTRICT` during normal application use, and handle full account deletion through a controlled deletion script.

## 21. Suggested Final Version 1 Schema

Use these tables:

```text
users
teams
players
matches
match_players
```

Avoid these for now:

roles
permissions
clubs
seasons as separate table
competitions
statistics
audit logs
player profiles
contacts
uploads

They may be useful later, but they do not belong in the first database version.

## 22. Technical Definition of Done

The database design is ready when:

all required tables exist;
foreign keys are defined;
ownership can be enforced through joins;
personal data is minimal;
migrations are versioned;
indexes support common queries;
deletion behavior is understood;
seed data uses fictional information;
queries use prepared statements in PHP;
no application logic depends on hidden or unvalidated IDs.

## 23. Guiding Database Question

Every database field should be tested against this question:

Does BarePitch need this data to help the coach prepare or manage a match?

If not, do not store it.

The database should protect the product from becoming larger than its purpose.
