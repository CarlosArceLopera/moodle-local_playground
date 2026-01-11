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
   git clone https://github.com/glendon-its/moodle-local_playground.git /path/to/moodle/local/playground
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

### For Instructors

1. Navigate to any course, module, or activity page
2. Look for the "Playground creation" link in the secondary navigation menu
3. Enter a name for your playground course (e.g., "Math 2340")
4. Click "Create my playground course"
5. You'll be redirected to your new course as an instructor

**Note**: The final course name will be: "My Playground Course [Your Name] [Your Title]"

### For Administrators

- **Monitor Playground Courses**: View all playground courses in the designated category
- **Configure Default Category**: Set where new playground courses are created
- **Email Templates**: Customize email notifications via language packs

## Technical Details

### Plugin Type
- **Type**: Local Plugin (Moodle)
- **Component**: `local_playground`

### Database
- No database tables are created or modified by this plugin
- Uses standard Moodle course and user enrollment structures

### Hooks & Callbacks
The plugin uses Moodle's hook system to integrate with the navigation:
- **Secondary Navigation Hook**: Adds "Playground creation" link to course/activity pages

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
├── config.php                  # Plugin configuration (Moodle 5.1 compatibility)
├── index.php                   # Main plugin entry point
├── lib.php                     # Plugin library functions
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
- **Copyright**: Glendon ITS, York University

## Support & Contributing

For bug reports, feature requests, or contributions, please visit:
- Website: [Glendon ITS](http://www.glendon.yorku.ca)

## Changelog

### Version 2.1.0 (2025-01-09)
- Updated to Moodle 5.1 standards
- Public folder structure support
- Rebranded from "Sandbox" to "Playground"
- Settings naming convention updated (sandbox_* → playground_*)
- Removed redundant plugin display in settings list
- Fixed language file syntax errors
- Improved code standards compliance

### Version 2.0.0
- Initial stable release
- Course creation functionality
- Email notifications
- Secondary navigation integration

## Privacy

This plugin complies with Moodle's privacy standards:
- **Data Storage**: No personal user data is stored by this plugin
- **Data Usage**: The plugin only creates courses and enrolls users
- **GDPR Compliance**: User information is handled by Moodle's standard course and enrollment processes

For more information, see the privacy policy in the plugin settings.

## Troubleshooting

### Plugin Not Appearing in Navigation
- Ensure you're logged in as an instructor or admin
- Check that you're viewing a course, activity, or category page
- Clear your browser cache and Moodle cache

### Course Creation Failing
- Verify the playground course category exists and is valid
- Check that you have proper course creation capabilities
- Review Moodle logs for detailed error messages

### Emails Not Sending
- Verify SMTP configuration in Moodle admin settings
- Check that sender email is configured
- Review mail logs for delivery errors

## FAQ

**Q: Can students create playground courses?**
A: Only instructors and administrators can create playground courses. Standard students do not see the creation link.

**Q: Can I delete a playground course?**
A: Yes, playground courses are standard Moodle courses and can be managed like any other course.

**Q: How many playground courses can I create?**
A: There's no limit to the number of playground courses you can create.

**Q: Are playground courses visible to students?**
A: No, playground courses are not automatically enrolled to students and are intended for instructor use only.

**Q: Can I move content from a playground course to another course?**
A: Yes, playground courses are full-featured Moodle courses. You can use Moodle's standard course backup/restore functionality or content copy features.

---

*For questions or support, contact Glendon ITS at York University*

