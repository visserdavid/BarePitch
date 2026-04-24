# BarePitch – Acceptance

## 1. Purpose

This document defines when BarePitch Version 1 can be considered acceptable for real use.

Acceptance is not about perfection. It is about readiness.

BarePitch is accepted when it is:

functionally usable;
technically stable;
secure enough for its scope;
clear in its behavior;
aligned with its original purpose.

The goal is not to delay release until everything is ideal. The goal is to avoid releasing something that creates confusion, risk or dependency on constant fixes.

---

## 2. Acceptance Philosophy

BarePitch follows a strict but grounded principle:

A feature is not accepted because it works once.
A feature is accepted when it works reliably under normal and imperfect conditions.

Acceptance is therefore based on:

repeatability;
predictability;
clarity;
safety.

---

## 3. Core Acceptance Statement

BarePitch Version 1 is acceptable when:

A coach can prepare and manage a match using only BarePitch, without needing external tools.

Everything in this document supports that statement.

---

## 4. Scope Acceptance

BarePitch Version 1 must remain within its defined scope.

Included:

authentication
teams
players
matches
attendance or selection

Excluded:

statistics
lineup visualization
multi-user collaboration
notifications
external integrations
player dossiers

If features outside scope appear, acceptance must be reconsidered.

---

## 5. Functional Acceptance

BarePitch must support the complete core flow.

A user can:

log in;
log out;
create a team;
edit a team;
archive a team;
add players;
edit players;
mark players inactive;
create a match;
edit a match;
view matches;
select players for a match;
save attendance or availability;
reopen and review saved data.

Each action must:

complete without error;
produce expected results;
persist after logout and login.

---

## 6. Usability Acceptance

BarePitch must be usable without explanation.

A coach should be able to:

understand where to start;
recognize main actions;
complete tasks without guessing;
recover from mistakes;
use the system on a mobile device.

Usability is accepted when:

no instructions are needed for core flows;
navigation feels predictable;
forms are clear;
feedback messages are understandable.

If a feature requires explanation, it is not ready.

---

## 7. Validation Acceptance

All input must be validated.

Acceptance criteria:

required fields are enforced;
invalid values are rejected;
error messages are clear;
safe input is preserved after validation errors;
no invalid data is stored.

Examples:

empty team name is rejected;
invalid date is rejected;
invalid attendance status is rejected;
non-numeric IDs are rejected.

Validation must happen server-side.

---

## 8. Security Acceptance

Security must meet a baseline level.

Acceptance criteria:

passwords are hashed;
sessions are secure;
prepared statements are used;
CSRF protection exists for all POST actions;
output is escaped;
ownership is enforced;
unauthorized access is blocked;
errors do not expose technical details;
HTTPS is used in production.

Basic attack tests must fail safely:

SQL injection;
cross-site scripting;
CSRF manipulation;
URL tampering.

If any of these succeed, BarePitch is not acceptable.

---

## 9. Access Control Acceptance

Access control must be correct and consistent.

Acceptance criteria:

users can only see their own teams;
users can only manage their own players;
users can only manage their own matches;
attendance can only be saved for valid players in the correct team;
changing IDs in URLs does not expose data;
hidden fields are not trusted.

Unauthorized access must:

fail safely;
not reveal unnecessary information;
not modify data.

---

## 10. Data Integrity Acceptance

Data must remain consistent.

Acceptance criteria:

relationships between users, teams, players and matches are valid;
inactive players do not break match history;
match data persists correctly;
no orphaned records are created;
database constraints are respected.

Recreating the database from migrations must produce a working system.

---

## 11. Error Handling Acceptance

Errors must be handled safely and clearly.

Acceptance criteria:

validation errors are shown near the field;
authorization errors return safe responses;
missing resources return 404;
unexpected errors are logged;
users do not see technical details;
messages are understandable.

The system must remain calm, even when something goes wrong.

---

## 12. Performance Acceptance

BarePitch must be responsive in normal use.

Acceptance criteria:

pages load quickly;
forms respond without noticeable delay;
navigation feels immediate;
no heavy scripts block interaction.

The system must feel usable during real coaching situations.

---

## 13. Mobile Acceptance

BarePitch must work on mobile devices.

Acceptance criteria:

core flows work on small screens;
buttons are easy to tap;
forms are readable;
no horizontal scrolling for main actions;
attendance selection is usable.

If the match screen is difficult on mobile, BarePitch is not acceptable.

---

## 14. Accessibility Acceptance

BarePitch must meet basic accessibility standards aligned with Web Content Accessibility Guidelines.

Acceptance criteria:

semantic HTML is used;
forms have labels;
keyboard navigation works;
focus is visible;
error messages are clear;
color is not the only indicator;
text is readable.

Accessibility is accepted when the system works without special effort.

---

## 15. Privacy Acceptance

BarePitch must respect privacy boundaries aligned with the General Data Protection Regulation.

Acceptance criteria:

only necessary data is stored;
no sensitive personal data is collected;
users can manage and delete their data;
no external tracking is used;
logs do not contain unnecessary personal data;
backups are secured.

If unnecessary personal data appears, acceptance must be reconsidered.

---

## 16. Technical Acceptance

The system must be technically stable.

Acceptance criteria:

application runs locally and in production;
configuration is externalized;
database migrations work;
logs are writable;
non-public files are protected;
no manual changes are required after deployment;
code is structured consistently.

A fresh setup must work without hidden steps.

---

## 17. Testing Acceptance

Testing must confirm reliability.

Acceptance criteria:

core flows are tested manually;
security tests are performed;
validation is tested;
access control is tested with multiple users;
regression testing confirms existing features still work;
deployment checks are completed.

Testing must be repeatable.

---

## 18. Documentation Acceptance

Documentation must support understanding.

Acceptance criteria:

README explains setup and purpose;
CHANGELOG reflects current version;
environment configuration is clear;
important decisions are documented;
test log reflects recent testing.

The project should not depend on memory alone.

---

## 19. Release Readiness Checklist

Before releasing Version 1:

login and logout work;
teams, players, matches and attendance work;
validation is complete;
security basics are implemented;
access control is correct;
mobile usability is acceptable;
errors are handled safely;
privacy boundaries are respected;
database can be recreated;
documentation is updated;
no critical issues remain.

If any of these fail, release should be delayed.

---

## 20. Known Limitations

Acceptance does not mean completeness.

Version 1 is expected to lack:

advanced features;
multi-user support;
statistics;
lineup tools;
integrations.

These are not failures. They are deliberate choices.

---

## 21. Post-Acceptance Responsibility

After acceptance:

monitor usage;
observe friction;
collect feedback;
fix issues quickly;
avoid uncontrolled feature growth.

Acceptance is the start of use, not the end of development.

---

## 22. Definition of Done

BarePitch Version 1 is done when:

the core flow works end to end;
the system is understandable;
the system is safe enough for its scope;
the system is usable in real conditions;
the system aligns with its minimal philosophy.

---

## 23. Guiding Question

Every acceptance decision should be tested against this:

Would you trust this system to use just before a match, without backup tools?

If the answer is no, BarePitch is not ready.
