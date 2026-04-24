You are continuing the development of BarePitch, a minimal PHP/MySQL web application for football coaches. No frameworks — plain PHP, PDO, HTML, CSS, vanilla JavaScript only.

## Current state
- Phase 1 (bootstrap) and Phase 2 (database connection) are complete
- app/config/bootstrap.php, app/config/database.php, and all helpers exist
- .env is configured and loaded correctly
- Local server runs on localhost:8000

## Your task
Implement Phase 3 and Phase 4: the database migrations and the authentication flow.

---

## Phase 3: Database migrations

Create the following migration files in database/migrations/:

**001_create_users_table.sql**
```sql
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**002_create_teams_table.sql**
**003_create_players_table.sql**
**004_create_matches_table.sql**
**005_create_match_players_table.sql**

Use the exact schema from the project database design. Each file must be self-contained and runnable independently in order.

Also create **database/seeds/dev_seed.sql** with one fictional test user:
- email: coach@example.test
- display_name: Test Coach
- password: generate a bcrypt hash for the plain password `TestCoach2026` using PHP password_hash() and hardcode the result

---

## Phase 4: Authentication flow

Create the following files:

**app/models/UserModel.php**
- Method: findByEmail(string $email): ?array
- Method: findById(int $id): ?array
- Uses $pdo global, prepared statements, no raw SQL concatenation

**app/services/AuthService.php**
- Method: login(string $email, string $password): bool
  - Finds user by email
  - Verifies password with password_verify()
  - On success: calls session_regenerate_id(true), sets $_SESSION['user_id'] and $_SESSION['last_activity']
  - Returns true on success, false on failure
  - Never reveals whether the email exists (neutral error only)
- Method: logout(): void
  - Clears session data
  - Destroys session cookie
  - Calls session_destroy()
- Method: isLoggedIn(): bool
  - Checks $_SESSION['user_id'] is set
  - Checks session has not been inactive for more than 7200 seconds (2 hours)
  - Refreshes $_SESSION['last_activity'] on valid session

**app/views/layouts/header.php**
- Semantic HTML: <!DOCTYPE html>, <html>, <head>, <header>, <nav>
- Includes public/assets/css/base.css and public/assets/css/layout.css
- Navigation shows logout link when user is logged in

**app/views/layouts/footer.php**
- Closes open HTML tags from header

**app/views/auth/login.php**
- Semantic form with label for email, label for password
- CSRF hidden field using csrfField()
- Shows error message if $error is set
- No JavaScript required

**public/login.php**
- Require bootstrap
- If already logged in: redirect to dashboard.php
- On GET: render login view
- On POST: validate CSRF, validate inputs, call AuthService::login()
  - On success: redirect to dashboard.php
  - On failure: re-render login with neutral error message "Invalid email or password."
  - Never log passwords

**public/logout.php**
- Require bootstrap
- Require login (redirect if not logged in)
- Validate CSRF token (POST only)
- Call AuthService::logout()
- Redirect to login.php

**public/dashboard.php**
- Require bootstrap
- Call requireLogin()
- Show: "Welcome, [display_name]" and a logout form (POST with CSRF token)
- Keep it minimal — no team data yet

**public/assets/css/base.css**
- Reset and base typography
- Readable on mobile
- No decorative styling

**public/assets/css/layout.css**
- Page container, header, nav, main
- Mobile-first, max-width centered layout

---

## Rules
- All output in views must use htmlspecialchars() or an e() helper for escaping
- No plain SQL in public files
- Session settings must be applied before session_start() (httponly, samesite Lax, secure false for local)
- CSRF must be validated on every POST
- Neutral error messages only — never reveal whether an email exists
- Password minimum length: 10 characters (validate on login form server-side)
- After all files are created, output a checklist of what was created and flag anything that needs manual action (such as running the migrations)