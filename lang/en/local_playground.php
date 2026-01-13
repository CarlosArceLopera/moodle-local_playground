<?php
/**
 * English language strings for the playground plugin.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string["category"] = "Category";
$string["category_help"] = "Select the category you would like playground courses to be created in.";
$string["completed"] = "Completed";
$string["create_playground_course"] = "Create playground course";
$string["enrol_confirmation_subject"] = "Your Playground course has been created";
$string["enrol_confirmation_body"] = 'Hello,<br /><br />We\'ve created your course and added you as an instructor. You can access it at {$a->link}<br /><br />Please note, this course should only be used to prepare your content for a future course you have not yet been assigned to, do not add students to this course or run the course from here. Once you have been assigned to your official course offering you can move any content created in this course to that course using the {$a->link2}.<br /><br />Learning Technology Services';
$string["pluginname"] = "Playground creation";
$string["privacy:metadata"] = "This plugin does not store any user data. It simply creates courses and enrols the logged in user.";
$string["playground_completed_text"] = "Your new playground course has been created. Click the button below to access your course.<br>";
$string["playground_course_name"] = "Playground course";
$string["access_control_heading"] = "Access Control Settings";
$string["access_control_heading_desc"] = "Configure who can create playground courses based on user profile fields and ID numbers.";
$string["profile_field_shortname"] = "Profile field shortname";
$string["profile_field_shortname_desc"] = "The shortname of the user profile field that contains user types (e.g., 'ldapusertypes'). Leave blank to disable profile field checking.";
$string["allowed_user_types"] = "Allowed user types";
$string["allowed_user_types_desc"] = "Comma-separated list of user types that are allowed to create playground courses (e.g., 'staff,employee'). Leave blank to allow all user types.";
$string["allowed_idnumber_prefixes"] = "Allowed ID number prefixes";
$string["allowed_idnumber_prefixes_desc"] = "Comma-separated list of ID number prefixes that are allowed to create playground courses (e.g., '1,5' will allow ID numbers starting with 1 or 5). Leave blank to allow all ID numbers.";
$string["require_both_conditions"] = "Require both conditions";
$string["require_both_conditions_desc"] = "If checked, users must meet BOTH the user type and ID number requirements. If unchecked, meeting either condition is sufficient.";

