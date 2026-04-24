# BarePitch – Documentation

## 1. Purpose

This document defines how BarePitch is documented.

Documentation is not an afterthought. It is part of the product.
Without it, the system may work, but it cannot be understood, maintained or trusted over time.

The goal is not to write a lot. The goal is to capture what would otherwise be forgotten.

BarePitch documentation should:

support understanding;
preserve decisions;
enable reuse;
reduce dependence on memory;
stay aligned with the minimal philosophy of the product.

---

## 2. Documentation Principles

BarePitch documentation follows these principles:

Keep it small but meaningful.
Document only what adds clarity.

Write for your future self.
Assume you will forget why decisions were made.

Explain why, not only how.
Code shows how. Documentation should reveal intent.

Stay close to the code.
Documentation should reflect reality, not an idealized version.

Update when something changes.
Outdated documentation is worse than no documentation.

---

## 3. Documentation Structure

BarePitch should use a simple, consistent structure.

```text
README.md
CHANGELOG.md
.env.example
docs/
    devlog.md
    decisions.md
    test-log.md
    architecture.md (optional)
```

Each file has a clear purpose.

---

## 4. README.md

The README is the entry point.

It answers:

What is BarePitch?
What problem does it solve?
How do I run it locally?
What technologies are used?
What is the current scope?

Example structure:

```markdown
# BarePitch

BarePitch is a minimal web application for football coaches to manage teams, players, matches and attendance.

## Stack
- PHP
- MySQL
- JavaScript
- HTML / CSS

## Setup

1. Clone the repository
2. Copy `.env.example` to `.env`
3. Configure database settings
4. Run migrations
5. Start local server

## Status

Version: v0.x.x  
Scope: teams, players, matches, attendance

## Philosophy

BarePitch shows what matters, when it matters. Nothing more.
```

The README should remain short and clear.

---

## 5. CHANGELOG.md

The changelog records what changed over time.

Purpose:

Track evolution.
Understand when features were added or changed.
Provide context for debugging and rollback.

Example:

```markdown
# Changelog

## v0.5.0
- Attendance management added
- Match player linking implemented
- Basic validation improved

## v0.4.0
- Match creation and editing
- Match overview screen

## v0.3.0
- Player management
- Player status (active/inactive)

## v0.2.0
- Team management

## v0.1.0
- Project setup
- Database connection
- Authentication
```

Keep entries concise and meaningful.

---

## 6. .env.example

The `.env.example` file documents required configuration.

It should include:

all required environment variables;
no real credentials;
clear naming.

Example:

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

This file is both documentation and template.

---

## 7. docs/devlog.md

The development log captures short-term thinking.

Purpose:

Track decisions during building.
Capture ideas before they disappear.
Make intuitive work visible.

Example:

```markdown
## 2026-04-24

- Decided to build without framework
- Focus first on authentication and teams
- Postpone lineup feature
- Attendance status limited to four values

## Questions

- Should season be required for teams?
- Do we need match location in Version 1?
```

The devlog is informal, but valuable.

---

## 8. docs/decisions.md

This file records important decisions.

Purpose:

Explain why something is the way it is.
Prevent re-discussing the same questions.
Provide context for future changes.

Example:

```markdown
## Decision: No file uploads in Version 1

Context:
Uploads increase storage and privacy complexity.

Decision:
BarePitch will not support uploads in Version 1.

Reason:
Not needed for match preparation and increases risk.

---

## Decision: Minimal player data

Context:
More data fields increase privacy responsibility.

Decision:
Only store display name and optional shirt number.

Reason:
Sufficient for coaching use, reduces legal risk.
```

This file should contain only meaningful decisions, not every small change.

---

## 9. docs/test-log.md

The test log captures what has been tested.

Purpose:

Avoid guessing what was verified.
Track known issues.
Support release decisions.

Example:

```markdown
## 2026-04-24 – v0.5.0

- Login: passed
- Team creation: passed
- Player management: passed
- Match creation: passed
- Attendance: passed
- Authorization tests: passed
- XSS test: passed
- SQL injection test: passed

## Known issues

- Attendance screen needs better mobile layout
```

Keep it honest and short.

---

## 10. Optional: docs/architecture.md

If needed, a short architecture overview may be added.

Purpose:

Explain structure.
Clarify relationships between components.
Support onboarding.

Example topics:

project structure;
data flow;
authentication flow;
ownership model.

Keep it high-level. Do not duplicate code.

---

## 11. Inline Code Documentation

Code should be readable without heavy comments.

Use comments when:

the intention is not obvious;
a security decision is made;
a workaround exists;
a trade-off was chosen;
future risk is known.

Example:

```php
// Archived teams are hidden by default, but kept to preserve match history.
```

Avoid comments that repeat the code.

---

## 12. API and Data Documentation

BarePitch Version 1 does not expose a public API.

However, internal data structures should remain predictable.

Document:

table purpose;
key relationships;
allowed values (such as attendance status);
important constraints.

This is already partly covered in the database design document.

---

## 13. Security Documentation

Security-related decisions should be visible.

Document:

why certain data is not stored;
why CSRF is required;
why ownership checks are strict;
why no public access exists;
why uploads are excluded.

This prevents future weakening of the security model.

---

## 14. Privacy Documentation

BarePitch should remain transparent about data usage.

Document:

what data is stored;
why it is stored;
what is intentionally not stored;
how data can be removed;
how long data is kept.

This may later evolve into a user-facing privacy statement.

---

## 15. Documentation During Vibe Coding

When working intuitively, documentation becomes more important, not less.

Minimal rhythm:

after a meaningful decision, update `decisions.md`;
after a development session, update `devlog.md`;
before a version tag, update `CHANGELOG.md`;
after testing, update `test-log.md`.

This keeps momentum without losing clarity.

---

## 16. What Not to Document

Avoid documenting:

obvious code behavior;
temporary experiments that are discarded;
every small commit;
implementation details already clear in code;
long theoretical explanations disconnected from the project.

Documentation should reduce noise, not add it.

---

## 17. Maintenance of Documentation

Documentation must be maintained with the code.

Rules:

update when functionality changes;
remove outdated sections;
keep examples aligned with reality;
review documentation before tagging a version.

Outdated documentation creates false confidence.

---

## 18. Documentation Checklist

Before a version is released, check:

README reflects current state;
CHANGELOG is updated;
.env.example matches required variables;
decisions.md includes important choices;
test-log.md reflects recent testing;
no sensitive data is included in documentation;
setup instructions work from scratch.

---

## 19. Definition of Done for Documentation

Documentation is sufficient when:

a new environment can be set up using the README;
important decisions are traceable;
recent changes are recorded;
tests are visible;
configuration is clear;
the project can be understood without guessing;
nothing critical depends on memory alone.

---

## 20. Guiding Question

Every documentation decision should be tested against this:

What would I need to remember if I stopped working on this for three months?

If the answer is not written down, it will be lost.
