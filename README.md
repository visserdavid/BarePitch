# BarePitch

> *BarePitch shows what matters, when it matters. Nothing more.*

BarePitch is a minimal web application for amateur football coaches. It removes the clutter of existing tools and provides only what a coach actually needs: a clear overview of teams, players, matches and attendance.

---

## Stack

- PHP 8.2+
- MySQL 8.x
- Vanilla JavaScript
- HTML / CSS

No frameworks. No unnecessary dependencies.

---

## Project Structure

```
barepitch/
├── app/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   ├── services/
│   ├── views/
│   └── helpers/
├── database/
│   ├── migrations/
│   └── seeds/
├── public/          ← only this directory is web-accessible
│   └── assets/
├── storage/
│   ├── logs/
│   └── uploads/
├── docs/
├── .env.example
├── .gitignore
├── CHANGELOG.md
└── README.md
```

---

## Local Setup

1. Clone the repository

   ```powershell
   git clone https://github.com/visserdavid/BarePitch.git
   cd barepitch
   ```

2. Copy the environment file and configure it

   ```powershell
   cp .env.example .env
   ```

   Open `.env` and fill in your local database credentials.

3. Create a local database

   ```sql
   CREATE DATABASE barepitch_local
   CHARACTER SET utf8mb4
   COLLATE utf8mb4_unicode_ci;
   ```

4. Run migrations

   Execute the SQL files in `database/migrations/` in order.

5. Start the local server

   ```powershell
   php -S localhost:8000 -t public
   ```

6. Open the application

   ```
   http://localhost:8000
   ```

---

## Environment Variables

See `.env.example` for all required variables. Never commit `.env` to Git.

Key variables:

| Variable | Description |
|---|---|
| `APP_ENV` | `local` or `production` |
| `APP_DEBUG` | `true` in development, `false` in production |
| `DB_HOST` | Database host |
| `DB_NAME` | Database name |
| `DB_USER` | Database user |
| `DB_PASS` | Database password |
| `SESSION_NAME` | Session identifier |

---

## Scope (Version 1)

What BarePitch includes:

- Secure login and session management
- Team management
- Player management
- Match management
- Attendance tracking per match

What BarePitch deliberately excludes:

- Statistics and analytics
- Communication features
- Financial or administrative modules
- External integrations
- File or media uploads
- Complex role and permission systems

---

## Git Workflow

| Branch | Purpose |
|---|---|
| `main` | Stable, tested code only |
| `wip` | Daily development |
| `spike/*` | Experiments |
| `backup/*` | Safety points before risky changes |

Development always happens on `wip`. Only tested, coherent code is merged to `main`.

---

## Version

Current version: `v0.0.1`  
See [CHANGELOG.md](CHANGELOG.md) for history.

---

## Philosophy

Most tools try to do everything. As a result, they dilute focus.

BarePitch does one thing: help a coach manage a match without switching tools or losing overview. Every feature is tested against one question:

> Does this help the coach in the moment, or does it add noise?

If it adds noise, it does not belong in BarePitch.
