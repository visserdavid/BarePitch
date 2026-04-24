# BarePitch – Concept Document

## 1. Context

BarePitch is a lightweight web application designed to support football coaches in managing their teams with clarity and minimal distraction.

The project emerges from a practical frustration: most existing tools are either too complex, too cluttered, or too far removed from how coaches actually think and work during training and match days.

BarePitch aims to strip away everything that is not essential, and present only what matters, at the moment it matters.

**Working tagline**
BarePitch shows what matters, when it matters. Nothing more.

---

## 2. Problem Definition

Coaches at amateur level face a recurring set of challenges:

* Too many disconnected tools (apps, spreadsheets, notes)
* Overly complex systems with unnecessary features
* Lack of clarity during time-sensitive moments (matches, training)
* Administrative overhead that distracts from coaching itself
* Poor alignment between digital tools and real-world workflows

Most tools try to do everything. As a result, they dilute focus.

The core problem BarePitch addresses:

> How can a coach manage a team efficiently, without being pulled away from the game itself?

---

## 3. Vision

BarePitch is not a feature-rich platform. It is a focused tool.

The vision is to create a system that:

* reduces cognitive load instead of adding to it
* mirrors how coaches already think and act
* supports decision-making in real time
* stays out of the way when not needed

The application should feel calm, direct, and intentional.

---

## 4. Target Users

Primary user:

* Amateur football coaches (youth and adult teams)
* Often volunteers or semi-professionals
* Limited time for administration
* Prefer clarity over complexity

Secondary users (later phase):

* Assistant coaches
* Team managers
* Players (limited interaction)

---

## 5. Core Use Cases (Version 1)

BarePitch focuses on a minimal but complete workflow.

### 5.1 Authentication

* User can log in securely
* User session is maintained safely

### 5.2 Team Management

* Create and manage teams
* View team overview

### 5.3 Player Management

* Add and edit players
* Assign players to teams

### 5.4 Match Management

* Create matches
* Define opponent, date, and context

### 5.5 Attendance Tracking

* Select players for a match
* Mark attendance or availability

These use cases form a complete loop: from team structure to match preparation.

---

## 6. Non-Goals (Version 1)

Explicit exclusions help maintain focus.

BarePitch will not initially include:

* Advanced statistics and analytics
* Communication features (chat, notifications)
* Financial or administrative modules
* Integration with external systems
* Media storage (photos, videos)
* Complex role and permission systems
* Detailed tactical analysis tools

These may be considered later, but are not part of the core concept.

---

## 7. Design Principles

### 7.1 Minimalism with purpose

Every element must justify its existence.

If something does not directly support the user’s task, it should not be there.

### 7.2 Clarity over flexibility

Clear structure is preferred over endless configuration.

### 7.3 Context-driven interface

The system shows only what is relevant in the current moment.

### 7.4 Low cognitive load

The user should not have to think about the system itself.

### 7.5 Consistency

Similar actions behave in similar ways throughout the application.

---

## 8. Functional Boundaries

BarePitch operates within a clearly defined scope:

* Single user or small team usage
* Web-based application
* No dependency on frameworks
* Built with PHP, MySQL, JavaScript, HTML, CSS
* Data stored centrally, accessed via browser

---

## 9. Data Considerations

The system will store limited personal data:

* User account (email, password)
* Player names (and possibly minimal identifiers)

Principles:

* Data minimization (store only what is necessary)
* No sensitive personal data
* Clear ownership of data by the user
* Ability to remove data when needed

---

## 10. Risks and Tensions

### 10.1 Simplicity vs. completeness

There is a constant risk of adding “just one more feature.”

This must be actively resisted.

### 10.2 Speed vs. structure

Rapid development (vibe coding) can lead to messy foundations.

A minimal structure must still be maintained.

### 10.3 Intuition vs. consistency

Designing intuitively may introduce inconsistencies.

Regular reflection and cleanup are required.

---

## 11. Success Criteria

BarePitch is successful if:

* A coach can manage a match without switching tools
* The interface feels obvious without explanation
* The system reduces time spent on administration
* The user does not feel overwhelmed
* The application remains understandable after growth

---

## 12. First Milestone

A working Version 0.1 should include:

* Basic project structure
* Database connection
* User authentication
* Simple team and player management

The goal is not completeness, but a working foundation.

---

## 13. Guiding Question

Throughout development, one question remains central:

> Does this help the coach in the moment, or does it add noise?

If it adds noise, it does not belong in BarePitch.
