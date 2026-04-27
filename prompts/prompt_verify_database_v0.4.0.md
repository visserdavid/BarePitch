# Prompt: Verify and fix database state up to v0.4.0

Follow all conventions in CLAUDE.md.

Do NOT make a checkpoint commit before this task — this is a diagnostic and repair task, not a code change.

---

## Context

The project is currently at v0.4.0 (player management). The local development environment uses Laragon on Windows with MySQL 8. The database may not have been set up correctly due to a PATH issue that has since been resolved. MySQL is now accessible from PowerShell.

Your job is to:
1. Check whether the database and all required tables exist
2. Check whether each table has the correct structure
3. Fix anything that is missing or incorrect by running the appropriate migrations
4. Load the seed data if the database is empty or nearly empty
5. Report exactly what was found and what was done

---

## Step 1: Check database existence

Run the following from the command line:

```bash
mysql -u barepitch_user -p -e "SHOW DATABASES LIKE 'barepitch_local';"
```

If the database does not exist, create it:

```bash
mysql -u root -p -e "CREATE DATABASE barepitch_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p -e "CREATE USER IF NOT EXISTS 'barepitch_user'@'localhost' IDENTIFIED BY 'your_password';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON barepitch_local.* TO 'barepitch_user'@'localhost'; FLUSH PRIVILEGES;"
```

Replace `your_password` with the value of `DB_PASS` from `.env`.

---

## Step 2: Check which tables exist

```bash
mysql -u barepitch_user -p barepitch_local -e "SHOW TABLES;"
```

As of v0.4.0, the following three tables must exist:

| Table | Introduced in |
|---|---|
| `users` | v0.2.0 (migration 001) |
| `teams` | v0.3.0 (migration 002) |
| `players` | v0.4.0 (migration 003) |

The tables `matches` and `match_players` are NOT required yet — they belong to v0.5.0 and v0.6.0.

---

## Step 3: Verify table structure

For each table that exists, verify its columns match the expected schema.

### users
```bash
mysql -u barepitch_user -p barepitch_local -e "DESCRIBE users;"
```

Expected columns:
- `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `email` VARCHAR(255) NOT NULL UNIQUE
- `password_hash` VARCHAR(255) NOT NULL
- `display_name` VARCHAR(100) NOT NULL
- `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
- `deleted_at` DATETIME NULL

### teams
```bash
mysql -u barepitch_user -p barepitch_local -e "DESCRIBE teams;"
```

Expected columns:
- `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `user_id` INT UNSIGNED NOT NULL
- `name` VARCHAR(100) NOT NULL
- `season` VARCHAR(20) NULL
- `status` ENUM('active','archived') NOT NULL DEFAULT 'active'
- `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
- `deleted_at` DATETIME NULL

Expected foreign key: `fk_teams_user` → `users(id)` ON DELETE CASCADE
Expected indexes: `idx_teams_user_id`, `idx_teams_status`

### players
```bash
mysql -u barepitch_user -p barepitch_local -e "DESCRIBE players;"
```

Expected columns:
- `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `team_id` INT UNSIGNED NOT NULL
- `display_name` VARCHAR(100) NOT NULL
- `shirt_number` TINYINT UNSIGNED NULL
- `status` ENUM('active','inactive') NOT NULL DEFAULT 'active'
- `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
- `deleted_at` DATETIME NULL

Expected foreign key: `fk_players_team` → `teams(id)` ON DELETE CASCADE
Expected indexes: `idx_players_team_id`, `idx_players_status`, `idx_players_shirt_number`

---

## Step 4: Run missing migrations

For each table that is missing, run the corresponding migration file.

**If `users` is missing:**
```bash
mysql -u barepitch_user -p barepitch_local < database/migrations/001_create_users_table.sql
```

**If `teams` is missing:**
```bash
mysql -u barepitch_user -p barepitch_local < database/migrations/002_create_teams_table.sql
```

**If `players` is missing:**
```bash
mysql -u barepitch_user -p barepitch_local < database/migrations/003_create_players_table.sql
```

Run migrations in order (001 → 002 → 003). Never skip one — foreign keys depend on earlier tables.

If a migration file does not exist in `database/migrations/`, create it now using the exact schema from Step 3 above before running it.

---

## Step 5: Check migration files on disk

Confirm that the following files exist in `database/migrations/`:

```
database/migrations/001_create_users_table.sql
database/migrations/002_create_teams_table.sql
database/migrations/003_create_players_table.sql
```

If any file is missing, create it with the correct schema from Step 3. These files must be committed to Git.

---

## Step 6: Check for seed data

```bash
mysql -u barepitch_user -p barepitch_local -e "SELECT COUNT(*) AS user_count FROM users;"
```

If the result is 0 (no users), load the seed data:

```bash
mysql -u barepitch_user -p barepitch_local < database/seeds/dev_seed.sql
```

If `database/seeds/dev_seed.sql` does not exist yet, do NOT create placeholder seed data. Report that the file is missing so it can be added separately.

---

## Step 7: Verify the connection from PHP

Check that `app/config/database.php` connects correctly by verifying the `.env` values match the database that now exists:

```bash
mysql -u barepitch_user -p barepitch_local -e "SELECT 1;"
```

If this succeeds, the PHP PDO connection using the same credentials will also work.

---

## Step 8: Report

After completing all steps, output a clear summary:

```
Database check complete — v0.4.0

Database:
  [✓ or ✗] barepitch_local exists

Tables:
  [✓ or ✗] users         (created by migration 001)
  [✓ or ✗] teams         (created by migration 002)
  [✓ or ✗] players       (created by migration 003)

Migration files on disk:
  [✓ or ✗] 001_create_users_table.sql
  [✓ or ✗] 002_create_teams_table.sql
  [✓ or ✗] 003_create_players_table.sql

Seed data:
  [✓ or ✗] Users in database: N

Actions taken:
  - List anything that was created or fixed

Remaining issues (if any):
  - List anything that could not be resolved automatically
```

Do not commit anything in this task. Report only. Any missing migration files that were created should be staged but not committed — the user will review and commit them.
