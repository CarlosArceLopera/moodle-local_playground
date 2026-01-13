<?php
/**
 * Local library functions for the playground plugin.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Creates the Moodle page header
 * @global stdobject $CFG
 * @global moodle_database $DB
 * @param string $url Current page url
 * @param string $pagetitle  Page title
 * @param string $pageheading Page heading (Note hard coded to site fullname)
 * @param array $context The page context (SYSTEM, COURSE, MODULE etc)
 * @return HTML Contains page information and loads all Javascript and CSS
 */
function local_playground_page($url, $pagetitle, $pageheading, $context) {
    global $CFG, $PAGE, $SITE;

    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('local_playground',
            current_language());

    $PAGE->set_url($CFG->wwwroot . $url);
    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($pageheading);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_context($context);
    //Language strings for Javascript
    $PAGE->requires->strings_for_js(array_keys($strings), 'local_playground');
}

/**
 * Check if user is eligible to create playground courses
 * Based on LDAP USER TYPES profile field and ID number pattern
 *
 * @param object|null $user User object (optional, defaults to current user)
 * @return bool True if user is eligible
 */
function local_playground_is_user_eligible($user = null) {
    global $USER, $DB;

    if ($user === null) {
        $user = $USER;
    }

    // Check if user is logged in and not a guest
    if (!isloggedin() || isguestuser()) {
        return false;
    }


    // Get configuration settings
    $profile_field_shortname = get_config('local_playground', 'profile_field_shortname');
    $allowed_user_types_config = get_config('local_playground', 'allowed_user_types');
    $allowed_idnumber_prefixes_config = get_config('local_playground', 'allowed_idnumber_prefixes');
    $require_both = get_config('local_playground', 'require_both_conditions');

    // If require_both is not set, default to true
    if ($require_both === false) {
        $require_both = true;
    }

    // Determine which checks are enabled
    $profile_checking_enabled = !empty($profile_field_shortname) && !empty($allowed_user_types_config);
    $idnumber_checking_enabled = !empty($allowed_idnumber_prefixes_config);

    // If no checks are configured at all, deny access (fail-safe default)
    if (!$profile_checking_enabled && !$idnumber_checking_enabled) {
        return false;
    }

    $has_allowed_type = false;
    $has_valid_idnumber = false;

    // Check profile field if configured
    if ($profile_checking_enabled) {
        $profile_field = $DB->get_record('user_info_field', array('shortname' => $profile_field_shortname));

        if ($profile_field) {
            $profile_data = $DB->get_record('user_info_data', array(
                'userid' => $user->id,
                'fieldid' => $profile_field->id
            ));

            if ($profile_data) {
                $ldap_user_types = strtolower(trim($profile_data->data));

                // Parse allowed user types (comma-separated)
                $allowed_types = array_map('trim', explode(',', strtolower($allowed_user_types_config)));

                // Check if user has any of the allowed types
                foreach ($allowed_types as $type) {
                    if (!empty($type) && strpos($ldap_user_types, $type) !== false) {
                        $has_allowed_type = true;
                        break;
                    }
                }
            }
        }
    }

    // Check ID number if configured
    if ($idnumber_checking_enabled) {
        $idnumber = isset($user->idnumber) ? trim($user->idnumber) : '';

        if (!empty($idnumber)) {
            // Parse allowed prefixes (comma-separated)
            $allowed_prefixes = array_map('trim', explode(',', $allowed_idnumber_prefixes_config));

            // Check if ID number starts with any of the allowed prefixes
            foreach ($allowed_prefixes as $prefix) {
                if (!empty($prefix) && strpos($idnumber, $prefix) === 0) {
                    $has_valid_idnumber = true;
                    break;
                }
            }
        }
    }

    // Return based on require_both setting and which checks are enabled
    if ($require_both) {
        // User must meet both enabled conditions
        if ($profile_checking_enabled && $idnumber_checking_enabled) {
            return ($has_allowed_type && $has_valid_idnumber);
        } else if ($profile_checking_enabled) {
            // Only profile check is enabled, user must pass it
            return $has_allowed_type;
        } else {
            // Only ID number check is enabled, user must pass it
            return $has_valid_idnumber;
        }
    } else {
        // User can meet either enabled condition
        if ($profile_checking_enabled && $idnumber_checking_enabled) {
            // Both checks enabled: pass if user meets either
            return ($has_allowed_type || $has_valid_idnumber);
        } else if ($profile_checking_enabled) {
            // Only profile check is enabled, user must pass it
            return $has_allowed_type;
        } else {
            // Only ID number check is enabled, user must pass it
            return $has_valid_idnumber;
        }
    }
}
