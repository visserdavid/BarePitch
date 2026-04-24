# BarePitch – Privacy, GDPR and Data Governance

## 1. Purpose

This document defines how BarePitch handles personal data, privacy and data governance in line with the General Data Protection Regulation.

BarePitch is intentionally minimal. That is not only a product choice, but also a privacy strategy.

The goal is clear:

BarePitch should process as little personal data as possible, protect it properly, and make its use understandable and controllable.

---

## 2. Context

BarePitch is used by football coaches to manage:

teams
players
matches
attendance and availability

Even though the data seems simple, it may involve:

names of individuals;
team membership;
participation in activities;
potentially minors.

This means privacy must be treated seriously, even in a small project.

---

## 3. Core Privacy Principles

BarePitch follows these principles:

Data minimization.
Only store what is necessary.

Purpose limitation.
Data is used only for match preparation and management.

Storage limitation.
Data is not kept longer than needed.

Security by design.
Protection is built into the system.

Transparency.
Users should understand what is stored and why.

Control.
Users must be able to manage and remove their data.

---

## 4. Roles and Responsibilities

BarePitch acts as a data processor only in a technical sense.

The coach using BarePitch is the effective data controller for the team data they enter.

BarePitch itself should be designed so that:

it does not claim ownership of user data;
it does not reuse data for other purposes;
it does not analyze or profile players;
it does not share data externally.

---

## 5. Data Categories

## 5.1 User Data

Stored for authentication and account management.

Fields:

email address
password hash
display name

Purpose:

login and access control

---

## 5.2 Team Data

Fields:

team name
season

Purpose:

structure for organizing players and matches

---

## 5.3 Player Data

Fields:

display name
optional shirt number
status (active or inactive)

Purpose:

identify players within a team

Important:

No personal contact or sensitive data is stored.

---

## 5.4 Match Data

Fields:

opponent name
match date
optional time
optional location
optional home or away indicator

Purpose:

context for match preparation

---

## 5.5 Attendance Data

Fields:

player reference
match reference
status such as available or selected

Purpose:

support selection decisions

---

## 6. Data Minimization

BarePitch explicitly avoids storing:

birth dates
addresses
phone numbers
email addresses of players
medical information
injury details
behavioral notes
photos
documents
free-text evaluations

This is a deliberate design choice.

The safest sensitive data is data that is never collected.

---

## 7. Lawful Basis

Under the General Data Protection Regulation, data processing requires a lawful basis.

For BarePitch, the likely basis is:

legitimate interest or contract between the coach and their team context

BarePitch itself should not assume or enforce a legal basis.
It should support the user in acting responsibly.

---

## 8. Purpose Limitation

Data stored in BarePitch may only be used for:

team organization
match preparation
attendance tracking

Data must not be used for:

profiling players
marketing
external sharing
automated decision-making
behavioral analysis

The system design should not invite these uses.

---

## 9. Data Storage and Retention

BarePitch should define clear retention behavior.

Recommended approach:

Active teams remain visible.
Archived teams are hidden but preserved.
Inactive players remain linked to past matches.
Matches remain stored for historical reference.

Deletion rules:

Users must be able to delete their account.
Deleting an account removes all associated data.

No indefinite retention without purpose.

---

## 10. User Rights

BarePitch should support the core rights defined in the General Data Protection Regulation.

## 10.1 Right of Access

Users should be able to view:

their teams
their players
their matches
their attendance data

---

## 10.2 Right to Rectification

Users should be able to:

edit team names
edit player names
edit match details

---

## 10.3 Right to Erasure

Users should be able to:

delete teams
remove players
delete matches
delete their account

Deleting the account should remove all associated data.

---

## 10.4 Right to Restriction

In a minimal system like BarePitch, this is implicitly supported through:

archiving teams
marking players inactive

---

## 10.5 Right to Data Portability

Optional for Version 1.

Future possibility:

export data as a structured file such as JSON or CSV

---

## 11. Data Security

BarePitch must protect stored data.

Measures include:

password hashing
secure sessions
prepared database queries
CSRF protection
output escaping
HTTPS in production
restricted server access
minimal logging

Data security is covered in detail in the security design.

---

## 12. Data Access

Access to data is strictly limited.

Rules:

users can only access their own data
no shared teams in Version 1
no public access
no external API access
no background data sharing

Every request must pass ownership checks.

---

## 13. Logging and Privacy

Logging should be minimal and careful.

Log:

errors
security-relevant events

Do not log:

passwords
full form submissions
player data beyond identifiers
sensitive user information

Logs should not become a hidden data store.

---

## 14. Backups

Backups must be treated as sensitive data.

Rules:

store backups securely
restrict access
do not expose backups publicly
define retention period
test restore process

A backup is a full copy of personal data.

---

## 15. Third-Party Services

BarePitch Version 1 should avoid third-party integrations.

No:

analytics tools
tracking pixels
external APIs
cloud file storage

This reduces privacy risk and simplifies compliance.

---

## 16. Cookies and Tracking

BarePitch should use only necessary cookies.

Examples:

session cookie
CSRF token storage

No:

tracking cookies
advertising cookies
behavioral analytics cookies

If cookies are used, they should be:

functional
minimal
transparent

---

## 17. Transparency

BarePitch should clearly communicate:

what data is stored
why it is stored
how it is used
how it can be removed

This may be implemented through:

a privacy page
clear language in the interface
simple explanations

Transparency builds trust and prevents misuse.

---

## 18. Data Breach Handling

A data breach is any unauthorized access, loss or exposure of personal data.

In case of a breach:

identify what happened
limit further exposure
secure the system
review logs
assess impact
determine whether notification is required under the General Data Protection Regulation
document the incident

Even small systems must take this seriously.

---

## 19. Data Lifecycle Overview

```text
Data is created by the user
→ stored in the database
→ used for match preparation
→ optionally archived
→ eventually deleted by the user
```

No secondary use should exist.

---

## 20. Privacy by Design

BarePitch integrates privacy into the design itself.

Examples:

minimal player data
no uploads
no free-text notes
no analytics tracking
no external sharing
strict access control

Privacy is not a feature. It is a boundary.

---

## 21. Privacy Risks

Even with a minimal design, risks remain:

coaches entering more data than intended
shared devices exposing sessions
weak passwords
improper backups
manual exports
misuse outside the system

The system should reduce risk, but cannot eliminate human behavior.

---

## 22. Governance and Responsibility

BarePitch should make it clear:

users are responsible for the data they enter
the system is designed to limit risk
the system does not enforce legal compliance for the user

This keeps responsibility aligned with actual use.

---

## 23. Definition of Done

Privacy and data governance are sufficient when:

only necessary data is stored
sensitive data is excluded by design
users can view and manage their data
users can delete their data
access is restricted to the owner
data is protected in storage and transport
logs do not expose personal data
backups are secured
no external tracking exists
privacy behavior is understandable

---

## 24. Guiding Question

Every privacy decision should be tested against this:

If this data were exposed, would it be justified that we stored it?

If the answer is no, the data should not exist in BarePitch.
