# local_playground — Architecture & Access Control

## Overview

The Playground plugin allows eligible York University staff and employees to
create a personal sandbox course in a designated Moodle category.  
The "Create Playground Course" button is surfaced in **two places** and is
controlled by a multi-layered eligibility system that ensures students **never**
see the button.

---

## Where the Button Appears

| Location | Hook | File |
|----------|------|------|
| **My Courses dashboard** (`/my/courses.php`) | `before_footer_html_generation` | `classes/hook_callbacks.php` → `inject_mycourses_button()` |
| **Course / Activity / Category — secondary nav (More menu)** | `secondary_extend` | `classes/hook_callbacks.php` → `extend_secondary_navigation()` |

### My Courses Dashboard (primary location)

- Only fires when `$PAGE->pagetype === 'my-index'` **AND** the URL contains `/my/courses.php`.
- The button is injected as footer HTML above the page footer.
- Only shown if the user has at least one enrolled course.

### Course More Menu (secondary location)

- Fires on `CONTEXT_COURSE`, `CONTEXT_MODULE`, and `CONTEXT_COURSECAT` pages.
- Explicitly **excluded** page types: `my-index`, `site-index`, `dashboard`.
- Any URL under `/my/` is also excluded as a belt-and-suspenders guard.
- The button appears as a node in the secondary navigation (More menu).

---

## Eligibility Gate — `local_playground_is_user_eligible()`

**File:** `locallib.php`

Both hooks call this function before rendering anything. It is the single
authoritative source of truth for whether a user can see the button.

### Execution order

```
1. isloggedin() && !isguestuser()          — must be a real logged-in user
2. Profile field check configured?         — read plugin settings
3. ID number prefix check configured?      — read plugin settings
4. Neither configured?                     — return false (fail-safe)
5. Run profile field check (if enabled)    — exact whole-value usertype match
6. Run ID number prefix check (if enabled) — exact prefix-from-start match
7. Apply require_both logic                — AND or OR, with profile as primary gate
```

---

## Admin Settings

Navigate to: **Admin › Plugins › Local plugins › Playground**

| Setting | Config key | Default | Purpose |
|---------|-----------|---------|---------|
| Playground category | `playground_category` | _(select)_ | Moodle category where sandbox courses are created |
| Profile field shortname | `profile_field_shortname` | `ldapusertypes` | Shortname of the custom profile field that holds the LDAP user type value (e.g. `ldapusertypes`) |
| Allowed user types | `allowed_user_types` | `staff,employee` | Comma-separated list of LDAP usertype values that are permitted. **Must be exact whole values** — partial matches are not accepted. |
| Allowed ID number prefixes | `allowed_idnumber_prefixes` | `1,5` | Comma-separated list of `idnumber` prefixes. A staff employee number starting with `1` or `5` matches. |
| Require both conditions | `require_both_conditions` | `1` (checked) | When checked: user must pass **both** the usertype AND the idnumber check. When unchecked: profile field is still the primary gate (see OR Logic section below). |

---

## Eligibility Logic in Detail

### Check 1 — Profile Field (LDAP usertype)

- Reads `mdl_user_info_data` for the field identified by `profile_field_shortname`.
- The stored value may be a single type (`staff`) or comma-separated (`staff,employee`).
- Each value is compared against `allowed_user_types` using **exact whole-value matching**
  via `in_array()`.
- `strpos()` is intentionally NOT used — it would allow `student` to match `staffstudent`
  or `staff` to match a substring of a longer value.

**York example:**

| User LDAP usertype | Allowed types config | Result |
|--------------------|---------------------|--------|
| `staff` | `staff,employee` | ✅ Pass |
| `employee` | `staff,employee` | ✅ Pass |
| `student` | `staff,employee` | ❌ Fail |
| `formerstudent` | `staff,employee` | ❌ Fail |
| `staffstudent` | `staff` | ❌ Fail — exact match only |

### Check 2 — ID Number Prefix

- Reads `$user->idnumber` and trims whitespace.
- **Empty `idnumber` always fails** — a user with no ID number cannot satisfy
  this check under any configuration.
- Prefix matching uses `substr($idnumber, 0, strlen($prefix)) === $prefix`
  (exact prefix from position 0, not `strpos` which could match mid-string).

**York example** (default prefixes `1,5`):

| idnumber | Result |
|----------|--------|
| `100012345` | ✅ Pass (starts with `1`) |
| `500067890` | ✅ Pass (starts with `5`) |
| `200345678` | ❌ Fail |
| _(empty)_ | ❌ Fail — empty guard |

### AND Logic (`require_both = true`, recommended)

Both checks must pass:

```
profile_field_check  AND  idnumber_prefix_check  →  eligible
```

| Usertype | idnumber | Result |
|----------|----------|--------|
| `staff` | `100012345` | ✅ Eligible |
| `staff` | `200345678` | ❌ Not eligible — idnumber fails |
| `student` | `100012345` | ❌ Not eligible — usertype fails |
| `student` | _(empty)_ | ❌ Not eligible — both fail |

### OR Logic (`require_both = false`)

When the profile field is configured, it is **always the primary gate** regardless
of the OR setting. The idnumber check alone **cannot** grant access when a profile
field is configured. This prevents students whose idnumber happens to start with a
staff prefix from seeing the button.

```
profile_field_configured + idnumber_configured + require_both=false:
    → return has_allowed_type   (profile field wins, idnumber ignored as standalone)

profile_field_configured only + require_both=false:
    → return has_allowed_type

idnumber_configured only (no profile field) + require_both=false:
    → return has_valid_idnumber  (idnumber is the only gate)
```

**Why this matters — the student with a staff-like idnumber:**

A student whose `idnumber` starts with `1` (e.g. a test account or dual-role
account) would pass the idnumber check. Without the profile-field-as-primary-gate
rule, OR logic would grant them access. With the rule:

- `has_allowed_type = false` (usertype is `student`)
- OR logic returns `has_allowed_type` → `false`
- Button is hidden ✅

---

## "Log in as" (Admin Impersonation)

When an admin uses Moodle's **"Log in as"** feature:

- Moodle sets `$SESSION->realuser` to the real admin's user ID.
- `$USER` is **completely replaced** with the impersonated user's object.
- The eligibility check runs against `$USER` — i.e. the **impersonated user**.

This is intentional. The button should show exactly what the impersonated user
would see in their own session:

| Admin impersonates | `$USER` usertype | Button shows? | Reason |
|-------------------|-----------------|---------------|--------|
| Student (`usertype=student`) | `student` | ❌ No | Student fails eligibility — correct, tester sees student view |
| Instructor (`usertype=staff`) | `staff` | ✅ Yes | Instructor passes eligibility — correct, tester sees instructor view |
| Another admin | _(staff/employee)_ | ✅ Yes | Passes eligibility |

> **Testing note:** If a test student account has an `idnumber` that starts with
> a staff prefix (e.g. `1xxxxxxx`), make sure `require_both = true` is set so
> the usertype check is also required. With `require_both = true`, a student
> usertype always fails regardless of idnumber.

---

## Why a Real Student Never Sees the Button

A genuine York student account has:
- `usertype` = `student` or `formerstudent` — neither is in `allowed_user_types`
- `idnumber` = student number (e.g. `2xxxxxxxx`) — does not start with `1` or `5`
- May have an **empty** `idnumber` — explicitly blocked

All three conditions independently return `false`. No configuration combination
allows a student through as long as `allowed_user_types` does not include
`student` or `formerstudent`.

```
Student request → isloggedin() ✅
               → profile field check: usertype=student ∉ {staff,employee} → false
               → idnumber check: 2xxxxxxxx ∉ prefixes {1,5}  → false
               → require_both=true: false AND false           → false
               → Button hidden ✅
```

---

## File Reference

| File | Purpose |
|------|---------|
| `locallib.php` | `local_playground_is_user_eligible()` — all eligibility logic |
| `classes/hook_callbacks.php` | Moodle hook handlers — where and when the button is injected |
| `db/hooks.php` | Hook registrations (`secondary_extend`, `before_footer_html_generation`) |
| `settings.php` | Admin settings UI — profile field, allowed types, prefixes, require_both |
| `index.php` | Playground course creation page (shown after button click) |
