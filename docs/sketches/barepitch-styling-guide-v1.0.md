# BarePitch — Styling Guide

**Version 1.0 — April 2026**

---

## 1. Purpose

This document defines the styling system for BarePitch.

The styling system must be:

- lightweight
- framework-free
- predictable
- mobile-first
- readable in outdoor conditions
- easy to maintain on shared hosting

No external CSS framework is required.

---

## 2. Architecture

### File Structure

The recommended CSS structure is:

- `base.css`
- `layout.css`
- `components.css`

Optional:

- `utilities.css`

---

### Loading Order

```html
<link rel="stylesheet" href="/css/base.css" />
<link rel="stylesheet" href="/css/layout.css" />
<link rel="stylesheet" href="/css/components.css" />
```

---

## 3. Design Tokens

All visual consistency is controlled through CSS variables.

### Root Variables

```css
:root {
  /* Colors */
  --color-primary: #1259a8;
  --color-primary-light: #3a7bd5;
  --color-accent: #e8720c;

  --color-bg: #f8fafc;
  --color-surface: #ffffff;

  --color-text: #1f2937;
  --color-text-muted: #6b7280;

  --color-success: #16a34a;
  --color-warning: #d97706;
  --color-danger: #dc2626;

  --color-border: #e5e7eb;

  /* Spacing */
  --space-xs: 4px;
  --space-sm: 8px;
  --space-md: 16px;
  --space-lg: 24px;
  --space-xl: 32px;

  /* Radius */
  --radius-sm: 6px;
  --radius-md: 10px;
  --radius-lg: 16px;

  /* Typography */
  --font-base: system-ui, -apple-system, sans-serif;
  --font-size-sm: 14px;
  --font-size-md: 16px;
  --font-size-lg: 18px;

  /* Shadows */
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 10px rgba(0, 0, 0, 0.08);
}
```

---

## 4. Base Styles

### Reset

```css
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: var(--font-base);
  font-size: var(--font-size-md);
  color: var(--color-text);
  background: var(--color-bg);
}
```

---

### Typography

```css
h1,
h2,
h3 {
  margin: 0 0 var(--space-md);
  font-weight: 600;
}

p {
  margin: 0 0 var(--space-md);
}
```

---

## 5. Layout

### Page Container

```css
.container {
  max-width: 960px;
  margin: 0 auto;
  padding: var(--space-md);
}
```

---

### Navigation

```css
.nav {
  display: flex;
  gap: var(--space-md);
}

.nav-item {
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--radius-md);
  cursor: pointer;
}

.nav-item.active {
  background: var(--color-primary);
  color: #ffffff;
}
```

---

## 6. Components

### Buttons

```css
.btn {
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--radius-md);
  border: none;
  cursor: pointer;
  font-size: var(--font-size-md);
}

.btn-primary {
  background: var(--color-primary);
  color: #ffffff;
}

.btn-danger {
  background: var(--color-danger);
  color: #ffffff;
}
```

---

### Cards

```css
.card {
  background: var(--color-surface);
  border-radius: var(--radius-md);
  padding: var(--space-md);
  box-shadow: var(--shadow-sm);
}
```

---

### Badges

```css
.badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: var(--radius-sm);
  font-size: var(--font-size-sm);
}

.badge-success {
  background: var(--color-success);
  color: #ffffff;
}

.badge-warning {
  background: var(--color-warning);
  color: #ffffff;
}

.badge-danger {
  background: var(--color-danger);
  color: #ffffff;
}
```

---

### Form Fields

```css
.input,
.select,
.textarea {
  width: 100%;
  padding: var(--space-sm);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  font-size: var(--font-size-md);
  background: #ffffff;
  color: var(--color-text);
}
```

---

## 7. Match Components

### Player Chip

```css
.player-chip {
  padding: var(--space-sm);
  border-radius: var(--radius-md);
  background: var(--color-surface);
  box-shadow: var(--shadow-sm);
}
```

---

### Lineup Grid

```css
.lineup-grid {
  display: grid;
  grid-template-columns: repeat(11, 1fr);
  grid-template-rows: repeat(10, 40px);
  gap: 4px;
}
```

---

### Lineup Slot

```css
.lineup-slot {
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-primary-light);
  border-radius: var(--radius-sm);
  color: #ffffff;
  font-size: var(--font-size-sm);
}
```

---

### Timeline Item

```css
.timeline-item {
  display: flex;
  gap: var(--space-sm);
  padding: var(--space-sm) 0;
}
```

---

## 8. Interaction Rules

### Touch Targets

All interactive elements must have:

- minimum size: 44px × 44px

---

### States

Each interactive component must support:

- default
- active
- disabled

Hover may be added for desktop use, but mobile behavior is primary.

---

## 9. Mobile First

Rules:

- design mobile first
- avoid fixed widths where possible
- prefer flexbox and grid
- avoid horizontal scrolling
- preserve clear spacing between tap targets

---

## 10. Performance Rules

The styling system must remain lightweight.

Rules:

- no external CSS framework
- no CSS preprocessing required
- no runtime style generation
- no server-side build requirement
- CSS size should remain minimal and feature-driven

---

## 11. Recommended Naming Style

Use semantic component-oriented class names.

Recommended examples:

- `.btn`
- `.btn-primary`
- `.card`
- `.badge`
- `.nav-item`
- `.player-chip`
- `.lineup-grid`
- `.lineup-slot`
- `.timeline-item`

Do not rely on excessive utility-class patterns.

---

## 12. Constraints

The styling system must NOT include:

- Tailwind
- Bootstrap
- Sass/SCSS as a requirement
- CSS build pipelines
- heavy animation systems
- JavaScript-dependent layout rendering

---

## 13. Summary

The BarePitch styling system is based on:

- plain CSS
- CSS variables
- semantic class names
- modular file separation
- mobile-first structure
- minimal overhead

This approach is appropriate for:

- shared hosting
- PHP-rendered views
- low-maintenance deployment
- predictable long-term development

---

## End
