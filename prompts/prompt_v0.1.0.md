You are helping build BarePitch, a minimal PHP/MySQL web application for football coaches to manage teams, players, matches, and attendance. No frameworks are used — plain PHP, MySQL via PDO, HTML, CSS, and vanilla JavaScript only.

## Current state
- Project folder structure already exists with: app/, database/, public/, storage/, docs/
- PHP 8.5 is installed
- MySQL 8.0 is installed and running
- A local database `barepitch_local` exists
- A `.env` file exists with correct DB credentials
- A local server runs via `php -S localhost:8000 -t public`

## Your task
Implement Phase 1 and Phase 2 of the build: project bootstrap and database connection.

### Create the following files:

**app/config/bootstrap.php**
- Load `.env` file from project root (parse key=value pairs manually, no external library)
- Set error reporting based on APP_ENV: show all errors when `local`, suppress display and log when not
- Start session with secure settings
- Include app/config/database.php
- Include app/helpers/view.php
- Include app/helpers/auth.php
- Include app/helpers/csrf.php

**app/config/database.php**
- Create a PDO connection using values from the loaded `.env`
- Settings: ERRMODE_EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES false, charset utf8mb4
- Store the connection in a variable `$pdo` that is accessible globally or via a simple getter function

**app/helpers/view.php**
- A helper function `render(string $view, array $data = [])` that includes a view file from app/views/
- Extract $data so variables are available in the view

**app/helpers/auth.php**
- A helper function `requireLogin()` that redirects to /login.php if the user session is not set
- A helper function `currentUserId()` that returns the user ID from the session or null

**app/helpers/csrf.php**
- A helper function `csrfToken()` that generates and stores a token in the session if not present, and returns it
- A helper function `csrfField()` that returns an HTML hidden input field with the token
- A helper function `validateCsrf()` that checks the POST token against the session token and returns bool

**public/index.php**
- Require bootstrap.php
- Redirect to /login.php

**storage/logs/.gitkeep**
- Empty file to ensure the logs directory is tracked by Git

**storage/uploads/.gitkeep**
- Empty file to ensure the uploads directory is tracked by Git

## Rules
- No Composer, no external libraries
- All database access must use prepared statements
- No sensitive values hardcoded — always read from environment
- Code must be clean, readable, and commented where intent is not obvious
- Follow this file structure strictly:
  - app/config/
  - app/helpers/
  - app/views/
  - app/models/
  - app/services/
  - public/
  - storage/logs/
  - storage/uploads/

After creating all files, confirm what was created and flag anything that requires manual attention.