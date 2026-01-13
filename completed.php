<?php
/**
 * Completion page for playground course creation.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');
require_once('locallib.php');
require_once($CFG->dirroot . '/local/playground/classes/request.php');

/**
 * Display the content of the page
 * @global stdobject $CFG
 * @global moodle_database $DB
 * @global core_renderer $OUTPUT
 * @global moodle_page $PAGE
 * @global stdobject $SESSION
 * @global stdobject $USER
 */
function display_page() {
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

    $REQUEST = new \local_playground\request();

    echo local_playground_page('/local/playground/completed.php?id=' . $id, $pagetitle, $pageheading, $context);

    $HTMLcontent = '';
    //**********************
    //*** DISPLAY HEADER ***
    //**********************
    echo $OUTPUT->header();
    $course = $DB->get_record('course', array('id' => $id));
    $templatecontext = [
        'course_id' => $course->id,
        'course_name' => $course->fullname,
        'wwwroot' => $CFG->wwwroot
    ];
    //**********************
    //*** DISPLAY CONTENT **
    //**********************-
    echo $OUTPUT->render_from_template('local_playground/completed', $templatecontext);
    //**********************
    //*** DISPLAY FOOTER ***
    //**********************
    echo $OUTPUT->footer();
}

display_page();
?>