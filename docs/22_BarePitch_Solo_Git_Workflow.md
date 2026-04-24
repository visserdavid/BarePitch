# BarePitch – Solo Git Workflow

## 1. Purpose

This document defines the Git workflow for solo development of BarePitch.

BarePitch is developed alone, with room for exploratory coding. The workflow must therefore support freedom without losing control.

The goal is simple:

Move quickly, but always keep a safe way back.

## 2. Core Principle

Git should create calm, not administration.

For BarePitch, Git is used to:

protect working states;
record meaningful progress;
separate experiments from stable code;
make releases traceable;
prevent accidental loss during vibe coding.

## 3. Branch Structure

Use a light branch model:

```text
main        stable working version
wip         daily development branch
spike/*     temporary experiments
backup/*    safety branches before risky changes
```

## 4. Main Branch

`main` is the stable branch.

Rules:

`main` must always run;
`main` contains only tested code;
`main` represents a usable state;
production releases come from `main`;
each important stable state receives a tag.

Do not use `main` as a place for free experimentation.

## 5. WIP Branch

`wip` is the daily working branch.

Create it once:

```bash
git checkout -b wip
```

Use it for:

building features;
trying small improvements;
working with AI-generated code;
cleaning up structure;
preparing the next stable version.

`wip` may be imperfect, but it should not stay broken for long.

## 6. Spike Branches

Use `spike/*` branches for experiments.

Examples:

```text
spike/lineup-grid
spike/mobile-navigation
spike/magic-login
spike/css-layout
```

Create a spike:

```bash
git checkout wip
git checkout -b spike/lineup-grid
```

If the experiment is useful:

```bash
git checkout wip
git merge spike/lineup-grid
git branch -d spike/lineup-grid
```

If the experiment is not useful:

```bash
git checkout wip
git branch -D spike/lineup-grid
```

A spike is allowed to be messy. That is its purpose.

## 7. Backup Branches

Use `backup/*` branches before risky refactors.

Example:

```bash
git checkout wip
git checkout -b backup/before-auth-refactor
git checkout wip
```

Use this when:

many files will change;
database structure may change;
AI-generated code will affect core logic;
you are unsure whether the direction is right.

A backup branch is a safety marker, not a long-term branch.

## 8. Daily Workflow

Start:

```bash
git checkout wip
git status
```

Before larger changes:

```bash
git add .
git commit -m "checkpoint: before restructuring authentication"
```

During development:

```bash
git add .
git commit -m "wip: add first version of match form"
```

When a feature becomes coherent:

```bash
git add .
git commit -m "feat: add match creation flow"
```

## 9. AI-Assisted Coding Rule

Before accepting AI-generated changes, commit first.

```bash
git add .
git commit -m "checkpoint: before AI changes to attendance flow"
```

Then apply AI changes in small parts.

Afterwards, review:

security;
validation;
ownership checks;
naming;
unnecessary complexity;
framework-like additions;
unexpected dependencies.

Do not accept code you do not understand.

## 10. Commit Types

Use practical commit prefixes:

```text
wip:        unfinished but useful progress
checkpoint: safe point before risky change
feat:       new working functionality
fix:        bug fix
refactor:   structure improvement without behavior change
security:   security improvement
docs:       documentation change
cleanup:    removal of temporary code or duplication
spike:      experiment
```

## 11. Commit Message Templates

### WIP

```text
wip: <what you are working on>

- context: <where you are in the flow>
- change: <what changed>
- status: <working / partial / broken / untested>
```

### Checkpoint

```text
checkpoint: <current stable point>

- reason: <why this checkpoint matters>
- next: <what happens next>
```

### Feature

```text
feat: <what now works>

- user: <what the user can do>
- logic: <what changed technically>
- notes: <known limitations>
```

### Fix

```text
fix: <what was fixed>

- issue: <what went wrong>
- cause: <why it happened>
- fix: <what changed>
```

### Refactor

```text
refactor: <what was restructured>

- before: <old situation>
- after: <new situation>
- impact: <why this helps>
```

## 12. When to Commit

Commit at natural stopping points.

Good moments:

before a risky change;
after something starts working;
after fixing a bug;
after adding validation;
after adding a security check;
before switching tasks;
at the end of a development session.

Do not wait until everything is perfect.

Small, honest commits are better than large vague commits.

## 13. When Not to Commit to Main

Do not merge to `main` when:

core flow is broken;
security checks are missing;
debug code remains;
temporary files are present;
database changes are undocumented;
the application only works because of local hidden state;
you cannot explain what changed.

In those cases, stay on `wip`.

## 14. Preparing WIP for Main

Before merging `wip` into `main`, check:

application runs locally;
core flow works;
validation works;
access control works;
CSRF protection is present;
output is escaped;
debug code is removed;
documentation is updated;
changelog is updated if needed.

Use:

```bash
git status
git diff main..wip
```

This is your solo code review.

## 15. Merge to Main

When `wip` is stable:

```bash
git checkout main
git merge wip
```

Then tag the stable version:

```bash
git tag v0.3.0
```

Push if using a remote repository:

```bash
git push
git push --tags
```

Then return to `wip`:

```bash
git checkout wip
git merge main
```

## 16. Version Tags

Use tags as milestones.

Suggested BarePitch milestones:

```text
v0.1.0 project structure and database connection
v0.2.0 authentication
v0.3.0 teams
v0.4.0 players
v0.5.0 matches
v0.6.0 attendance
v0.7.0 responsive cleanup
v0.8.0 security cleanup
v1.0.0 first production-ready release
```

Patch versions:

```text
v1.0.1 small fix
v1.0.2 security fix
```

## 17. Changelog

Update `CHANGELOG.md` before tagging.

Example:

```markdown
## v0.4.0
- Match creation added
- Match overview added
- Ownership checks added for match access
```

The changelog should explain what changed from a project perspective, not list every commit.

## 18. Protecting Secrets

Never commit:

`.env`;
database passwords;
API keys;
production credentials;
log files;
real personal data;
database dumps.

Use `.gitignore`:

```gitignore
.env
*.log
/storage/logs/*
/storage/uploads/*
/vendor/
/node_modules/
```

If a secret is committed, treat it as leaked and replace it.

## 19. Production Rule

Never edit production directly.

Correct flow:

change locally;
test locally;
commit;
merge to `main`;
tag version;
deploy from that version.

Production must always match a known Git state.

## 20. Devlog Integration

Use `docs/devlog.md` alongside Git.

Git records what changed.
The devlog records why direction changed.

Example:

```markdown
## 2026-04-24
- Decided to postpone lineup grid.
- Focus remains on attendance flow.
- Need to simplify match detail screen before adding new fields.
```

This is especially useful during vibe coding.

## 21. Cleanup Rhythm

After exploratory work:

remove debug output;
rename unclear functions;
move repeated logic;
delete unused files;
document known debt.

Then commit:

```bash
git add .
git commit -m "cleanup: remove temporary attendance debug code"
```

Do not let exploratory code silently become permanent.

## 22. Recovery

Useful recovery commands:

Discard changes in one file:

```bash
git restore path/to/file.php
```

See recent commits:

```bash
git log --oneline -10
```

Undo a committed change safely:

```bash
git revert <commit-id>
```

Return to a tagged version:

```bash
git checkout v0.3.0
```

Use recovery deliberately. Do not panic-delete files.

## 23. Definition of Done

The Git workflow is working when:

`main` stays stable;
`wip` supports daily momentum;
experiments happen in `spike/*`;
risky changes have checkpoints;
versions are tagged;
secrets stay out of Git;
production matches a known commit;
commit history helps instead of confusing.

## 24. Guiding Question

Every Git decision should be tested against this:

If I break the project now, can I safely return to a known good point?

If the answer is no, commit, branch or tag before continuing.
