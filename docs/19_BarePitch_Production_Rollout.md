# BarePitch – Production Rollout

## 1. Purpose

This document describes how BarePitch is released to production.

Production rollout is not a single action. It is a controlled transition from development to real use.

The goal is:

BarePitch must go live in a way that is predictable, reversible and observable.

---

## 2. Core Principle

A rollout is successful when:

users can start using the system without disruption;
core functionality works immediately;
problems can be detected quickly;
changes can be reversed if needed.

The focus is not speed, but control.

---

## 3. Rollout Strategy

BarePitch Version 1 should use a simple, controlled rollout strategy.

Recommended approach:

single environment
single release version
limited initial usage

Avoid:

multiple parallel versions;
feature flags for Version 1;
complex staged deployments;
partial rollouts without control.

Keep it simple, but disciplined.

---

## 4. Pre-Rollout Conditions

Before rollout, confirm:

production environment is configured;
HTTPS is active;
database is created and migrated;
no test data exists;
security checks are verified;
backup strategy is active;
documentation is updated;
core flows are tested in production-like conditions.

If any of these are uncertain, do not deploy.

---

## 5. Versioning

Every rollout must correspond to a version.

Example:

```text
v1.0.0
```

Rules:

only deploy tagged versions;
never deploy uncommitted or untracked changes;
the deployed version must be identifiable;
the version must be recorded in the changelog.

This ensures traceability.

---

## 6. Deployment Steps

A standard rollout should follow a fixed sequence.

### Step 1: Prepare Code

Ensure:

latest changes are committed;
working branch is stable;
tests are performed;
version tag is created.

Example:

```bash
git checkout main
git pull
git tag v1.0.0
git push --tags
```

---

### Step 2: Backup Current State

Before changing anything:

backup the database;
store backup securely;
verify backup success.

Optional:

backup current code version.

---

### Step 3: Upload or Pull Code

Deploy the new version:

pull from repository or upload files;
ensure correct directory structure;
verify file integrity.

Do not edit files manually during deployment.

---

### Step 4: Update Configuration

Check `.env`:

production values are correct;
no development settings remain;
database credentials are valid;
debug mode is disabled.

---

### Step 5: Run Migrations

Apply database changes:

run migration scripts;
verify schema updates;
confirm no errors occurred.

If a migration fails:

stop rollout;
restore backup if needed.

---

### Step 6: Set Permissions

Ensure:

log directory is writable;
configuration files are protected;
non-public directories are inaccessible;
no excessive permissions are set.

---

### Step 7: Restart Services

If necessary:

restart web server;
restart PHP service.

This ensures new code is loaded.

---

## 7. Immediate Post-Rollout Testing

After deployment, test immediately.

Core flow:

log in;
create team;
add player;
create match;
set attendance;
log out;
log in again;
verify data persists.

Also test:

invalid login;
unauthorized access;
error handling;
mobile layout.

This should be done before real users start using the system.

---

## 8. Smoke Testing

Smoke testing checks whether the system is alive.

Minimal checks:

application loads;
login page loads;
database connection works;
main pages respond;
no fatal errors occur;
logs do not show critical issues.

If smoke testing fails, rollback immediately.

---

## 9. Monitoring After Rollout

After release, observe behavior.

Monitor:

error logs;
unexpected responses;
failed logins;
performance issues;
user feedback.

Initial monitoring window:

first few hours;
first day;
first week.

Most issues appear early.

---

## 10. Controlled First Use

Do not open BarePitch to full use immediately.

Recommended:

use it yourself first;
use with a small group;
observe real usage patterns;
note friction points.

This reduces risk and provides insight.

---

## 11. Handling Issues

If issues appear:

identify the problem;
check logs;
assess impact;
decide whether to fix or rollback.

For minor issues:

apply a fix quickly;
test locally;
deploy a patch version.

For critical issues:

stop usage if needed;
rollback to previous version;
restore database if necessary.

---

## 12. Rollback Strategy

Rollback must always be possible.

Requirements:

previous version available in Git;
database backup available;
deployment process reversible.

Rollback steps:

restore previous code version;
restore database if required;
verify application works;
document incident.

Rollback is not failure. It is control.

---

## 13. Database Rollback Considerations

Database changes are harder to reverse.

Best practices:

write safe migrations;
avoid destructive changes in early versions;
backup before migration;
test migrations locally;
avoid dropping columns immediately.

If rollback is needed:

restore database from backup;
do not attempt manual fixes unless fully understood.

---

## 14. Communication

Even in a small project, communication matters.

If BarePitch is used by others:

inform users of deployment;
inform users of known issues;
inform users of fixes;
keep communication simple and honest.

Silence creates confusion.

---

## 15. Post-Rollout Review

After rollout, reflect.

Questions:

Did the deployment go smoothly?
Were there unexpected issues?
Were logs useful?
Was rollback possible?
Did users understand the system?
Did anything feel fragile?

Document insights in:

```text
docs/devlog.md
```

---

## 16. Iteration After Rollout

Do not immediately add new features.

First:

stabilize;
fix issues;
observe usage;
refine existing flows.

Only then:

consider next feature.

BarePitch grows by strengthening its core, not by expanding quickly.

---

## 17. Version Increment Strategy

After rollout:

fixes increase patch version:

```text
v1.0.1
v1.0.2
```

small improvements increase minor version:

```text
v1.1.0
```

major changes increase major version:

```text
v2.0.0
```

Versioning helps structure growth.

---

## 18. Common Rollout Mistakes

Avoid:

deploying untested code;
skipping backups;
editing production directly;
ignoring logs;
deploying without version tags;
not testing immediately after deployment;
opening system to users without observation;
making multiple changes at once without control.

These mistakes often lead to confusion, not just bugs.

---

## 19. Rollout Checklist

Before rollout:

code is stable;
version is tagged;
backup is ready;
environment is configured;
migrations are tested;
security is verified;
documentation is updated.

During rollout:

code is deployed;
configuration is verified;
migrations run;
permissions checked.

After rollout:

smoke test passes;
core flow works;
logs are monitored;
users are informed if needed.

---

## 20. Definition of Done

The rollout is complete when:

the correct version is running in production;
the application is accessible via HTTPS;
core flows work without issues;
no critical errors appear in logs;
users can use the system;
monitoring is active;
rollback remains possible.

---

## 21. Guiding Question

Every rollout decision should be tested against this:

If something goes wrong right now, do I know what to do?

If the answer is no, the rollout is not under control.
