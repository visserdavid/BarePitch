# Prompt: Add i18n infrastructure to BarePitch

Follow all conventions in CLAUDE.md.

Create a checkpoint commit before making any changes:

```bash
git add .
git commit -m "checkpoint: before adding i18n infrastructure"
```

---

## Context

BarePitch is developed in English (code, comments, keys) but will be used in Dutch by default. Other users may download and use it in their own language. A lightweight PHP array-based translation system must be added — no external libraries, no framework.

---

## Your task

### 1. Add APP_LANG to configuration

In `.env`, add:

```
APP_LANG=nl
```

In `.env.example`, add:

```
APP_LANG=nl
```

---

### 2. Create language files

**`lang/en.php`** — English strings (canonical keys and fallback)

```php
<?php

return [
    // Auth
    'auth.login.title'            => 'Sign in',
    'auth.login.email'            => 'Email address',
    'auth.login.password'         => 'Password',
    'auth.login.submit'           => 'Sign in',
    'auth.login.error'            => 'Invalid email or password.',
    'auth.logout'                 => 'Sign out',

    // Dashboard
    'dashboard.title'             => 'Dashboard',
    'dashboard.welcome'           => 'Welcome, :name',
    'dashboard.no_teams'          => 'You have not created any teams yet.',

    // Teams
    'teams.title'                 => 'Teams',
    'teams.create'                => 'Create team',
    'teams.edit'                  => 'Edit team',
    'teams.name'                  => 'Team name',
    'teams.season'                => 'Season',
    'teams.status'                => 'Status',
    'teams.created'               => 'Team created.',
    'teams.updated'               => 'Team updated.',
    'teams.archived'              => 'Team archived.',
    'teams.empty'                 => 'No teams found.',

    // Players
    'players.title'               => 'Players',
    'players.add'                 => 'Add player',
    'players.edit'                => 'Edit player',
    'players.display_name'        => 'Name',
    'players.shirt_number'        => 'Shirt number',
    'players.status'              => 'Status',
    'players.created'             => 'Player added.',
    'players.updated'             => 'Player updated.',
    'players.deactivated'         => 'Player set to inactive.',
    'players.empty'               => 'No players found.',

    // Matches
    'matches.title'               => 'Matches',
    'matches.create'              => 'Create match',
    'matches.edit'                => 'Edit match',
    'matches.opponent'            => 'Opponent',
    'matches.date'                => 'Match date',
    'matches.kickoff'             => 'Kickoff time',
    'matches.location'            => 'Location',
    'matches.home_away'           => 'Home / Away',
    'matches.status'              => 'Status',
    'matches.created'             => 'Match created.',
    'matches.updated'             => 'Match updated.',
    'matches.archived'            => 'Match archived.',
    'matches.empty'               => 'No matches found.',

    // Attendance
    'attendance.title'            => 'Attendance',
    'attendance.save'             => 'Save attendance',
    'attendance.saved'            => 'Attendance saved.',
    'attendance.status.unknown'   => 'Unknown',
    'attendance.status.available' => 'Available',
    'attendance.status.unavailable' => 'Unavailable',
    'attendance.status.selected'  => 'Selected',

    // Validation
    'validation.required'         => 'This field is required.',
    'validation.too_long'         => 'This field is too long.',
    'validation.invalid_email'    => 'Please enter a valid email address.',
    'validation.invalid_date'     => 'Please enter a valid date.',
    'validation.password_too_short' => 'Password must be at least 10 characters.',
    'validation.invalid_status'   => 'Invalid status value.',

    // Errors
    'error.not_found'             => 'Page not found.',
    'error.unauthorized'          => 'You do not have access to this page.',
    'error.invalid_request'       => 'Invalid request.',
    'error.generic'               => 'Something went wrong. Please try again.',

    // General
    'general.save'                => 'Save',
    'general.cancel'              => 'Cancel',
    'general.back'                => 'Back',
    'general.edit'                => 'Edit',
    'general.archive'             => 'Archive',
    'general.confirm'             => 'Are you sure?',
];
```

**`lang/nl.php`** — Dutch translations (same keys, Dutch values)

```php
<?php

return [
    // Auth
    'auth.login.title'            => 'Inloggen',
    'auth.login.email'            => 'E-mailadres',
    'auth.login.password'         => 'Wachtwoord',
    'auth.login.submit'           => 'Inloggen',
    'auth.login.error'            => 'Ongeldig e-mailadres of wachtwoord.',
    'auth.logout'                 => 'Uitloggen',

    // Dashboard
    'dashboard.title'             => 'Dashboard',
    'dashboard.welcome'           => 'Welkom, :name',
    'dashboard.no_teams'          => 'Je hebt nog geen teams aangemaakt.',

    // Teams
    'teams.title'                 => 'Teams',
    'teams.create'                => 'Team aanmaken',
    'teams.edit'                  => 'Team bewerken',
    'teams.name'                  => 'Teamnaam',
    'teams.season'                => 'Seizoen',
    'teams.status'                => 'Status',
    'teams.created'               => 'Team aangemaakt.',
    'teams.updated'               => 'Team bijgewerkt.',
    'teams.archived'              => 'Team gearchiveerd.',
    'teams.empty'                 => 'Geen teams gevonden.',

    // Players
    'players.title'               => 'Spelers',
    'players.add'                 => 'Speler toevoegen',
    'players.edit'                => 'Speler bewerken',
    'players.display_name'        => 'Naam',
    'players.shirt_number'        => 'Rugnummer',
    'players.status'              => 'Status',
    'players.created'             => 'Speler toegevoegd.',
    'players.updated'             => 'Speler bijgewerkt.',
    'players.deactivated'         => 'Speler op inactief gezet.',
    'players.empty'               => 'Geen spelers gevonden.',

    // Matches
    'matches.title'               => 'Wedstrijden',
    'matches.create'              => 'Wedstrijd aanmaken',
    'matches.edit'                => 'Wedstrijd bewerken',
    'matches.opponent'            => 'Tegenstander',
    'matches.date'                => 'Wedstrijddatum',
    'matches.kickoff'             => 'Aanvangstijd',
    'matches.location'            => 'Locatie',
    'matches.home_away'           => 'Thuis / Uit',
    'matches.status'              => 'Status',
    'matches.created'             => 'Wedstrijd aangemaakt.',
    'matches.updated'             => 'Wedstrijd bijgewerkt.',
    'matches.archived'            => 'Wedstrijd gearchiveerd.',
    'matches.empty'               => 'Geen wedstrijden gevonden.',

    // Attendance
    'attendance.title'            => 'Aanwezigheid',
    'attendance.save'             => 'Aanwezigheid opslaan',
    'attendance.saved'            => 'Aanwezigheid opgeslagen.',
    'attendance.status.unknown'   => 'Onbekend',
    'attendance.status.available' => 'Beschikbaar',
    'attendance.status.unavailable' => 'Niet beschikbaar',
    'attendance.status.selected'  => 'Geselecteerd',

    // Validation
    'validation.required'         => 'Dit veld is verplicht.',
    'validation.too_long'         => 'Dit veld is te lang.',
    'validation.invalid_email'    => 'Voer een geldig e-mailadres in.',
    'validation.invalid_date'     => 'Voer een geldige datum in.',
    'validation.password_too_short' => 'Wachtwoord moet minimaal 10 tekens zijn.',
    'validation.invalid_status'   => 'Ongeldige statuswaarde.',

    // Errors
    'error.not_found'             => 'Pagina niet gevonden.',
    'error.unauthorized'          => 'Je hebt geen toegang tot deze pagina.',
    'error.invalid_request'       => 'Ongeldig verzoek.',
    'error.generic'               => 'Er is iets misgegaan. Probeer het opnieuw.',

    // General
    'general.save'                => 'Opslaan',
    'general.cancel'              => 'Annuleren',
    'general.back'                => 'Terug',
    'general.edit'                => 'Bewerken',
    'general.archive'             => 'Archiveren',
    'general.confirm'             => 'Weet je het zeker?',
];
```

---

### 3. Create the translation helper

**`app/helpers/lang.php`**

- Read `APP_LANG` from environment via `getenv('APP_LANG')`, default to `'en'`
- Load the matching language file from `lang/{locale}.php` once and store in a static variable
- Fall back to `lang/en.php` if the requested locale file does not exist
- Implement `__(string $key, array $replace = []): string`:
  - Look up the key in the loaded language array
  - If not found, return the key itself as fallback (never throw or crash)
  - Support simple placeholder replacement: `:name` replaced by `$replace['name']`
  - Example: `__('dashboard.welcome', ['name' => 'Jan'])` → `'Welkom, Jan'`

---

### 4. Register the helper in bootstrap

In `app/config/bootstrap.php`, require the lang helper alongside the other helpers:

```php
require __DIR__ . '/../helpers/lang.php';
```

Place it before `view.php` so views can use `__()` immediately.

---

### 5. Add i18n conventions to CLAUDE.md

Append the following section to `CLAUDE.md`:

```markdown
## i18n

Language files live in `lang/`. Each file returns a flat PHP array keyed by dot-notation strings.

- `lang/en.php` — English (canonical keys, always the fallback)
- `lang/nl.php` — Dutch

The active language is set via `APP_LANG` in `.env`.

**Usage in views**
```php
<?= e(__('teams.title')) ?>
<?= e(__('dashboard.welcome', ['name' => $displayName])) ?>
```

**Rules**
- All user-facing strings in views must use `__()`  — never hardcode interface text
- New strings are added to both `lang/en.php` and `lang/nl.php` at the same time
- Keys use dot-notation grouped by feature: `auth.*`, `teams.*`, `players.*`, `matches.*`, `attendance.*`, `validation.*`, `error.*`, `general.*`
- The `__()` helper never throws — missing keys return the key itself as fallback
```

---

### 6. Update existing views

Update the already-created views to use `__()` for all user-facing strings:

- `app/views/auth/login.php`
- `app/views/layouts/header.php`
- `app/views/layouts/footer.php`
- `app/views/dashboard/index.php`

Replace any hardcoded English strings with the appropriate `__()` calls.

---

### 7. Document the decision

Create or update `docs/decisions.md` and add:

```markdown
## Decision: Lightweight array-based i18n

**Context:**
BarePitch is developed in English but used in Dutch by default. Other users may download and use it in their own language.

**Decision:**
Use a simple PHP array-based translation system with a `__()` helper. No external library. Language files live in `lang/`. Active language is set via `APP_LANG` in `.env`.

**Reason:**
Keeps the codebase accessible to contributors while allowing any language to be added by creating a single file. Fits the minimal philosophy of the project.

**Excluded:**
- Gettext / .po files — unnecessary complexity for this scale
- Framework i18n packages — no Composer in this project
- Pluralization rules — not needed for Version 1
```

---

### 8. After completing all changes

Commit with:

```bash
git add .
git commit -m "feat: add lightweight i18n infrastructure with NL and EN support"
```

Then confirm:
- `__()` helper is loaded via bootstrap
- `lang/en.php` and `lang/nl.php` exist and return arrays
- Existing views use `__()` for all interface text
- `APP_LANG=nl` in `.env` returns Dutch strings
- Missing keys return the key itself without errors
- `docs/decisions.md` is updated
- `CLAUDE.md` includes the i18n section
