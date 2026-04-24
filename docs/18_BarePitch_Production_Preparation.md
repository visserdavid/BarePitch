# BarePitch – Production Preparation

## 1. Purpose

This document defines how BarePitch is prepared for production use.

Production is not just “putting code online.”
It is the moment where assumptions meet reality.

The goal is:

BarePitch must run safely, predictably and independently in a real environment, without relying on development shortcuts.

---

## 2. Core Principle

A system is production-ready when:

it can run without the developer being present;
it behaves consistently under normal use;
it fails safely under abnormal conditions;
it can be restored if something goes wrong.

Production preparation is about removing hidden dependencies.

---

## 3. Environment Separation

Production must be clearly separated from development.

Key differences:

```text
Development:
- APP_DEBUG = true
- local database
- relaxed security
- visible errors

Production:
- APP_DEBUG = false
- secure database access
- strict security
- hidden errors
```

This separation must be controlled through environment variables, not manual code changes.

---

## 4. Server Setup

BarePitch requires a stable server environment.

Minimum requirements:

PHP 8.2 or higher
MySQL or MariaDB
Web server (Apache or Nginx)
HTTPS support

Server must be configured so that:

only the `public/` directory is accessible via the web;
all other directories are protected;
directory listing is disabled;
error display is disabled in production.

---

## 5. Directory Security

The following directories must not be publicly accessible:

```text
app/
database/
storage/
docs/
```

Sensitive files must be protected:

```text
.env
.git/
logs
backups
```

Test by attempting to access:

```text
/.env
/app/config/database.php
/storage/logs/app.log
/.git/config
```

Expected result:

access denied.

---

## 6. HTTPS Configuration

BarePitch must run over HTTPS.

Requirements:

valid TLS certificate;
secure cookies enabled;
no mixed content;
automatic redirect from HTTP to HTTPS.

Without HTTPS:

login credentials are exposed;
sessions are vulnerable;
the system is not acceptable for production.

---

## 7. Environment Configuration

Production `.env` example:

```text
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_HOST=production-db-host
DB_PORT=3306
DB_NAME=barepitch_prod
DB_USER=barepitch_user
DB_PASS=strong_password

SESSION_NAME=barepitch_session
```

Rules:

never commit `.env` to Git;
use strong passwords;
limit access to configuration files;
do not reuse development credentials.

---

## 8. Database Preparation

Before going live:

create production database;
run all migrations;
verify schema;
create database user with limited privileges;
test connection.

Database user should have only:

```text
SELECT
INSERT
UPDATE
DELETE
```

Avoid using root credentials.

---

## 9. Data Initialization

Production should start clean.

Do not use:

test users;
dummy data;
development seeds.

If initial admin access is needed:

create it manually or via a controlled script;
use a secure password;
change credentials after first login if needed.

---

## 10. Logging Setup

Ensure logging works before release.

Create:

```text
storage/logs/app.log
```

Requirements:

log file is writable;
logs are not publicly accessible;
logs do not contain sensitive data;
log rotation is considered.

Test:

trigger an error;
verify it is logged;
verify it is not shown to the user.

---

## 11. Error Handling in Production

In production:

users see generic messages;
technical details are logged;
no stack traces are shown;
no SQL errors are exposed.

Example user message:

```text
Something went wrong. Please try again.
```

This must be consistent across the application.

---

## 12. Security Verification

Before release, verify:

authentication works securely;
passwords are hashed;
sessions are protected;
CSRF protection is active;
all SQL uses prepared statements;
output is escaped;
ownership checks are enforced;
unauthorized access fails safely.

Test common attack patterns:

SQL injection;
XSS;
CSRF;
URL tampering.

---

## 13. Backup Strategy

Backups must be in place before production use.

Minimum requirements:

daily database backup;
secure storage location;
restricted access;
retention policy;
tested restore procedure.

Test restore:

import backup into a clean database;
verify application works.

A backup that cannot be restored is not a backup.

---

## 14. Deployment Process

Deployment must be controlled and repeatable.

Recommended steps:

pull or upload code from repository;
ensure correct version or tag;
update `.env`;
run database migrations;
set file permissions;
verify logs;
test core flow;
monitor for errors.

Avoid:

editing files directly on the server;
manual fixes without version control;
deploying untested code.

---

## 15. File Permissions

Ensure correct permissions:

application files are readable;
logs are writable;
uploads directory (if ever used) is restricted;
configuration files are not publicly readable.

Incorrect permissions can break the system or expose data.

---

## 16. Performance Check

BarePitch does not require heavy optimization, but must be responsive.

Check:

page load time;
database queries are efficient;
no unnecessary scripts;
no blocking resources.

The system must feel immediate in normal use.

---

## 17. Mobile Verification

Test production environment on mobile.

Check:

login works;
navigation works;
forms are usable;
attendance selection works;
no layout breaks.

Production behavior may differ from development.

---

## 18. Monitoring

BarePitch does not require complex monitoring in Version 1, but basic awareness is needed.

Monitor:

error logs;
unexpected behavior;
login failures;
system availability.

Review logs regularly.

---

## 19. Data Privacy Verification

Before release, confirm:

only minimal data is stored;
no sensitive fields exist;
no external tracking is active;
logs do not expose personal data;
backups are secured;
data can be deleted.

This aligns with the General Data Protection Regulation.

---

## 20. Final Pre-Launch Checklist

Before going live:

application runs in production mode;
HTTPS is active;
database is correctly configured;
migrations are complete;
no test data remains;
logs are working;
security checks are verified;
core flow works end to end;
mobile usability is acceptable;
backup strategy is active;
documentation is up to date.

If any of these fail, delay release.

---

## 21. First Launch Strategy

Do not assume everything works perfectly at first launch.

Recommended approach:

release to limited use;
observe behavior;
monitor logs;
fix issues quickly;
avoid adding new features immediately.

Stability first, expansion later.

---

## 22. Post-Deployment Checks

Immediately after deployment:

log in;
create a team;
add a player;
create a match;
set attendance;
log out;
log in again;
verify data persists.

Also test:

unauthorized access;
error handling;
mobile interface.

---

## 23. Rollback Awareness

Be prepared to revert.

If a deployment causes issues:

restore previous code version;
restore database if needed;
communicate clearly;
analyze cause before redeploying.

Version control and backups make rollback possible.

---

## 24. Definition of Done

Production preparation is complete when:

the application runs in a secure environment;
configuration is correct and externalized;
database is initialized and protected;
logs are working;
errors are handled safely;
security checks are verified;
backups exist and are tested;
deployment is repeatable;
core flows work in production;
no manual fixes are required after launch.

---

## 25. Guiding Question

Every production decision should be tested against this:

If something breaks tonight, can I understand, fix or restore it without guessing?

If not, production is not ready.
