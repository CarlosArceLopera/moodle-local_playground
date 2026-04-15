# Moodle Playground - Local Plugin

## Overview

**Playground** is a Moodle local plugin that allows eligible York University staff and employees to quickly create a personal "playground" sandbox course without requiring administrator intervention. These courses are designed as safe spaces to prepare content, test new features, and experiment with course design.

### Key Features

- **Quick Course Creation**: Eligible users can create their own playground course on-demand
- **One-Click Access**: Simple interface for creating and accessing playground courses
- **Course Organization**: Courses are organized in a designated course category (configurable by administrators)
- **Auto-Enrollment**: Requesting users are automatically enrolled as instructors in their new playground course
- **Email Notifications**: Automated confirmation emails sent when a playground course is created
- **Access Control**: Eligibility is gated by LDAP user-type profile field and/or employee ID number prefix — students are never shown the button
- **Privacy Focused**: No user data is stored; the plugin simply creates courses and enrollments

## Requirements

- **Moodle Version**: 5.0 or later

## Installation

1. Download or clone the plugin into your Moodle installation:
   ```bash
   git clone git@github.com:YorkUITInnovation/moodle-local_playground.git /path/to/moodle/local/playground
   ```

2. From your Moodle administration dashboard, navigate to:
   - **Site Administration > Notifications**
   - The plugin will be detected and you'll be prompted to install it

3. Follow the installation wizard and complete the setup

## Configuration

### Administrator Settings

After installation, navigate to **Site Administration > Local plugins > Playground** to configure:

#### Course Category
- **Setting Name**: `playground_category`
- **Description**: Select the category in which new playground courses will be created
- **Default**: _(must be set before the button appears)_
- **Note**: It is recommended to create a dedicated "Playground" category to keep these courses organized

#### Access Control Settings

These settings determine which users are eligible to see and use the "Create Playground Course" button. See `docs/PLAYGROUND_ARCHITECTURE.md` for the full eligibility logic.

| Setting | Config key | Default | Purpose |
|---------|-----------|---------|---------|
| Profile field shortname | `profile_field_shortname` | `usertypes` | Shortname of the custom profile field that holds the LDAP user type (e.g. `usertypes`) |
| Allowed user types | `allowed_user_types` | `staff,employee` | Comma-separated list of LDAP usertype values that are permitted. Exact whole-value match — `student` or `formerstudent` will never match `staff`. |
| Allowed ID number prefixes | `allowed_idnumber_prefixes` | `1,5` | Comma-separated list of `idnumber` prefixes. York staff numbers start with `1` or `5`. Empty idnumbers always fail. |
| Require both conditions | `require_both_conditions` | `1` (checked) | When checked: user must pass **both** the usertype AND the idnumber check. Recommended to keep enabled. |

### Email Configuration

The plugin uses Moodle's standard email settings. Ensure your Moodle instance is configured to send emails:
- **Site Administration > Server > Email**
- Configure your SMTP settings or sendmail appropriately

## Usage

### Where the button appears

| Location | Who sees it |
|----------|-------------|
| **My Courses page** (`/my/courses.php`) | Eligible users who have at least one enrolled course |
| **Course / Activity / Category — More menu** (secondary nav) | Eligible users browsing any course, activity, or category page |

Students and former students are **never** shown the button regardless of which page they are on. See `docs/PLAYGROUND_ARCHITECTURE.md` for the full access-control explanation.

### Creating a playground course

1. Navigate to **My Courses** (or any course/activity page)
2. Look for the **"Create playground course"** button (My Courses page) or the **"Playground"** link in the course More menu
3. Enter a name for your playground course (e.g., "Math 2340")
4. Click **"Create my playground course"**
5. You will be redirected to your new course as an instructor

> **Note**: The final course name will be formatted as:  
> `My Playground Course [Your Name] [Your Title]`

## Technical Details

### Plugin Type
- **Type**: Local Plugin (Moodle)
- **Component**: `local_playground`

### Database
- No database tables are created or modified by this plugin
- Uses standard Moodle course and user enrollment structures

### Hooks & Callbacks
The plugin uses Moodle 5's hook system:

| Hook | Handler | Purpose |
|------|---------|---------|
| `core\hook\output\before_footer_html_generation` | `inject_mycourses_button()` | Injects button on My Courses page |
| `core\hook\navigation\secondary_extend` | `extend_secondary_navigation()` | Adds Playground link to course More menu |

### File Structure

```
playground/
├── amd/                          # AMD (Asynchronous Module Definition) modules
│   ├── build/                   # Minified JS files
│   │   ├── step1.min.js
│   │   └── step1.min.js.map
│   └── src/                     # Source JS files
│       ├── mycourses_button.js  # Repositions button into My Courses header
│       └── step1.js
├── classes/                     # PHP classes
│   ├── hook_callbacks.php       # Navigation hook implementation
│   ├── request.php              # Core course creation logic
│   └── privacy/
│       └── provider.php         # Privacy provider implementation
├── css/                         # CSS stylesheets
│   └── styles.css
├── db/                          # Database-related files
│   ├── access.php               # Capabilities definition
│   ├── hooks.php                # Hook registrations
│   └── upgrade.php              # Database upgrade scripts
├── docs/                        # Documentation
│   ├── README.md                # This file
│   └── PLAYGROUND_ARCHITECTURE.md  # Eligibility & access-control deep-dive
├── lang/                        # Language strings
│   ├── en/
│   │   └── local_playground.php
│   └── fr/
│       └── local_playground.php
├── templates/                   # Mustache templates
│   ├── completed.mustache       # Completion confirmation page
│   ├── mycourses_button.mustache # Button injected on My Courses page
│   └── step1.mustache           # Course creation form
├── ajax.php                     # AJAX endpoint for course creation
├── completed.php                # Course creation completion page
├── diagnostics.php              # Admin diagnostics / eligibility checker
├── index.php                    # Main plugin entry point
├── locallib.php                 # Local library functions (eligibility logic)
├── settings.php                 # Admin settings definition
├── version.php                  # Plugin version information
└── LICENSE                      # GNU GPL v3 License
```

## Language Support

The plugin currently supports:
- **English** (en)
- **French** (fr)

Additional languages can be added by creating language files in the `lang/` directory.

## License

This plugin is licensed under the [GNU General Public License v3 (GPLv3)](../LICENSE).

## Authors

- **Original Developer**: Patrick Thibaudeau
- **Copyright**: York University — UIT It Innovation & Academic Technologies

## Privacy

This plugin complies with Moodle's privacy standards:
- **Data Storage**: No personal user data is stored by this plugin
- **Data Usage**: The plugin only creates courses and enrolls users using Moodle's standard APIs
- **GDPR Compliance**: User information is handled by Moodle's standard course and enrollment processes

For more information, see `classes/privacy/provider.php`.
