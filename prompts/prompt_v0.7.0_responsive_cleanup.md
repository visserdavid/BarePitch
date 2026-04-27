# Prompt: Build v0.7.0 — Responsive and Accessibility Cleanup

Follow all conventions in CLAUDE.md.

Create a checkpoint commit before making any changes:

```bash
git add .
git commit -m "checkpoint: before building v0.7.0 responsive and accessibility cleanup"
```

---

## Context

BarePitch is a minimal PHP/MySQL application for football coaches. The full core feature set is complete as of v0.6.0. This prompt implements v0.7.0: a dedicated pass to make the application reliably usable on mobile devices and accessible by default.

This version adds no new features. It only improves what already exists.

The guiding question for every change in this version:

> Can someone use this quickly, without explanation, on a small screen, under real conditions?

Stack: plain PHP 8.x, MySQL 8 via PDO, HTML, CSS, vanilla JS. No frameworks, no Composer. No new PHP files unless strictly necessary.

---

## Scope

This version touches only:

- CSS files in `public/assets/css/`
- HTML structure in `app/views/` (semantic corrections only, no logic changes)
- Optional minor JS in `public/assets/js/` (enhancement only)
- `app/views/layouts/header.php` and `footer.php`

Do not change any PHP logic, models, services, or public controller files unless a specific HTML correction requires it.

---

## 1. Viewport and base HTML

Verify that `app/views/layouts/header.php` includes:

```html
<meta name="viewport" content="width=device-width, initial-scale=1">
```

If missing, add it. This is required for mobile rendering.

Also confirm:
- `<html lang="nl">` (or `en`, matching `APP_LANG`) is set
- `<meta charset="UTF-8">` is present
- The `<title>` tag is populated per page

---

## 2. CSS: base.css

Review and update `public/assets/css/base.css` to ensure:

```css
/* Box model */
*, *::before, *::after {
    box-sizing: border-box;
}

/* Body */
body {
    margin: 0;
    font-family: system-ui, -apple-system, sans-serif;
    font-size: 1rem;
    line-height: 1.5;
    color: #1a1a1a;
    background-color: #f8f8f8;
}

/* Links */
a {
    color: #1a56db;
    text-decoration: underline;
}

a:hover,
a:focus {
    text-decoration: none;
    outline: 2px solid #1a56db;
    outline-offset: 2px;
}

/* Headings */
h1, h2, h3 {
    line-height: 1.2;
    margin-top: 0;
}

/* Images */
img {
    max-width: 100%;
    height: auto;
}
```

---

## 3. CSS: layout.css

Update `public/assets/css/layout.css` for mobile-first responsive layout:

```css
/* Page container */
.container {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Header */
.site-header {
    background-color: #1a1a1a;
    color: #ffffff;
    padding: 0.75rem 1rem;
}

.site-header a {
    color: #ffffff;
}

/* Navigation */
.site-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
    padding: 0.5rem 0;
}

.site-nav a {
    color: #ffffff;
    text-decoration: none;
    font-size: 0.95rem;
    padding: 0.25rem 0;
    min-height: 44px;
    display: inline-flex;
    align-items: center;
}

.site-nav a:hover,
.site-nav a:focus {
    text-decoration: underline;
    outline: 2px solid #ffffff;
    outline-offset: 2px;
}

/* Main content */
.site-main {
    padding: 1.5rem 1rem;
}

/* Page title */
.page-title {
    font-size: 1.5rem;
    margin-bottom: 1.25rem;
}

/* Section title */
.section-title {
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 0.4rem;
}

/* Footer */
.site-footer {
    padding: 1.5rem 1rem;
    border-top: 1px solid #e0e0e0;
    font-size: 0.875rem;
    color: #666;
    text-align: center;
}
```

---

## 4. CSS: components.css

Review and update `public/assets/css/components.css` to ensure all components are mobile-ready:

### Buttons
```css
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    padding: 0.5rem 1.25rem;
    font-size: 1rem;
    font-weight: 500;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
}

.btn-primary {
    background-color: #1a56db;
    color: #ffffff;
}

.btn-primary:hover,
.btn-primary:focus {
    background-color: #1348c7;
    outline: 2px solid #1a56db;
    outline-offset: 2px;
}

.btn-secondary {
    background-color: #f0f0f0;
    color: #1a1a1a;
    border: 1px solid #ccc;
}

.btn-secondary:hover,
.btn-secondary:focus {
    background-color: #e0e0e0;
    outline: 2px solid #666;
    outline-offset: 2px;
}

.btn-danger {
    background-color: #c0392b;
    color: #ffffff;
}

.btn-danger:hover,
.btn-danger:focus {
    background-color: #a93226;
    outline: 2px solid #c0392b;
    outline-offset: 2px;
}

/* Full-width button on mobile */
.btn-block {
    display: flex;
    width: 100%;
}
```

### Flash messages
```css
.flash {
    padding: 0.875rem 1rem;
    border-radius: 4px;
    margin-bottom: 1.25rem;
    font-size: 0.95rem;
}

.flash--success {
    background-color: #eafaf1;
    border-left: 4px solid #27ae60;
    color: #1a5e35;
}

.flash--error {
    background-color: #fdf3f2;
    border-left: 4px solid #c0392b;
    color: #7b241c;
}
```

### Lists and tables
```css
/* Item list (teams, players, matches) */
.item-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.item-list__row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e8e8e8;
}

.item-list__row:last-child {
    border-bottom: none;
}

.item-list__name {
    flex: 1;
    font-weight: 500;
    min-width: 0;
    word-break: break-word;
}

.item-list__meta {
    font-size: 0.875rem;
    color: #666;
}

.item-list__actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Empty state */
.empty-state {
    padding: 2rem 1rem;
    text-align: center;
    color: #666;
}

/* Badge */
.badge {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 3px;
    font-size: 0.8rem;
    font-weight: 500;
}

.badge--home   { background: #eaf4fb; color: #1a56db; }
.badge--away   { background: #fef9e7; color: #b7770d; }
.badge--neutral { background: #f0f0f0; color: #444; }
.badge--inactive { background: #f0f0f0; color: #888; }
.badge--selected { background: #eafaf1; color: #1a5e35; border: 1px solid #27ae60; }
.badge--available { background: #eaf4fb; color: #1a56db; }
.badge--unavailable { background: #fdf3f2; color: #7b241c; }
.badge--unknown { background: #f5f5f5; color: #666; }
```

---

## 5. CSS: forms.css

Review and update `public/assets/css/forms.css`:

```css
/* Form group: label + input stacked */
.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.4rem;
    font-size: 0.95rem;
}

/* Inputs */
.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"],
.form-group input[type="date"],
.form-group input[type="time"],
.form-group input[type="number"],
.form-group select,
.form-group textarea {
    display: block;
    width: 100%;
    min-height: 44px;
    padding: 0.5rem 0.75rem;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #ffffff;
    color: #1a1a1a;
    appearance: none;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: 2px solid #1a56db;
    outline-offset: 0;
    border-color: #1a56db;
}

/* Validation error */
.form-error {
    display: block;
    margin-top: 0.3rem;
    font-size: 0.875rem;
    color: #c0392b;
    font-weight: 500;
}

/* Input in error state */
.form-group--error input,
.form-group--error select {
    border-color: #c0392b;
}

/* Form action row */
.form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1.5rem;
    align-items: center;
}

/* Required field indicator */
.required-mark {
    color: #c0392b;
    margin-left: 0.15rem;
}
```

---

## 6. CSS: pages.css

Create or update `public/assets/css/pages.css` for page-specific styles:

```css
/* Attendance screen */
.attendance-list {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem;
}

.attendance-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e8e8e8;
}

.attendance-row:last-child {
    border-bottom: none;
}

.attendance-row--selected {
    background-color: #f0faf4;
    border-left: 3px solid #27ae60;
    padding-left: 0.75rem;
    margin-left: -0.75rem;
}

.attendance-player {
    flex: 1;
    min-width: 120px;
}

.attendance-player__number {
    font-size: 0.8rem;
    color: #888;
    margin-right: 0.3rem;
}

.attendance-player__name {
    font-weight: 500;
}

.attendance-status select {
    min-height: 44px;
    padding: 0.4rem 0.5rem;
    font-size: 0.95rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #fff;
}

.attendance-save {
    margin-top: 1.5rem;
}

.attendance-summary {
    font-size: 0.875rem;
    color: #555;
    margin-bottom: 1rem;
}
```

---

## 7. HTML audit: all views

Audit all views in `app/views/` for the following. Fix each issue found.

### Semantic structure
- Each page has exactly one `<h1>` that describes the page content
- Heading levels are logical: `h1` → `h2` → `h3`, no skipped levels
- `<main>` wraps the primary content area on every page
- `<header>` and `<nav>` are used in the layout, not `<div>`

### Forms
- Every `<input>`, `<select>`, and `<textarea>` has a corresponding `<label>` with matching `for` and `id`
- Placeholder text is not used as the only label
- Required fields are marked — either visually or with `required` attribute
- Error messages are placed directly after the relevant field, not at the top of the form only
- Submit buttons use `<button type="submit">` not `<input type="submit">`

### Buttons and links
- Destructive actions (archive, delete, deactivate) use `<button>` inside a `<form>`, not a plain link
- Navigation between pages uses `<a>`, not `<button>`
- No `<div>` or `<span>` is used as a clickable action

### Focus
- No `outline: none` or `outline: 0` without a visible replacement
- All interactive elements are reachable via Tab key in a logical order

### Color
- Status indicators (available, selected, unavailable, inactive) use text labels in addition to color
- Error messages are not indicated by color alone — include an icon character or text prefix like "Error:" or "✕"

---

## 8. Navigation consistency

Review `app/views/layouts/header.php`.

Ensure the navigation shows:
- Dashboard link
- Teams link
- Logout form (POST with CSRF, not a plain link)
- Current user display name (from session)

Navigation must be consistent across all pages. If the current page is active, the link may be visually highlighted, but this is optional.

On small screens, the navigation links must remain accessible — either wrapping naturally or using a simple stacked layout. No hamburger menu or JavaScript toggle required.

---

## 9. Priority screens for mobile

The following screens are the most important for mobile use. Review each one specifically after applying the CSS changes above.

**Attendance screen (`app/views/matches/detail.php`)**
- Player rows must not overflow horizontally
- Status select must be easy to tap (min 44px height)
- Save button must be prominent and easy to reach at the bottom
- Selected players must be visually distinct without relying on color alone
- Summary line must be readable at small font sizes

**Match list (`app/views/matches/index.php`)**
- Upcoming and past sections must be clearly separated
- Opponent name must not be truncated
- Date and status must be visible without horizontal scrolling
- Create match button must be easy to find

**Player list (`app/views/players/index.php`)**
- Shirt number + name + status must fit on one row at 375px width
- Edit link must be large enough to tap
- Inactive players must be clearly marked but not hidden

**Login screen (`app/views/auth/login.php`)**
- Email and password fields must be full width
- Submit button must be full width on mobile
- Error message must appear before the form, clearly visible

---

## 10. Zoom and readability

Verify that at 200% zoom:
- No content is hidden or overlaps
- Forms remain usable
- Navigation remains accessible
- Text does not overflow its container

Use `overflow-wrap: break-word` on text containers where long team names or opponent names could cause overflow.

---

## 11. Optional: JavaScript enhancements

These are strictly optional. All must degrade gracefully without JavaScript.

**Confirm destructive actions**

Add a small inline confirmation for archive and deactivate buttons:

```js
// public/assets/js/app.js
document.querySelectorAll('[data-confirm]').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        if (!window.confirm(btn.dataset.confirm)) {
            e.preventDefault();
        }
    });
});
```

Add `data-confirm="Are you sure?"` (or the Dutch equivalent via a `data-confirm` attribute populated in the view using `__('general.confirm')`) to archive and deactivate submit buttons.

**Attendance row highlight**

In `public/assets/js/match_attendance.js` (if not already present):

```js
document.querySelectorAll('.attendance-status select').forEach(function(sel) {
    sel.addEventListener('change', function() {
        var row = sel.closest('.attendance-row');
        if (!row) return;
        row.classList.toggle('attendance-row--selected', sel.value === 'selected');
    });
});
```

This updates the visual highlight immediately on change, without a page reload.

---

## 12. Performance check

Verify:
- No large images are loaded (BarePitch has none, but confirm no accidental embeds)
- CSS files are linked in `<head>`, not at the bottom
- JS files are loaded at the bottom of `<body>`, not in `<head>` (or use `defer`)
- No render-blocking inline scripts

---

## After completing all changes

1. Test the following screens manually at 375px width (mobile):
   - Login
   - Dashboard
   - Team list
   - Player list
   - Match list
   - Match detail / attendance

2. Test keyboard navigation on at least one full flow (login → team → match → attendance → logout)

3. Commit:
```bash
git add .
git commit -m "cleanup: responsive layout, accessibility audit, mobile-first CSS"
```

4. When all screens pass the mobile review:
```bash
git checkout main
git merge wip
git tag v0.7.0
git push
git push --tags
git checkout wip
git merge main
gh release create v0.7.0 --title "v0.7.0 Responsive and Accessibility Cleanup" --notes "Mobile-first CSS, semantic HTML audit, keyboard accessibility, accessible form layout, attendance screen optimised for mobile use."
```

5. Confirm:
- [ ] All pages have a single `<h1>` and logical heading hierarchy
- [ ] All form fields have connected `<label>` elements
- [ ] No `outline: none` without replacement
- [ ] All interactive elements reachable via Tab
- [ ] Status indicators use text labels, not color alone
- [ ] Buttons are minimum 44px height
- [ ] Login form is full-width on mobile
- [ ] Attendance screen is usable at 375px width
- [ ] Player list fits at 375px without horizontal scroll
- [ ] Match list shows date and opponent without horizontal scroll
- [ ] Navigation is consistent across all pages
- [ ] No content overflows at 200% zoom
- [ ] Destructive actions have confirmation (JS optional but preferred)
- [ ] Attendance row highlight works on status change (JS optional)
- [ ] All CSS is in CSS files, no inline style attributes in views
