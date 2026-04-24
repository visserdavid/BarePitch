# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

**Start development server**
```bash
php -S localhost:8000 -t public
```

**Run migrations** (execute in order; no migration runner exists)
```bash
mysql -u barepitch_user -p barepitch_local < database/migrations/001_create_users_table.sql
# repeat for 002–005
```

**Run seed**
```bash
mysql -u barepitch_user -p barepitch_local < database/seeds/dev_seed.sql
```

**Generate a bcrypt hash** (e.g. for seed data)
```bash
php -r "echo password_hash('PlainPassword', PASSWORD_DEFAULT);"
```

There are no build steps, no package manager, no test runner, and no linter. PHP syntax errors surface immediately in the browser or server output.

## Architecture

**Stack:** Plain PHP 8.2+, MySQL 8 via PDO, HTML/CSS, vanilla JS. No Composer, no frameworks, no external libraries.

**Web root:** Only `public/` is exposed. Every page in `public/` is a controller — it requires `bootstrap.php`, does its logic, then calls `render()`.

**Bootstrap chain:** `public/*.php` → `app/config/bootstrap.php` → parses `.env` manually, sets error reporting, starts session, then requires `database.php`, `view.php`, `auth.php`, `csrf.php` in that order. Everything downstream relies on this chain being loaded first.

**Database access:** `app/config/database.php` creates a PDO instance and stores it in `$GLOBALS['_pdo']`. All code accesses it via `getPdo()`. Models receive the connection through this getter — never via a `global` keyword or constructor injection.

**Request flow:**
```
public/foo.php
  └── bootstrap.php (env, session, helpers)
      └── database.php (PDO via getPdo())
  └── optional: require AuthService.php / UserModel.php
  └── requireLogin() / validateCsrf() guards
  └── business logic
  └── render('path/to/view', $data)
      └── app/views/path/to/view.php
          ├── include layouts/header.php
          └── include layouts/footer.php
```

**Views:** `render(string $view, array $data)` resolves to `app/views/{$view}.php` and extracts `$data` into local variables. Views include `layouts/header.php` and `layouts/footer.php` directly — there is no template inheritance. All output must go through `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.

**Auth:** Session-based. `requireLogin()` redirects to `/login.php` when `$_SESSION['user_id']` is absent. `AuthService::isLoggedIn()` also enforces a 7200-second inactivity timeout. `session_regenerate_id(true)` is called on every successful login.

**CSRF:** Every POST must call `validateCsrf()` before processing. Forms include `<?= csrfField() ?>`. The token lives in `$_SESSION['csrf_token']`.

## Conventions

- All user-facing output: `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`
- All DB queries: prepared statements via `getPdo()->prepare()`; no string concatenation in SQL
- Error messages shown to users are always neutral — never reveal whether an email exists or which field failed
- `.env` is parsed in `bootstrap.php`; use `getenv('KEY')` to read values everywhere else
- `APP_ENV=local` shows all PHP errors; any other value suppresses display and logs to `storage/logs/error.log`
- New models go in `app/models/`, services in `app/services/`, views in `app/views/`
- Migrations are plain `.sql` files numbered sequentially; there is no rollback mechanism

## Git Workflow

**Branch model**

```
main      stable, always runnable, production-ready
wip       daily development branch
spike/*   temporary experiments
backup/*  safety snapshots before risky changes
```

**Daily start**

```bash
git checkout wip
git status
```

**Before every session that touches existing code**

Create a checkpoint commit before making any changes:

```bash
git add .
git commit -m "checkpoint: before AI changes to <area>"
```

**Commit prefixes**

```
wip:        unfinished but useful progress
checkpoint: safe point before risky change
feat:       new working functionality
fix:        bug fix
refactor:   structure improvement without behavior change
security:   security improvement
docs:       documentation change
cleanup:    removal of temporary code or duplication
```

**After a feature works**

```bash
git add .
git commit -m "feat: <what now works>"
```

**Merge wip to main**

Only merge when: core flow works, no debug code remains, CSRF/auth/validation are present.

```bash
git checkout main
git merge wip
git tag v0.x.0
git push
git push --tags
git checkout wip
git merge main
```

**Version milestones**

```
v0.1.0  project structure and database connection
v0.2.0  authentication
v0.3.0  teams
v0.4.0  players
v0.5.0  matches
v0.6.0  attendance
v0.7.0  responsive cleanup
v0.8.0  security cleanup
v1.0.0  production-ready release
```

**GitHub CLI — useful commands**

```bash
gh repo view                  # open repository overview
gh issue list                 # list open issues
gh issue create               # create a new issue
gh issue close <number>       # close an issue
gh release create v0.x.0 --title "v0.x.0 <Title>" --notes "<Summary of changes>"
```

## Git Rules

These rules apply to every task in this project:

- Never commit `.env`, `*.log`, or anything inside `storage/logs/` or `storage/uploads/`
- Never commit directly to `main` — always work on `wip`
- Before modifying existing files, create a `checkpoint:` commit first
- After completing a feature, suggest the correct commit message using the prefix conventions above
- When a version milestone is complete, remind to: merge `wip` → `main`, create a version tag, push branch and tags
- When creating a GitHub release, use `gh release create` with a short summary of what changed
