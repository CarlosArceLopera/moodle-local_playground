<?php
/**
 * Plugin settings for the playground plugin.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
global $CFG, $DB;

$category = [];

if (!during_initial_install()) {
    $categories = $DB->get_records('course_categories');

    foreach ($categories as $c) {
        $category[$c->id] = $c->name;
    }
}

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_playground', get_string('pluginname', 'local_playground'));
    $ADMIN->add('localplugins', $settings);

    // Category selection
    if (!empty($category)) {
        $settings->add(new admin_setting_configselect('local_playground/playground_category',
            get_string('category', 'local_playground'),
            get_string('category_help', 'local_playground'),
            null,
            $category));
    }

    // Access control settings heading
    $settings->add(new admin_setting_heading('local_playground/access_control_heading',
        get_string('access_control_heading', 'local_playground'),
        get_string('access_control_heading_desc', 'local_playground')));

    // Profile field shortname
    $settings->add(new admin_setting_configtext('local_playground/profile_field_shortname',
        get_string('profile_field_shortname', 'local_playground'),
        get_string('profile_field_shortname_desc', 'local_playground'),
        'usertypes',
        PARAM_ALPHANUMEXT));

    // Allowed user types (comma-separated)
    $settings->add(new admin_setting_configtext('local_playground/allowed_user_types',
        get_string('allowed_user_types', 'local_playground'),
        get_string('allowed_user_types_desc', 'local_playground'),
        'staff,employee',
        PARAM_TEXT));

    // Allowed ID number prefixes (comma-separated)
    $settings->add(new admin_setting_configtext('local_playground/allowed_idnumber_prefixes',
        get_string('allowed_idnumber_prefixes', 'local_playground'),
        get_string('allowed_idnumber_prefixes_desc', 'local_playground'),
        '1,5',
        PARAM_TEXT));

    // Require both conditions
    $settings->add(new admin_setting_configcheckbox('local_playground/require_both_conditions',
        get_string('require_both_conditions', 'local_playground'),
        get_string('require_both_conditions_desc', 'local_playground'),
        1));
}