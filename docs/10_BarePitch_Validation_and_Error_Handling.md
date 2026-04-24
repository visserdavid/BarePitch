# BarePitch – Validation and Error Handling

## 1. Purpose

This document defines how BarePitch handles validation and errors.

The goal is simple:

BarePitch should prevent bad data from entering the system, guide the user clearly when something goes wrong, and never expose technical details that could create security or privacy risks.

Validation protects data quality.
Error handling protects the user, the system and the developer’s ability to diagnose problems.

## 2. Core Principles

Validation and error handling follow these principles:

Validate on the server.
Client-side validation is useful, but never trusted.

Fail safely.
If something is unclear or invalid, the system should reject the request.

Be clear to the user.
Messages should explain what the user can do next.

Do not expose internals.
Never show SQL errors, file paths, stack traces or raw exceptions.

Log what matters.
Technical details belong in logs, not in the interface.

Do not store unnecessary input.
Invalid or sensitive input should not be preserved longer than needed.

## 3. Validation Layers

BarePitch uses three validation layers.

### 3.1 Browser-level validation

HTML can help with simple checks.

Examples:

```html
<input type="email" name="email" required>
<input type="text" name="team_name" maxlength="100" required>
<input type="date" name="match_date" required>
```

This improves usability, but it is not security.

### 3.2 JavaScript validation

JavaScript may improve immediate feedback.

Examples:

show missing fields before submit;
warn before leaving unsaved changes;
highlight invalid attendance choices.

JavaScript validation is optional and must always be repeated in PHP.

### 3.3 Server-side validation

PHP validation is mandatory.

Every submitted value must be checked before database use.

Server-side validation is the final authority.

## 4. General Input Rules

All submitted input should be:

trimmed;
checked for required presence;
checked for correct type;
checked for maximum length;
checked against allowed values where relevant;
checked against ownership where IDs are involved.

Example:

```php
$name = trim($_POST['team_name'] ?? '');

if ($name === '') {
    $errors['team_name'] = 'Please enter a team name.';
}

if (mb_strlen($name) > 100) {
    $errors['team_name'] = 'Team name may not exceed 100 characters.';
}
```

## 5. Field Validation Rules

## 5.1 Email

Used for login.

Rules:

required;
valid email format;
maximum 255 characters;
normalized by trimming and lowercasing.

Example:

```php
$email = strtolower(trim($_POST['email'] ?? ''));

if ($email === '') {
    $errors['email'] = 'Please enter your email address.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
} elseif (mb_strlen($email) > 255) {
    $errors['email'] = 'Email address is too long.';
}
```

## 5.2 Password

Used for login and account creation.

Rules:

required;
minimum length for account creation;
never logged;
never returned in error output;
never stored except as a hash.

Login error should remain neutral:

```text
Invalid email or password.
```

## 5.3 Team Name

Rules:

required;
maximum 100 characters;
belongs to the logged-in user;
escaped when displayed.

Message:

```text
Please enter a team name.
```

## 5.4 Season

Optional in Version 1.

Rules:

maximum 20 characters;
simple format recommended, for example `2026/2027`;
escaped when displayed.

Do not over-validate too early. A season label is supporting information, not a legal field.

## 5.5 Player Display Name

Rules:

required;
maximum 100 characters;
no need for separate first name and last name in Version 1;
escaped when displayed.

Message:

```text
Please enter a player name.
```

BarePitch should allow practical names coaches actually use, including initials or short display names.

## 5.6 Shirt Number

Optional.

Rules:

integer;
between 1 and 99;
empty is allowed.

Example:

```php
$shirtNumber = trim($_POST['shirt_number'] ?? '');

if ($shirtNumber !== '') {
    if (!ctype_digit($shirtNumber)) {
        $errors['shirt_number'] = 'Shirt number must be a number.';
    } elseif ((int)$shirtNumber < 1 || (int)$shirtNumber > 99) {
        $errors['shirt_number'] = 'Shirt number must be between 1 and 99.';
    }
}
```

## 5.7 Opponent Name

Rules:

required;
maximum 100 characters;
escaped when displayed.

Message:

```text
Please enter an opponent name.
```

## 5.8 Match Date

Rules:

required;
must be a valid date;
stored as `DATE`;
not necessarily restricted to future dates, because past matches may be entered later.

Example:

```php
$date = $_POST['match_date'] ?? '';
$parsed = DateTime::createFromFormat('Y-m-d', $date);

if (!$parsed || $parsed->format('Y-m-d') !== $date) {
    $errors['match_date'] = 'Please enter a valid match date.';
}
```

## 5.9 Kickoff Time

Optional.

Rules:

valid `HH:MM` format;
stored as `TIME`;
empty is allowed.

## 5.10 Home, Away or Neutral

Optional.

Allowed values:

```text
home
away
neutral
```

Reject everything else.

## 5.11 Attendance Status

Allowed values for Version 1:

```text
unknown
available
unavailable
selected
```

Validation:

```php
$allowedStatuses = ['unknown', 'available', 'unavailable', 'selected'];

if (!in_array($status, $allowedStatuses, true)) {
    $errors['status'] = 'Invalid attendance status.';
}
```

Never trust submitted status values.

## 6. ID Validation

IDs from URLs or forms must be treated as untrusted.

Rules:

ID must be present;
ID must be numeric;
ID must exist;
ID must belong to the logged-in user through ownership chain.

Example:

```php
$teamId = $_GET['team_id'] ?? null;

if (!$teamId || !ctype_digit($teamId)) {
    http_response_code(404);
    require view('errors/404.php');
    exit;
}
```

After numeric validation, still check ownership in the database.

Numeric is not enough.

## 7. Ownership Validation

Ownership validation is mandatory for every protected resource.

Examples:

A team must belong to the current user.
A player must belong to a team owned by the current user.
A match must belong to a team owned by the current user.
A submitted player ID for attendance must belong to the same team as the match.

This prevents URL tampering and hidden-field manipulation.

Unsafe assumption:

```text
The form only showed valid players, so submitted IDs are valid.
```

Correct assumption:

```text
The submitted IDs may have been changed and must be checked again.
```

## 8. CSRF Validation

Every state-changing request must validate a CSRF token.

If CSRF validation fails:

return HTTP 403;
show a generic invalid request message;
log the event in limited form;
do not process the request.

Message:

```text
Invalid request. Please try again.
```

CSRF failure should not produce detailed explanations to the user.

## 9. Validation Error Display

When validation fails:

show the form again;
preserve safe submitted values;
show clear field-specific messages;
do not preserve passwords;
do not save anything to the database.

Example structure:

```php
$errors = [
    'team_name' => 'Please enter a team name.',
];
```

In the view:

```php
<?php if (!empty($errors['team_name'])): ?>
    <p class="form-error"><?= e($errors['team_name']) ?></p>
<?php endif; ?>
```

## 10. Safe Value Refill

When showing a form again after validation errors, refill safe values.

Example:

```php
<input
    type="text"
    name="team_name"
    value="<?= e($old['team_name'] ?? '') ?>"
>
```

Never refill:

password fields;
CSRF tokens from request;
unsafe raw HTML;
large or sensitive free text.

## 11. Error Categories

BarePitch distinguishes between validation errors and system errors.

Validation errors:

caused by user input;
shown near the form;
recoverable by the user.

Authorization errors:

user tries to access something they should not;
shown as 403 or 404;
logged if suspicious.

Not found errors:

resource does not exist or is not accessible;
usually shown as 404.

System errors:

database failure;
unexpected exception;
server misconfiguration;
logged internally;
shown as generic message.

## 12. HTTP Status Codes

Use meaningful status codes.

```text
200 OK                Page loaded successfully
302 Found             Redirect after successful POST
400 Bad Request       Invalid request structure
403 Forbidden         Authenticated but not allowed, or CSRF failure
404 Not Found         Resource not found or hidden for security
422 Unprocessable     Validation failed, optional
500 Server Error      Unexpected system failure
```

For small PHP projects, using 200 with form errors is acceptable.
For access violations and missing resources, status codes should be correct.

## 13. Redirect After Successful POST

After a successful POST, redirect.

This prevents duplicate form submission when the user refreshes.

Pattern:

```text
POST /teams/create
save team
redirect to /teams
show success message
```

This is commonly known as Post, Redirect, Get.

It keeps the interface predictable.

## 14. Flash Messages

Use short session-based flash messages after redirects.

Examples:

```text
Team created.
Player updated.
Match saved.
Attendance saved.
```

Rules:

messages are stored once;
shown on next page load;
then removed from session.

Do not use flash messages for technical errors.

## 15. Exception Handling

Unexpected exceptions should be caught at a central level where possible.

In development:

show enough detail to debug.

In production:

log the exception;
show a generic error page;
do not expose technical details.

Generic message:

```text
Something went wrong. Please try again.
```

## 16. Logging Errors

Log:

unexpected exceptions;
database connection failures;
authorization failures;
CSRF failures;
repeated failed login attempts;
failed critical writes.

Do not log:

passwords;
password hashes;
session IDs;
full request payloads;
unnecessary player information.

Example log entry:

```text
[2026-04-24 15:12:03] ERROR: Failed to save attendance for match_id=42, user_id=3
```

This is useful, but still limited.

## 17. User-Facing Error Tone

BarePitch should remain calm and direct.

Good:

```text
Please enter a player name.
You do not have access to this page.
This match could not be found.
Something went wrong. Please try again.
```

Avoid:

```text
Fatal error.
SQLSTATE[23000].
You made an invalid request.
Access denied due to authorization failure.
```

The user should understand what to do next.

## 18. Security-Sensitive Errors

Some errors should be intentionally vague.

Login:

```text
Invalid email or password.
```

Not:

```text
No account exists for this email.
```

Access to another user’s data:

Prefer:

```text
This page could not be found.
```

over:

```text
You tried to access another user’s team.
```

This avoids confirming that the resource exists.

## 19. Database Error Handling

Database errors must never be displayed directly.

Common database issues:

duplicate email;
foreign key failure;
connection failure;
invalid enum value;
deadlock or timeout.

Application behavior:

catch the exception;
log technical detail;
show generic error;
preserve safe user input if appropriate.

For expected conflicts, create friendly messages.

Example:

```text
An account with this email address already exists.
```

Only use this during account creation, not during login.

## 20. Validation Helpers

Create reusable validation helpers.

Example functions:

```php
required($value): bool
maxLength($value, $length): bool
validEmail($value): bool
validDate($value): bool
integerInRange($value, $min, $max): bool
inAllowedValues($value, array $allowed): bool
```

Do not scatter slightly different validation logic across many files.

Consistency prevents hidden bugs.

## 21. Error Pages

Create simple error views:

```text
app/views/errors/403.php
app/views/errors/404.php
app/views/errors/500.php
```

Tone:

short;
clear;
non-technical;
with a way back to dashboard or login.

Example 404:

```text
This page could not be found.
Return to dashboard.
```

## 22. Development vs Production

Development:

show errors;
log errors;
use test data;
debug quickly.

Production:

hide errors;
log errors;
protect data;
show generic messages.

This distinction must be controlled by environment configuration, not by manually editing code.

## 23. Validation Testing Checklist

For each form, test:

empty required fields;
too long input;
invalid date;
invalid email;
invalid status value;
non-numeric ID;
ID from another user;
missing CSRF token;
invalid CSRF token;
XSS input;
refresh after successful submit.

Basic XSS test:

```html
<script>alert('xss')</script>
```

It should be displayed as text or rejected, never executed.

## 24. Error Handling Checklist

Before a feature is considered done:

validation errors show clearly;
safe input is preserved;
passwords are never refilled;
successful POST redirects;
technical errors are not shown;
unexpected errors are logged;
access errors are handled safely;
missing resources do not break the page;
messages are understandable on mobile.

## 25. Definition of Done

Validation and error handling are complete when:

all inputs are validated server-side;
all IDs are checked for ownership;
all state-changing requests validate CSRF;
form errors are field-specific where useful;
successful posts redirect;
technical errors are logged;
production users never see internals;
security-sensitive errors remain vague;
messages are calm and understandable;
no invalid data is saved.

## 26. Guiding Question

Every validation and error-handling decision should be tested against this question:

Does this protect the system while helping the user recover?

If it protects the system but confuses the user, improve the message.
If it helps the user but weakens security, tighten the design.
