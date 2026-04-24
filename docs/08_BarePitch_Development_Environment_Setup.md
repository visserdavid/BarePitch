# BarePitch – Development Environment Setup

## 1. Purpose

This document describes how to set up and maintain the development environment for BarePitch.

The goal is not only to “make it run,” but to create a setup that is:

predictable
repeatable
safe
close enough to production
easy to understand later

A good environment reduces friction during development and prevents subtle bugs when moving to production.

---

## 2. Guiding Principles

The development environment follows these principles:

Local-first development.
Everything must run locally without external dependencies.

Environment isolation.
Local settings must not affect production and vice versa.

Reproducibility.
Another developer, or you in three months, should be able to recreate the setup.

Minimal tooling.
No unnecessary complexity or heavy toolchains.

Transparency.
Configuration is visible and understandable, not hidden in layers.

---

## 3. Required Software

BarePitch Version 1 requires:

PHP (recommended 8.2 or higher)
MySQL or MariaDB
Web server (Apache or Nginx)
Git
A code editor

Optional but recommended:

Composer
A local HTTPS tool (for realistic testing)

### 3.1 PHP

Requirements:

```text
PHP 8.2+
```

Required extensions:

```text
pdo
pdo_mysql
mbstring
openssl
json
session
```

Verify installation:

```bash
php -v
php -m
```

---

### 3.2 MySQL

Requirements:

```text
MySQL 8.x or MariaDB equivalent
```

Verify:

```bash
mysql --version
```

---

### 3.3 Web Server

Two common options:

Apache
Nginx

For simplicity, Apache is often easier in early stages.

Alternative:

Use PHP’s built-in server for initial development:

```bash
php -S localhost:8000 -t public
```

This is acceptable for development, not for production.

---

### 3.4 Git

Verify:

```bash
git --version
```

Initialize repository:

```bash
git init
```

---

### 3.5 Code Editor

Use an editor that supports:

PHP syntax highlighting
basic linting
search across files

Examples:

VS Code
PhpStorm
Sublime Text

---

## 4. Project Initialization

Create the project:

```bash
mkdir barepitch
cd barepitch
git init
```

Create base structure:

```text
app/
database/
public/
storage/
docs/
```

Add base files:

```text
README.md
CHANGELOG.md
.env.example
.gitignore
```

First commit:

```bash
git add .
git commit -m "chore: initialize BarePitch project structure"
```

---

## 5. Environment Configuration

### 5.1 .env File

Create a local `.env` file.

Example:

```text
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=barepitch_local
DB_USER=root
DB_PASS=

SESSION_NAME=barepitch_session
```

### 5.2 .env.example

Provide a template:

```text
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_HOST=localhost
DB_PORT=3306
DB_NAME=barepitch
DB_USER=
DB_PASS=

SESSION_NAME=barepitch_session
```

### 5.3 Git Ignore

Ensure `.env` is not committed:

```gitignore
.env
/vendor/
/node_modules/
/storage/logs/*
/storage/uploads/*
*.log
```

---

## 6. Database Setup

Create a local database:

```sql
CREATE DATABASE barepitch_local
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Create a database user (recommended):

```sql
CREATE USER 'barepitch_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON barepitch_local.* TO 'barepitch_user'@'localhost';
FLUSH PRIVILEGES;
```

Update `.env` accordingly.

---

## 7. Database Migrations

Create migration files:

```text
database/migrations/
```

Example:

```text
001_create_users_table.sql
002_create_teams_table.sql
003_create_players_table.sql
004_create_matches_table.sql
005_create_match_players_table.sql
```

Run them manually or via a simple script.

Important rule:

Database structure must always be reproducible from migrations.

---

## 8. Running the Application

### 8.1 Using PHP Built-in Server

```bash
php -S localhost:8000 -t public
```

Then open:

```text
http://localhost:8000
```

### 8.2 Using Apache

Set document root to:

```text
/path/to/barepitch/public
```

Ensure:

`.env` is not publicly accessible
directory listing is disabled

---

## 9. Basic Bootstrap File

Create a central bootstrap file:

```text
app/config/bootstrap.php
```

Responsibilities:

load `.env`
configure error reporting
start session
load database connection
load helper functions

Each public file should include this:

```php
require __DIR__ . '/../app/config/bootstrap.php';
```

---

## 10. Error Reporting

### Development

Enable full error reporting:

```php
error_reporting(E_ALL);
ini_set('display_errors', '1');
```

### Production

Disable display:

```php
ini_set('display_errors', '0');
```

Log errors instead.

Use environment variable:

```php
if ($env === 'local') {
    // show errors
} else {
    // hide errors
}
```

---

## 11. Logging

Create log directory:

```text
storage/logs/
```

Example log file:

```text
storage/logs/app.log
```

Ensure it is writable.

Never commit logs to Git.

---

## 12. Local HTTPS (Optional but Recommended)

Testing with HTTPS helps detect issues early.

Options:

local reverse proxy
tools like mkcert

This allows testing:

secure cookies
SameSite behavior
mixed content issues

---

## 13. Development Workflow Integration

Tie environment to Git workflow.

Daily flow:

```bash
git checkout wip
git status
```

Start server:

```bash
php -S localhost:8000 -t public
```

Work, then:

```bash
git add .
git commit -m "wip: implement player list"
```

---

## 14. Data for Development

Use safe, fictional data.

Never use:

real player names
real emails
real team data

Example:

```text
Team: Test United
Players: Player One, Player Two
Opponent: Sample FC
```

---

## 15. Environment Differences

Understand differences between local and production:

Local:

debug on
less strict security
simpler setup

Production:

debug off
HTTPS required
secure cookies
restricted access
real data

The environment must make this distinction explicit.

---

## 16. Backup Awareness

Even in development, understand backup flow.

At minimum:

export database periodically
know how to import it again

Example:

```bash
mysqldump -u root barepitch_local > backup.sql
```

Restore:

```bash
mysql -u root barepitch_local < backup.sql
```

---

## 17. Common Pitfalls

Typical mistakes in small projects:

working directly on production
hardcoding database credentials
not using `.env`
mixing environments
no migration history
forgetting to commit structure
using real data locally
ignoring file permissions
not testing fresh setup

The environment setup should prevent these.

---

## 18. Environment Validation Checklist

Before starting development, confirm:

PHP runs correctly
database connection works
migrations can be executed
application loads without fatal errors
logs can be written
`.env` is loaded
Git is initialized
public directory is accessible
non-public directories are protected

---

## 19. Reproducibility Test

A simple but powerful check:

Delete your local database and recreate it from migrations.

If this fails, your setup is incomplete.

Another check:

Clone the repository into a new folder and set it up from scratch.

If this fails, your documentation is insufficient.

---

## 20. Definition of Done for Environment Setup

The development environment is complete when:

the application runs locally
database can be created from migrations
configuration is externalized
sensitive data is not in Git
logs are written correctly
errors are visible in development
project can be cloned and started by someone else
structure is clear and consistent

---

## 21. Guiding Question

Every environment decision should be tested against this:

If I return to this project after three months, can I understand and run it within minutes?

If not, the environment is too fragile or too implicit.
