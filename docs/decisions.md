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
