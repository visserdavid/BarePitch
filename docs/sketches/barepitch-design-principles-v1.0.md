# BarePitch — Design Principles Guide
Version 1.0 — April 2026

---

## 1. Purpose

This document translates the BarePitch tagline into concrete design rules.

Tagline:

BarePitch shows what matters, when it matters. Nothing more.

This guide defines:
- what “matters”
- when it matters
- what must be excluded
- how to apply this in UI and feature decisions

---

## 2. Core Principle

BarePitch only shows information that directly supports action in the current moment.

If information does not:
- apply to the current context
- influence a decision
- provide immediate clarity

it must not be shown.

---

## 3. Definition of “What Matters”

Information matters when it meets all three conditions:

1. It is current  
2. It directly influences a decision  
3. It requires no interpretation  

If one condition is not met, the information must not be shown.

---

## 4. Definition of “When It Matters”

Context determines relevance.

Application states:
- planned
- prepared
- active
- finished

Information visibility must adapt to these states.

---

## 5. Screen-Level Rules

---

### 5.1 Livestream (Audience)

Purpose:
Follow the match.

Show:
- score
- time
- goals
- cards
- substitutions
- match phase

Do not show:
- lineup details
- statistics
- ratings
- attendance
- historical data

---

### 5.2 Coach — During Match

Purpose:
Make decisions.

Show:
- current lineup
- players on field
- bench players
- substitutions
- cards
- score
- time

Do not show:
- statistics
- historical data
- training data
- ratings

---

### 5.3 Coach — Before Match

Purpose:
Prepare lineup.

Show:
- available players
- attendance
- formation
- lineup
- guest players

Do not show:
- match events
- live data
- ratings

---

### 5.4 After Match

Purpose:
Review and correct.

Show:
- events
- goals
- assists
- cards
- playing time

Optional:
- ratings (only if complete)

Do not show by default:
- complex statistics
- analysis dashboards

---

## 6. Design Rules

---

### Rule 1 — One Purpose per Screen

Each screen must serve one primary goal.

If multiple goals exist:
- split into separate screens

---

### Rule 2 — No Parallel Information Streams

Avoid combining:
- live data + statistics
- actions + analysis

---

### Rule 3 — Default is Minimal

Additional information:
- must be explicitly requested
- or should not exist

---

### Rule 4 — Time Determines Visibility

Information must adapt to match state.

---

### Rule 5 — Action First

If action is required:
- show action first
- show context second

---

### Rule 6 — No “Nice to Have”

Every feature must answer:

Does this directly help the user now?

If not:
- do not build it

---

## 7. Practical Examples

---

### Example: Season Goals

- not relevant during match
- not relevant for lineup

Conclusion:
Do not show by default

---

### Example: Assist Leader

- no direct action
- no immediate relevance

Conclusion:
Exclude

---

### Example: Yellow Card Risk

- influences behavior
- affects decisions

Conclusion:
Include

---

## 8. Validation Questions

For every feature:

1. When is this used?  
2. What decision does it support?  
3. What happens if it is removed?  

If removal has no impact:
- the feature is not needed

---

## 9. Philosophy

BarePitch is not an analytics platform.

It is a tool for:
- clarity
- presence
- decision-making

It removes:
- noise
- excess
- distraction

---

## 10. Summary

BarePitch design is based on:

- context-driven visibility
- minimal interfaces
- action-oriented information
- strict feature discipline

Nothing more.

---

## End
