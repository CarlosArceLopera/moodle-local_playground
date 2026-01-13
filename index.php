<?php
/**
 * Main page for creating playground courses.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');
require_once('locallib.php');

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

$id = optional_param('id', 0, PARAM_INT); //List id

require_login(1, false); //Use course 1 because this has nothing to do with an actual course, just like course 1

$context = context_system::instance();

// Check if user is eligible to create playground courses
if (!local_playground_is_user_eligible()) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('pluginname', 'local_playground'));
}

$pagetitle = get_string('pluginname', 'local_playground');
$pageheading = get_string('pluginname', 'local_playground');

echo local_playground_page('/local/playground/index.php', $pagetitle, $pageheading, $context);

$HTMLcontent = '';
//**********************
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();

//**********************
//*** DISPLAY CONTENT **
//**********************-
$templatecontext = [
    'sesskey' => sesskey()
];
echo $OUTPUT->render_from_template('local_playground/step1', $templatecontext);
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();

