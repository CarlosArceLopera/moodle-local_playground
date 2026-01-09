<?php
/**
 * *************************************************************************
 * *                           playground                                 **
 * *************************************************************************
 * @package     local                                                     **
 * @subpackage  playground                                                **
 * @name        playground                                                **
 * @copyright   Glendon ITS York University                               **
 * @link        http://www.glendon.yorku.ca                               **
 * @author      Patrick Thibaudeau                                        **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * *************************************************************************/

function local_playground_cron() {
    global $CFG, $DB;

    return false;
}

/**
 * Inject the "Create playground course" button into the my/courses page
 * This function should be called by a hook or directly from the my/courses.php page
 *
 * @return string HTML for the playground button
 */
function local_playground_render_mycourses_button() {
    global $PAGE, $USER, $OUTPUT;

    // Only show on my/courses.php page
    if ($PAGE->pagetype !== 'my-index') {
        return '';
    }

    // Only show if user is logged in and not a guest
    if (!isloggedin() || isguestuser()) {
        return '';
    }

    // Only show if user has at least one course
    $user_courses = enrol_get_all_users_courses($USER->id, true);
    if (empty($user_courses)) {
        return '';
    }

    // Create context data for the template
    $context = new \stdClass();
    $context->playgroundurl = new \moodle_url('/local/playground/index.php');
    $context->label = get_string('create_sandbox_course', 'local_playground');

    // Render using template
    return $OUTPUT->render_from_template('local_playground/mycourses_button', $context);
}

/**
 * Note: Navigation is now handled via hooks in db/hooks.php
 * - secondary_extend hook: Adds to secondary nav in courses/activities/categories
 * See classes/hook_callbacks.php for implementation.
 *
 * For my/courses page button injection, use the local_playground_render_mycourses_button() function
 */





