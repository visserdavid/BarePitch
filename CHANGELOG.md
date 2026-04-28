# Changelog

All notable changes to BarePitch are recorded here.  
The changelog is updated before tagging a new version.

---

## [Unreleased]

No changes pending.

---

## v1.1.0 — 2026-04-28

Visual redesign.

- Fresh, sporty design direction applied across all views
- Bottom navigation bar with icon labels, fixed to viewport bottom
- Context-sensitive nav slot for active team's players and matches
- Icon-only action buttons in list views (edit, archive, deactivate)
- Full CSS rewrite with design system via custom properties
- Top header stripped back to app name only
- Mobile-first layout preserved and strengthened
- Stagger animation on list rows (fade + slide, 40ms gap)
- All inline `style=""` attributes removed from views

---

## v1.0.0 — 2026-04-28

- Added custom 404 and 500 error pages (standalone HTML, Apache `ErrorDocument`)
- Committed preliminary research sketches for post-v1.0.0 roadmap

---

## v0.8.0 — 2026-04-28

- Added database-backed login rate limiting (10 attempts per 15 minutes per IP)
- Rotated CSRF token after every validated POST
- Fixed session inactivity timeout enforcement in `requireLogin()`
- Added `cookie_secure` flag to session configuration
- Added HTTP security headers on every response
- Added `public/.htaccess` for directory listing denial and HTTP→HTTPS redirect

---

## v0.7.0 — 2026-04-28

- Added responsive layout with mobile breakpoint styles
- Added bottom navigation bar for mobile users
- Made table rows tappable on touch devices

---

## v0.6.0 — 2026-04-27

- Added attendance tracking: coaches can select which players attend each match

---

## v0.5.0 — 2026-04-27

- Added match management: create, edit, archive, and view matches per team
- Match list split into upcoming and past sections

---

## v0.4.0 — 2026-04-27

- Added player management: add, edit, deactivate, and remove players per team

---

## v0.3.0 — 2026-04-24

- Added team management: create, edit, archive, and list teams

---

## v0.2.0 — 2026-04-24

- Added secure login and session management with CSRF protection
- Added i18n infrastructure (EN and NL language files)

---

## v0.1.0 — 2026-04-24

- Initialized project structure (`app/`, `database/`, `public/`, `storage/`, `docs/`)
- Established bootstrap chain, PDO database connection, and view rendering helpers

---

## v0.0.1 — 2026-04-24

- Repository initialized
- Base files added (`.gitignore`, `.env.example`, `README.md`, `CHANGELOG.md`)
- `wip` branch created

---

## How to use this file

Update this file before merging `wip` to `main` and before tagging a version.

Each entry should answer: *what can the user do now that they could not do before?*

Keep entries concise. This is not a commit log — it records meaningful progress from a product perspective.
