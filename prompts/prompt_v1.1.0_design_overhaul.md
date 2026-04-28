# Prompt: Build v1.1.0 — Design Overhaul

Follow all conventions in CLAUDE.md.

Create a checkpoint commit before making any changes:

```bash
git add .
git commit -m "checkpoint: before v1.1.0 design overhaul"
```

---

## Context

BarePitch is a PHP/MySQL application for football coaches. Version 1.0.0 is complete and functional. This prompt implements v1.1.0: a full visual redesign.

The application is used on mobile, near pitches and training grounds, often quickly and under real conditions. The design must reflect that: sporty, fresh, fast to scan, easy to operate with one hand.

**Read and apply the following skill files before writing a single line of CSS or HTML. They define the aesthetic direction, typography rules, and implementation standards. Follow them strictly.**

```
C:\Users\visse\.agents\skills\redesign-existing-projects\SKILL.md
C:\Users\visse\.agents\skills\minimalist-ui\SKILL.md
C:\Users\visse\.agents\skills\emil-design-eng\SKILL.md
```

Let the skills make the decisions on: color palette, typeface selection, spacing scale, visual tone, and component aesthetics. Do not default to safe or generic choices. Execute the skills' direction with precision.

---

## Scope

This version touches only:

- All CSS files in `public/assets/css/`
- All view files in `app/views/`
- `app/views/layouts/header.php` and `footer.php`
- A new `app/views/layouts/bottom_nav.php`
- A new `public/assets/css/nav.css`
- Icon assets or inline SVG — no external icon libraries, no CDN dependencies
- `public/assets/js/app.js` — minor enhancements only

Do not change any PHP logic, models, services, migrations, or public controller files.
Do not change `.env`, `CLAUDE.md`, or documentation files.
Do not add new routes or pages.

---

## 1. Design direction

The aesthetic is: **fresh and sporty**. Think a modern football coaching app — not a consumer fitness app, not a corporate dashboard. Clean lines, strong typographic hierarchy, purposeful use of color, and an interface that feels built for action.

Let the skills determine the exact execution. The only hard constraint from the product side is:

- Mobile-first, responsive, works from 375px upward
- All interactive elements minimum 44px touch target
- Status indicators use text labels in addition to color
- No external font CDNs — use system fonts or embed via `@font-face` if the skills require a specific typeface and a free/open-source file is available locally

---

## 2. Bottom navigation bar

Replace the current top navigation with a fixed bottom navigation bar. This is the primary navigation for mobile — reachable with the thumb without shifting grip.

### Structure

Create `app/views/layouts/bottom_nav.php`:

- Fixed to the bottom of the viewport
- Full width
- Four navigation items:
  1. **Dashboard** — house icon
  2. **Teams** → `teams.php` — shield or group icon
  3. **Account / Logout** → logout POST form — user or exit icon
  4. One slot reserved for context-sensitive navigation (active team's players or matches) — shown only when a `$activeTeamId` variable is set in the view; hidden otherwise

Each item has:
- An SVG icon (inline, no external dependency)
- A short text label below the icon
- Active state highlight when on the corresponding page
- Touch target minimum 44px height

The bar must not overlap page content — add `padding-bottom` to `<main>` equal to the bar height.

### Icons

Use clean, simple inline SVG for all icons. Draw them directly in the PHP/HTML — no icon font, no sprite file, no external CDN. Keep each SVG minimal: 24×24 viewBox, 2px stroke, no fill on line icons.

Suggested icon shapes:
- Dashboard: house outline
- Teams: shield outline
- Players (context): person with plus or group outline
- Matches (context): calendar outline
- Logout: arrow pointing out of a box (exit icon)

The skills may refine or replace these. Follow their guidance.

### Active state

Pass a `$currentPage` variable from each public controller file to indicate which nav item is active. Set it before calling `render()`:

```php
$currentPage = 'teams'; // or 'dashboard', 'players', 'matches'
render('teams/index', [..., 'currentPage' => $currentPage]);
```

The bottom nav reads `$currentPage` to apply the active class.

### Update all public controller files

Add `$currentPage` and (where relevant) `$activeTeamId` to the data array passed to `render()` in:

- `public/dashboard.php` → `$currentPage = 'dashboard'`
- `public/teams.php` → `$currentPage = 'teams'`
- `public/team_create.php` → `$currentPage = 'teams'`
- `public/team_edit.php` → `$currentPage = 'teams'`
- `public/players.php` → `$currentPage = 'players'`, `$activeTeamId = $team['id']`
- `public/player_create.php` → `$currentPage = 'players'`, `$activeTeamId = $team['id']`
- `public/player_edit.php` → `$currentPage = 'players'`, `$activeTeamId = $team['id']`
- `public/matches.php` → `$currentPage = 'matches'`, `$activeTeamId = $team['id']`
- `public/match_create.php` → `$currentPage = 'matches'`, `$activeTeamId = $team['id']`
- `public/match_edit.php` → `$currentPage = 'matches'`, `$activeTeamId = $team['id']`
- `public/match.php` → `$currentPage = 'matches'`, `$activeTeamId = $team['id']`

---

## 3. Remove top navigation

Remove or strip back the top header navigation. The bottom bar is the primary nav.

The top `<header>` may remain for:
- The app name / logo (text-based, no image required)
- The current page title or breadcrumb
- Nothing else

No logout link in the header — it moves to the bottom nav.
No team/player/match links in the header — they move to the bottom nav.

---

## 4. Icon buttons

Replace all text-based action buttons in list views and table rows with icon-only buttons. Text-heavy rows become clean, scannable, icon-driven rows.

### Icon map

| Action | Icon shape | Color hint |
|---|---|---|
| Edit / bewerken | Pencil outline | Neutral |
| Delete / verwijderen | Trash outline | Destructive (red) |
| Archive / archiveren | Box with arrow down | Warning (amber) |
| Deactivate / inactiveren | Slash circle or pause | Warning (amber) |
| View detail / bekijken | Eye or chevron right | Neutral |
| Add / toevoegen | Plus circle | Primary |
| Attendance / aanwezigheid | Checklist or clipboard | Primary |
| Back / terug | Chevron left | Neutral |

All icons are inline SVG, 20×20 viewBox, 2px stroke, no fill. Same construction as bottom nav icons.

### Accessibility

Icon-only buttons must have:

```html
<button type="submit" aria-label="<?= e(__('general.edit')) ?>" title="<?= e(__('general.edit')) ?>">
    <!-- SVG icon here -->
</button>
```

The `aria-label` and `title` attributes provide the text label for screen readers and on hover. The visible text is removed from the button itself, but the meaning is preserved.

### Where to apply icon buttons

Apply in all list and row views:

- `app/views/teams/index.php` — edit, archive per team row
- `app/views/players/index.php` — edit, deactivate per player row
- `app/views/matches/index.php` — edit, archive, view detail per match row
- `app/views/matches/detail.php` — edit match button

Do NOT use icon-only buttons for:
- Primary form submit buttons (Save, Sign in) — keep text here
- Destructive confirmations that need a clear label
- Any button where the action is ambiguous without text

For the above, keep text buttons — but restyle them with the new design system.

---

## 5. CSS rewrite

Rewrite all CSS files guided by the skills. Do not patch the existing CSS — rewrite from scratch.

Files:
- `public/assets/css/base.css`
- `public/assets/css/layout.css`
- `public/assets/css/components.css`
- `public/assets/css/forms.css`
- `public/assets/css/pages.css`
- `public/assets/css/nav.css` ← new, bottom navigation only

CSS rules that must always hold regardless of skill direction:

- CSS custom properties (`--var`) for all colors, spacing, and radii
- `box-sizing: border-box` on all elements
- All interactive elements minimum 44px touch target
- Focus outlines visible and clearly distinct (never `outline: none` without replacement)
- Bottom nav height stored in a CSS variable: `--nav-height`
- `<main>` has `padding-bottom: var(--nav-height)` so content never hides behind the bar
- No `!important` unless absolutely unavoidable
- No inline `style=""` attributes in HTML

---

## 6. Header and footer layout

Update `app/views/layouts/header.php`:
- Include `nav.css` alongside other stylesheets
- Remove navigation links from the header
- Keep app name and current page title area

Update `app/views/layouts/footer.php`:
- Include `bottom_nav.php` just before `</body>`

---

## 7. Login page exception

The login page (`app/views/auth/login.php`) does not show the bottom navigation — there is no session yet. The layout must still look intentional and complete without it.

---

## 8. View cleanup

While rewriting views for the icon buttons and new layout, also:

- Remove any `<table>` used for layout purposes — use flexbox or grid instead
- Ensure all list rows use the new CSS component classes
- Replace any remaining hardcoded color values with CSS custom properties
- Keep all `e()` output escaping and `__()` translation calls intact — do not touch logic

---

## 9. Version bump

Update `CHANGELOG.md` to add:

```markdown
## v1.1.0 — [current date]

Visual redesign.

- Fresh, sporty design direction applied across all views
- Bottom navigation bar with icon labels, fixed to viewport bottom
- Context-sensitive nav slot for active team's players and matches
- Icon-only action buttons in list views (edit, delete, archive, deactivate)
- Full CSS rewrite with design system via custom properties
- Top header stripped back to app name and page title only
- Mobile-first layout preserved and strengthened
```

---

## 10. After completing all changes

Test these screens manually at 375px width:

- [ ] Login — looks complete without bottom nav
- [ ] Dashboard — bottom nav shows Dashboard as active
- [ ] Team list — icon buttons visible, no text clutter, bottom nav shows Teams as active
- [ ] Player list — icon buttons, bottom nav shows Players, context slot links to matches
- [ ] Match list — icon buttons, bottom nav shows Matches, context slot links to players
- [ ] Match detail / attendance — save button prominent, bottom nav visible
- [ ] All bottom nav items are tappable and navigate correctly
- [ ] Logout via bottom nav works (POST form)
- [ ] Active nav item is visually distinct
- [ ] Context slot hidden on dashboard and team list (no activeTeamId)
- [ ] Context slot visible on player and match pages
- [ ] No content hidden behind bottom nav bar
- [ ] Focus outlines visible on all interactive elements
- [ ] Icon buttons have aria-label and title

Then commit:

```bash
git add .
git commit -m "feat: v1.1.0 design overhaul — sporty theme, bottom nav, icon buttons"
git checkout main
git merge wip
git tag v1.1.0
git push
git push --tags
git checkout wip
git merge main
gh release create v1.1.0 --title "v1.1.0 Design Overhaul" --notes "Fresh sporty design, fixed bottom navigation with thumb-friendly icons, icon-only action buttons in list views, full CSS rewrite with design system."
```
