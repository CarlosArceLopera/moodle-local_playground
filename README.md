# Moodle Playground - Local Plugin

## Overview

**Playground** is a Moodle local plugin that allows instructors to quickly create temporary "playground" courses without requiring administrator intervention. These courses are designed as safe spaces for instructors to prepare content, test new features, and experiment with course design before deploying to their official course offerings.

### Key Features

- **Quick Course Creation**: Users can create their own playground course on-demand
- **One-Click Access**: Simple interface for creating and accessing playground courses
- **Course Organization**: Courses are organized in a designated course category (configurable by administrators)
- **Auto-Enrollment**: Requesting users are automatically enrolled as instructors in their new playground course
- **Email Notifications**: Automated confirmation emails sent when a playground course is created
- **Privacy Focused**: No user data is stored; the plugin simply creates courses and enrollments

## Requirements

- **Moodle Version**: 5.0 or later

## Installation

1. Download or clone the plugin into your Moodle installation:
   ```bash
   git@github.com:YorkUITInnovation/moodle-local_playground.git /path/to/moodle/local/playground
   ```

2. From your Moodle administration dashboard, navigate to:
   - **Site Administration > Notifications**
   - The plugin will be detected and you'll be prompted to install it

3. Follow the installation wizard and complete the setup

## Configuration

### Administrator Settings

After installation, navigate to **Site Administration > Local plugins > Playground creation** to configure:

#### Course Category
- **Setting Name**: Playground Category
- **Description**: Select the category in which new playground courses will be created
- **Default**: Site root category
- **Note**: It's recommended to create a dedicated "Playground" category to keep these courses organized

### Email Configuration

The plugin uses Moodle's standard email settings. Ensure your Moodle instance is configured to send emails:
- **Site Administration > Server > Email**
- Configure your SMTP settings or sendmail appropriately

## Usage

1. Navigate to My courses
2. Look for the "Create playground course"
3. Enter a name for your playground course (e.g., "Math 2340")
4. Click "Create my playground course"
5. You'll be redirected to your new course as an instructor

**Note**: The final course name will be: "My Playground Course [Your Name] [Your Title]"


## Technical Details

### Plugin Type
- **Type**: Local Plugin (Moodle)
- **Component**: `local_playground`

### Database
- No database tables are created or modified by this plugin
- Uses standard Moodle course and user enrollment structures

### Hooks & Callbacks
The plugin uses Moodle's hook system to integrate with the navigation

### File Structure

```
playground/
├── amd/                          # AMD (Asynchronous Module Definition) modules
│   ├── build/                   # Minified JS files
│   │   ├── step1.min.js
│   │   └── step1.min.js.map
│   └── src/                     # Source JS files
│       └── step1.js
├── classes/                     # PHP classes
│   ├── hook_callbacks.php       # Navigation hook implementation
│   ├── request.php              # Core course creation logic
│   └── privacy/
│       └── provider.php         # Privacy provider implementation
├── css/                         # CSS stylesheets
│   └── styles.css
├── db/                          # Database-related files
│   ├── access.php              # Capabilities definition (unused)
│   ├── hooks.php               # Hook declarations
│   └── upgrade.php             # Database upgrade scripts
├── lang/                       # Language strings
│   ├── en/
│   │   └── local_playground.php
│   └── fr/
│       └── local_playground.php
├── templates/                  # Mustache templates
│   ├── completed.mustache      # Completion confirmation page
│   └── step1.mustache          # Course creation form
├── ajax.php                    # AJAX endpoint for course creation
├── completed.php               # Course creation completion page
├── index.php                   # Main plugin entry point
├── locallib.php                # Local library functions
├── settings.php                # Admin settings definition
├── version.php                 # Plugin version information
├── LICENSE                     # GNU GPL v3 License
└── README.md                   # This file
```

## Language Support

The plugin currently supports:
- **English** (en)
- **French** (fr)

Additional languages can be added by creating language files in the `lang/` directory.

## License

This plugin is licensed under the [GNU General Public License v3 (GPLv3)](LICENSE).

## Authors

- **Original Developer**: Patrick Thibaudeau
- **Copyright**: York University - UIT It Innovation & Academic Technologies

## Privacy

This plugin complies with Moodle's privacy standards:
- **Data Storage**: No personal user data is stored by this plugin
- **Data Usage**: The plugin only creates courses and enrolls users
- **GDPR Compliance**: User information is handled by Moodle's standard course and enrollment processes

For more information, see the privacy policy in the plugin settings.

