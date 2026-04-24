# BarePitch – Accessibility

## 1. Purpose

This document defines how BarePitch ensures accessibility.

Accessibility is not an additional layer. It is part of building a tool that works in real situations, under pressure, on different devices, by different users.

BarePitch is designed for coaches. That includes variation in:

age
technical experience
device use
visual ability
motor precision
context of use

The goal is simple:

BarePitch should remain usable, understandable and predictable for as many users as possible, without adding unnecessary complexity.

---

## 2. Guiding Principles

BarePitch follows practical accessibility principles inspired by Web Content Accessibility Guidelines, without overengineering.

The core principles are:

Perceivable
Information must be visible and readable.

Operable
The interface must be usable with different input methods.

Understandable
The user must know what is happening.

Robust
The application should work across browsers and devices.

---

## 3. Accessibility Philosophy

BarePitch does not aim for theoretical perfection.

It aims for:

clarity over decoration;
structure over styling tricks;
simplicity over feature overload;
consistency over cleverness.

Accessibility is achieved by making the system calm, predictable and readable.

---

## 4. Semantic HTML

Accessibility starts with correct HTML.

Use meaningful elements:

```html
<header>
<nav>
<main>
<section>
<form>
<label>
<input>
<button>
```

Avoid using generic elements for structure:

```html
<div> instead of <button> for actions
<div> instead of <label> for form labels
```

Screen readers and browsers rely on semantics.

If HTML is correct, many accessibility issues disappear automatically.

---

## 5. Headings and Structure

Each page must have a clear structure.

Rules:

one main heading (`h1`) per page;
logical hierarchy (`h2`, `h3`);
no skipped levels;
headings describe content, not style.

Example:

```text
h1 Match overview
h2 Players
h2 Attendance
```

Headings help both screen readers and visual scanning.

---

## 6. Forms

Forms are central in BarePitch.

Every form field must have a label.

Example:

```html
<label for="team_name">Team name</label>
<input id="team_name" name="team_name" type="text">
```

Rules:

labels must be connected using `for` and `id`;
placeholder text is not a replacement for labels;
required fields must be clear;
error messages must be visible near the field.

Example error:

```text
Please enter a team name.
```

Forms should be usable without guessing.

---

## 7. Keyboard Accessibility

BarePitch must work without a mouse.

Test:

Tab navigation works logically;
all interactive elements are reachable;
buttons can be activated with Enter or Space;
links can be activated with Enter;
focus is visible.

Do not remove focus outlines unless replaced with a clear alternative.

A user must always know where they are.

---

## 8. Focus Management

Focus should follow the user’s actions.

After navigation:

focus moves to the main content.

After form submission with errors:

focus moves to the first error.

After successful action:

focus remains in a predictable location, usually near the success message or updated content.

Unexpected focus jumps confuse users.

---

## 9. Color and Contrast

Color must not carry meaning alone.

Examples:

Do not rely only on red to indicate errors.
Do not rely only on green to indicate success.

Combine color with:

text;
icons;
position.

Contrast must be sufficient:

text must be readable against the background;
buttons must be distinguishable;
disabled elements must still be readable.

BarePitch should favor calm but clear color use.

---

## 10. Text and Readability

Text should be:

clear;
short;
direct;
free of jargon.

Examples:

Good:

```text
Save match
Add player
You do not have access to this page
```

Avoid:

```text
Execute persistence operation
Authorization failed due to insufficient privileges
```

Font size should be readable on mobile without zoom.

Line spacing and margins should support scanning.

---

## 11. Touch and Mobile Interaction

BarePitch is likely used on mobile.

Requirements:

buttons must be large enough to tap;
interactive elements must have spacing;
no accidental taps due to crowding;
forms must be usable on small screens;
no horizontal scrolling for main actions.

Important screens:

match detail;
attendance selection;
player list.

These must work under real conditions.

---

## 12. Feedback and Status

The system must communicate clearly.

After actions:

show confirmation message;
show validation errors;
show loading states if needed.

Examples:

```text
Team created.
Player updated.
Please enter a valid date.
```

Avoid silent failures.

The user must always know what happened.

---

## 13. Error Messages

Error messages must be:

specific;
actionable;
visible near the problem;
not dependent on color alone.

Example:

```text
Please enter a player name.
```

Not:

```text
Invalid input.
```

Errors should help the user recover quickly.

---

## 14. Consistency

Consistency is one of the strongest accessibility tools.

Rules:

buttons behave the same across pages;
forms follow the same layout;
navigation stays predictable;
status messages look the same;
icons have consistent meaning.

If a user learns one screen, they should understand the next.

---

## 15. Avoiding Cognitive Overload

BarePitch should not overwhelm the user.

Avoid:

too many options at once;
dense dashboards;
complex filters;
unnecessary data fields;
hidden logic.

Focus on:

one clear action per screen;
simple lists;
clear hierarchy.

The best accessibility improvement is often removing unnecessary complexity.

---

## 16. Screen Reader Considerations

BarePitch should remain usable with screen readers.

Basic support comes from:

semantic HTML;
labels on inputs;
logical heading structure;
buttons and links used correctly.

Additional considerations:

use `aria-live` for important messages if needed;
avoid dynamic content that appears without context;
ensure forms announce errors clearly.

Do not overuse ARIA. Correct HTML solves most issues.

---

## 17. Responsive Layout

Accessibility and responsiveness are connected.

Test:

small screens
medium screens
large screens

Check:

content reflows properly;
text remains readable;
navigation adapts clearly;
no content becomes hidden or unreachable;
forms remain usable.

Mobile-first design supports accessibility by default.

---

## 18. Performance and Accessibility

Performance affects accessibility.

Slow interfaces create friction.

BarePitch should:

load quickly;
avoid heavy scripts;
avoid large images;
respond quickly to user input.

A fast interface is easier to use.

---

## 19. Testing Accessibility

Accessibility testing should be practical.

Test:

keyboard-only navigation;
mobile use;
zoom to 200 percent;
contrast in different lighting;
screen reader basic navigation;
error handling clarity;
focus visibility;
form usability.

You do not need specialized tools for basic checks.

You need attention.

---

## 20. Common Accessibility Mistakes

Avoid:

using color only for meaning;
missing labels on inputs;
clickable `div` elements instead of buttons;
removing focus outlines;
tiny touch targets;
hidden important actions;
inconsistent navigation;
complex layouts without clear hierarchy;
error messages far from the field;
overuse of placeholders instead of labels.

These issues often appear when focusing on visuals before structure.

---

## 21. Accessibility and Minimalism

BarePitch’s minimal philosophy supports accessibility.

By limiting:

data fields;
features;
visual clutter;
interactions;

the system becomes easier to understand and use.

Minimalism is not only aesthetic. It is functional.

---

## 22. Definition of Done

Accessibility is sufficient when:

all forms have labels;
the application works with keyboard navigation;
focus is visible and logical;
error messages are clear and near the problem;
color is not the only way to convey meaning;
text is readable on mobile;
buttons are easy to tap;
navigation is consistent;
HTML is semantic;
main flows work without confusion.

---

## 23. Guiding Question

Every accessibility decision should be tested against this:

Can someone use this quickly, without explanation, on a small screen, under real conditions?

If not, the design needs to become simpler or clearer.
