# BarePitch — PHP Project Structure and Architecture Guide

**Version 1.0 — April 2026**

---

## 1. Purpose

This document defines the recommended PHP project structure and application architecture for BarePitch.

The architecture is designed for:

- shared hosting
- PHP + MySQL
- low memory usage
- predictable maintenance
- no heavy framework dependency
- server-side rendering with targeted JavaScript

---

## 2. Architectural Principles

The application must follow these principles:

- simple request lifecycle
- low bootstrap overhead
- explicit file structure
- separation of concerns
- no hidden framework behavior
- no runtime dependency on Node.js
- no build process required on the server

---

## 3. Recommended Stack

- PHP 8.x
- MySQL 8.x
- PDO
- plain CSS
- server-rendered HTML
- lightweight JavaScript for interaction only

No full framework is required.

Optional lightweight helper packages are allowed if they:

- do not introduce major runtime overhead
- can be deployed without background workers
- do not require Node.js on the server

---

## 4. Recommended Folder Structure

```text
/public
    index.php
    /css
        base.css
        layout.css
        components.css
    /js
        app.js
        match-editor.js
        livestream.js
    /uploads
        .htaccess

/app
    /Config
        app.php
        database.php
        routes.php
        permissions.php

    /Core
        Router.php
        Request.php
        Response.php
        Session.php
        Csrf.php
        Auth.php
        View.php
        Database.php
        Validator.php
        Flash.php

    /Controller
        AuthController.php
        DashboardController.php
        TeamController.php
        PlayerController.php
        TrainingController.php
        MatchController.php
        LivestreamController.php
        SettingsController.php

    /Service
        AuthService.php
        TeamService.php
        PlayerService.php
        TrainingService.php
        MatchService.php
        LivestreamService.php
        RatingService.php
        StatisticsService.php
        AuditService.php
        LockService.php

    /Repository
        UserRepository.php
        TeamRepository.php
        PlayerRepository.php
        TrainingRepository.php
        MatchRepository.php
        EventRepository.php
        RatingRepository.php
        AuditRepository.php

    /Policy
        TeamPolicy.php
        MatchPolicy.php
        PlayerPolicy.php
        TrainingPolicy.php

    /Validation
        AuthValidator.php
        PlayerValidator.php
        TrainingValidator.php
        MatchValidator.php
        RatingValidator.php

    /View
        /layout
            header.php
            footer.php
            nav.php
        /auth
        /dashboard
        /team
        /player
        /training
        /match
        /livestream
        /settings

/storage
    /logs
    /cache
    /sessions

/vendor

.env
composer.json
```

---

## 5. Directory Responsibilities

### /public

Public web root.

Contains:

- entry point
- static assets
- public JavaScript
- public CSS

Rules:

- only `/public` is web-accessible
- application code must not be directly accessible
- uploads must be heavily restricted if used at all

---

### /app/Config

Contains static configuration.

Examples:

- app name
- environment mode
- database credentials loader
- route definitions
- permission maps

Rules:

- no business logic here
- no direct HTML output

---

### /app/Core

Contains reusable infrastructure classes.

Purpose:

- request lifecycle
- routing
- rendering
- session handling
- CSRF handling
- authentication support
- database access bootstrap

Rules:

- no feature-specific domain logic
- no direct SQL queries for domain behavior except base connection handling

---

### /app/Controller

Controllers handle HTTP requests.

Responsibilities:

- receive request
- call service layer
- return view or redirect
- return JSON where needed

Controllers must not contain:

- database queries
- transaction logic
- authorization logic beyond access gate calls
- business rules

Controllers must stay thin.

---

### /app/Service

Services contain business workflows.

Responsibilities:

- execute use cases
- coordinate repositories
- manage transactions
- call validators and policies
- trigger audit logging
- update aggregates

Examples:

- prepare match
- start match
- register goal
- execute substitution
- finish match
- correct finished match

This is the main application logic layer.

---

### /app/Repository

Repositories contain database read/write operations.

Responsibilities:

- execute SQL
- map query results
- return domain-shaped arrays or simple objects

Rules:

- no workflow logic
- no view formatting
- no redirects
- no HTML

Repositories should stay close to the schema.

---

### /app/Policy

Policies contain authorization rules.

Responsibilities:

- check whether user may perform action on resource

Examples:

- may edit match
- may correct finished match
- may manage player
- may manage training

Rules:

- all write routes must pass policy checks
- UI hiding is never sufficient without policy enforcement

---

### /app/Validation

Contains validation logic per feature.

Responsibilities:

- validate input structure
- validate field presence
- validate business preconditions where appropriate

Examples:

- complete lineup validation
- player count validation
- valid event payload validation

Rules:

- validation must not be spread ad hoc across controllers
- reusable validation belongs here

---

### /app/View

Contains server-rendered templates.

Structure:

- grouped by feature
- uses shared layout templates

Rules:

- no SQL
- no business logic
- minimal conditional display logic only

---

### /storage

Contains writable runtime data.

Includes:

- logs
- cache
- sessions if file-based session handling is used

Rules:

- must not be directly web-accessible
- clear retention policy recommended

---

## 6. Request Lifecycle

Recommended request lifecycle:

1. Request enters through `/public/index.php`
2. Bootstrap app configuration
3. Build request object
4. Resolve route
5. Authenticate session where required
6. Apply CSRF check for write requests
7. Call controller
8. Controller calls service
9. Service validates, authorizes, performs business action
10. Service calls repositories
11. Service commits transaction if needed
12. Response rendered or redirect returned

---

## 7. Routing

Use a simple route table.

Recommended style:

```php
return [
    ['GET', '/', [DashboardController::class, 'index']],
    ['GET', '/matches/{id}', [MatchController::class, 'show']],
    ['POST', '/matches/{id}/start', [MatchController::class, 'start']],
];
```

Rules:

- route definitions must stay centralized
- avoid route logic in multiple files unless size requires grouping later
- prefer clear, resource-oriented paths

---

## 8. Controllers and Services

### 8.1 Controller Pattern

Example pattern:

```php
public function start(int $matchId): void
{
    $this->matchService->startMatch($this->auth->user(), $matchId);
    $this->response->redirect("/matches/{$matchId}");
}
```

This is the desired level of controller complexity.

---

### 8.2 Service Pattern

Example service flow for starting a match:

1. load match
2. check permission
3. validate status transition
4. start database transaction
5. update match status
6. create first period
7. set livestream start
8. commit
9. return result

Services must own the workflow.

---

## 9. Database Access

### 9.1 PDO Only

Use PDO with prepared statements for all queries.

Requirements:

- exceptions enabled
- emulated prepares disabled where possible
- utf8mb4 enforced

---

### 9.2 No Heavy ORM

BarePitch should not depend on a heavy ORM.

Reason:

- less overhead
- more transparent performance
- easier hosting compatibility
- closer alignment with the explicit schema

A very light query helper is acceptable.
A full ORM-first architecture is not recommended.

---

### 9.3 Transaction Ownership

Transactions belong in services, not repositories.

Reason:

- business operations often span multiple repositories
- repositories should remain atomic data access units

---

## 10. Session and Authentication

### 10.1 Session Handling

Use standard PHP sessions.

Recommended settings:

- secure cookies
- HttpOnly
- SameSite=Lax or stricter where possible
- session regeneration after login

---

### 10.2 Magic Link Flow

Flow:

1. user submits email
2. generate hashed one-time token
3. send email link
4. validate token
5. log user in
6. invalidate token
7. redirect to dashboard

Rules:

- neutral response for unknown email
- short expiration window
- one-time use only

---

## 11. Views and Rendering

### 11.1 Rendering Strategy

Use server-rendered HTML as default.

Benefits:

- low server complexity
- no JavaScript dependency for core flow
- easier shared hosting deployment
- predictable performance

---

### 11.2 Use JavaScript Only Where Needed

Recommended JavaScript usage:

- match lock refresh
- swipe controls
- polling for livestream
- dynamic lineup interaction
- lightweight form interactivity

Do not move core application logic into JavaScript.

---

## 12. JavaScript Structure

Recommended files:

- `app.js` → shared global behaviors
- `match-editor.js` → match editing behaviors
- `livestream.js` → polling and live refresh
- `lineup-grid.js` → grid interaction if separated later

Rules:

- no frontend framework required
- no build pipeline required
- keep modules feature-focused

---

## 13. CSS Structure

Recommended CSS files:

- `base.css`
- `layout.css`
- `components.css`

Optional:

- `utilities.css`

Use:

- CSS variables
- mobile-first rules
- grid and flexbox
- no framework dependency

---

## 14. Error Handling

### 14.1 User Errors

Show:

- safe
- clear
- non-technical messages

Examples:

- "You do not have permission to perform this action."
- "This match is currently being edited by another user."
- "The lineup is incomplete."

---

### 14.2 System Errors

Log:

- route
- user id where available
- exception type
- internal message
- stack trace where available

Do not expose:

- SQL
- stack traces
- token values
- sensitive internal paths

---

## 15. Logging

Recommended logs:

- application error log
- audit log for finished match corrections
- optional security log for repeated invalid login attempts

Prefer file logging in `/storage/logs`.

Rotate logs regularly if hosting environment does not do this automatically.

---

## 16. Caching

Caching should stay simple.

Recommended:

- small file cache only for low-risk, low-volatility data
- no dependency on Redis or background infrastructure

Possible cache targets:

- formation definitions
- static settings
- language file loading

Do not cache:

- live match state in a way that bypasses database truth
- permission decisions beyond request scope

---

## 17. Recommended First Build Order

1. bootstrap core
2. authentication and sessions
3. database connection and repositories
4. team and player management
5. training management
6. match preparation
7. match live flow
8. livestream
9. ratings and statistics
10. audit and correction tooling

This order reduces rework.

---

## 18. Recommended Development Rules

- thin controllers
- service-owned workflows
- repository-owned SQL
- explicit validation
- explicit authorization
- no hidden magic
- no framework-style convenience abstractions unless clearly useful

---

## 19. What to Avoid

Avoid:

- long controller methods
- business logic in templates
- raw SQL in controllers
- multiple sources of truth for scores
- JavaScript-only critical flows
- framework dependencies that exceed hosting capacity
- unnecessary abstractions before real need exists

---

## 20. Summary

Recommended architecture for BarePitch:

- plain PHP application
- modular but lightweight structure
- server-rendered views
- PDO-based repositories
- service layer for workflows
- policy layer for authorization
- validation layer for request and business checks
- simple locking and polling
- no Node.js
- no heavy PHP framework required

This architecture is robust, maintainable, and appropriate for shared hosting.

---

## End
