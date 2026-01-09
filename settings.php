<?php
defined('MOODLE_INTERNAL') || die;
global $CFG, $DB;

$categories = $DB->get_records( 'course_categories' );

$category = array();

foreach ( $categories as $c ) {
    $category[$c->id] =  $c->name;
}
if ($hassiteconfig) {
    $settings = new admin_settingpage('local_playground', get_string('pluginname', 'local_playground'));
    $ADMIN->add('localplugins', $settings);
    $settings->add( new admin_setting_configselect( 'playground_category', get_string( 'category', 'local_playground' ), get_string( 'category_help', 'local_playground' ), '', $category ) );
}