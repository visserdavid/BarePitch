# BarePitch – Legal and Ethical Exploration

## 1. Context

BarePitch is a minimal web application for managing amateur football teams.
It processes limited personal data in a practical, low-threshold context, often involving minors.

This creates a specific responsibility:

The system must remain simple, but cannot be careless.

Legal compliance is not treated as an afterthought, but as a design constraint that shapes what BarePitch is allowed to be.

---

## 2. Applicable Legal Framework

BarePitch operates within the scope of European and Dutch law.

The primary framework is the General Data Protection Regulation.

This implies:

* personal data may only be processed with a valid legal basis
* data processing must be transparent
* data must be limited to what is necessary
* users have enforceable rights
* the developer is accountable for compliance

In the Dutch context, this is enforced by the Autoriteit Persoonsgegevens.

Even for small-scale or personal projects, these principles apply.

---

## 3. Nature of Data in BarePitch

BarePitch intentionally limits the type and scope of data.

### 3.1 Expected data

* User account data (email, password)
* Player names
* Team structures
* Attendance and participation records

### 3.2 Explicit exclusions

BarePitch should not store:

* Sensitive personal data (health, religion, ethnicity)
* National identification numbers
* Detailed personal profiles
* Free-text notes that may contain sensitive information

### 3.3 Key principle

> If the application can function without a piece of data, it should not be stored.

This is not only a legal principle, but a structural design choice.

---

## 4. Legal Basis for Processing

For BarePitch, the most realistic legal bases are:

### 4.1 Legitimate interest

A coach managing a team has a legitimate interest in organizing players and matches.

This requires:

* proportionality (no excessive data)
* expectation (users reasonably expect this use)
* minimal impact on privacy

### 4.2 Consent (secondary)

Consent may be relevant, especially when:

* dealing with minors
* allowing players to access their own data
* sharing data across users

Consent must be:

* freely given
* specific
* informed
* revocable

### 4.3 Tension

Legitimate interest allows simplicity.
Consent introduces complexity.

This tension must be consciously managed.

---

## 5. Special Consideration: Minors

BarePitch will often be used in youth football contexts.

This raises additional ethical and legal sensitivity.

Key implications:

* extra caution with personal data
* avoid unnecessary exposure of player information
* no public visibility by default
* no tracking or profiling
* no behavioral data collection

The system should assume vulnerability, even if not strictly required by law in every case.

---

## 6. Privacy by Design

Privacy is not a layer added later. It is embedded in the structure.

Concrete implications:

### 6.1 Data minimization

Only store:

* what is needed
* when it is needed
* for as long as it is needed

### 6.2 Default privacy

* no public access to data
* no sharing between teams by default
* no indexing by search engines

### 6.3 Controlled access

* users only see their own teams
* no global browsing of players or data
* strict server-side access checks

### 6.4 Limited retention

* no indefinite storage without purpose
* clear ability to delete data

---

## 7. Transparency

Users must understand what happens with their data.

BarePitch should provide:

* a concise privacy statement
* clear explanation of stored data
* purpose of data usage
* contact point for questions

The tone should match the product:

clear, minimal, without legal overload.

---

## 8. User Rights

Users must be able to exercise their rights.

At minimum:

* access to their data
* correction of incorrect data
* deletion of data

In practice, this means:

* data must be structured and retrievable
* deletion must be technically possible
* no hidden or orphaned data

If a right cannot be fulfilled technically, the system is not compliant.

---

## 9. Security as a Legal Requirement

Security is not only technical, but legal.

BarePitch must implement:

* encrypted connections (HTTPS)
* secure password hashing
* protection against common attacks (SQL injection, XSS, CSRF)
* session security
* limited server access

The standard reference for risks is the OWASP, particularly its widely adopted Top 10 risk categories.

Security is proportional:

Not enterprise-level complexity, but no avoidable vulnerabilities.

---

## 10. Data Storage and Hosting

Data will be stored on a hosting environment.

This introduces a second party: the hosting provider.

Implications:

* the provider acts as a data processor
* a processing agreement may be required
* data location (EU vs. non-EU) matters
* backups must be considered part of data processing

Choice of hosting is therefore not neutral.

---

## 11. Logging and Monitoring

Logging is necessary for debugging and security.

However:

* logs may contain personal data
* logs must not become hidden storage

Principles:

* log minimally
* avoid storing full input data
* define retention limits
* restrict access to logs

---

## 12. Ethical Boundaries Beyond Law

Legal compliance is a baseline, not the goal.

BarePitch deliberately avoids:

* addictive design patterns
* unnecessary notifications
* behavioral tracking
* data monetization
* hidden data usage

The ethical stance is:

> The user is not the product.

The system serves the coach, not the other way around.

---

## 13. Risks and Tensions

### 13.1 Simplicity vs. compliance

Adding compliance features can increase complexity.

Risk: losing the minimal nature of the product.

### 13.2 Informality vs. responsibility

Amateur contexts often feel informal.

Risk: underestimating legal obligations.

### 13.3 Flexibility vs. control

Allowing free input fields increases usability.

Risk: uncontrolled storage of sensitive data.

---

## 14. Practical Design Decisions (Early)

Based on this exploration, the following directions are advised:

* No free-text fields in version 1
* No public profiles
* No cross-team visibility
* Minimal user roles
* Email + password only (no social login)
* Clear delete functionality
* Limited data fields for players
* Avoid storing dates of birth unless strictly necessary

These are not technical decisions alone, but legal and ethical safeguards.

---

## 15. Guiding Question

Throughout development:

> Am I storing or exposing anything I would not accept for my own child or team?

If the answer is yes or uncertain, the design should be reconsidered.

---

## Closing Reflection

There is a subtle tension in this project.

You are building something minimal, but the moment you deal with people and especially young players, the system becomes relational and sensitive.

The risk is not that you intentionally build something wrong.
The risk is that small, practical shortcuts slowly accumulate into something you did not intend.

Where do you see yourself most likely to take such a shortcut?

That is probably the place where you need the strongest guardrail.
