<?php
/**
 * Request class for handling playground course creation.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_playground;

/**
 * Description of playground
 *
 * @author patrick
 */
class request {

    /**
     * 
     * @global \stdClass $CFG
     * @global \moodle_database $DB
     */
    function __construct() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
    }

    public function enrol_user($roleid, $courseid, $username) {
        global $CFG, $DB;
        $user = $DB->get_record('user', array('username' => $username, 'deleted' => 0), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        $context = \context_course::instance($course->id);

        if (!is_enrolled($context, $user)) {

            $enrol = enrol_get_plugin('manual');

            if ($enrol === null) {
                return false;
            }

            $instances = enrol_get_instances($course->id, true);

            $manualinstance = null;
            foreach ($instances as $instance) {
                if ($instance->enrol == 'manual') {
                    $manualinstance = $instance;
                    break;
                }
            }

            if ($manualinstance == null) {
                $instanceid = $enrol->add_default_instance($course);

                if ($instanceid === null) {
                    $instanceid = $enrol->add_instance($course);
                }
                $instance = $DB->get_record('enrol', array('id' => $instanceid));
            }
            $enrol->enrol_user($instance, $user->id, $roleid);
            $stringattrib = new \stdClass();
            $stringattrib->link = "<a href='" . $CFG->wwwroot . "/course/view.php?id=" . $courseid . "'>My Playground Course</a> (" . $CFG->wwwroot . "/course/view.php?id=" . $courseid . ").";
	    $stringattrib->link2 = "<a href=https://lthelp.yorku.ca/adding-content/copy-over-content-from-your-existing-courses>copy content feature</a>";
            email_to_user($user, $user, get_string('enrol_confirmation_subject', 'local_playground'), get_string('enrol_confirmation_body', 'local_playground', $stringattrib), get_string('enrol_confirmation_body', 'local_playground', $stringattrib) . '</a>');
            return true;
        }
        return false;
    }

    /**
     * 
     * @global \stdClass $CFG
     * @global \moodle_database $DB
     * @param int $userid
     * @return string
     */
    public function createSandboxCourse($userid, $title = "") {
        global $CFG, $DB;
        require_login(1, false);
        include_once ( $CFG->dirroot . '/course/lib.php');

        $user = $DB->get_record('user', array('id' => $userid));
        $fullname = get_string('playground_course_name', 'local_playground') . " " . $title;

        // Generate unique shortname for instant creation without database queries
        // Use hash of userid+microtime to ensure uniqueness without exposing user IDs (security)
        // Hash ensures: no user enumeration, privacy protection, handles multiple courses per user
        // Format: "My Playground Course John Doe a1b2c3d4" where a1b2c3d4 is unique hash
        $baseShortname = get_string('playground_course_name', 'local_playground') . ' ' . fullname($user);
        $uniqueHash = substr(md5($userid . microtime(true)), 0, 8); // 8-char hash for brevity
        $shortname = $baseShortname . ' ' . $uniqueHash;

        // Generate unique idnumber for external integrations (LTI, web services, SIS, etc.)
        // Format: playground_hash - hash already includes userid for uniqueness without exposing it
        // Using hash instead of counting courses avoids database queries for optimal performance
        $idnumber = 'playground_' . $uniqueHash;

        // Get the configured playground category from settings.
        $categoryId = get_config('local_playground', 'playground_category');

        // If no category is configured or the category doesn't exist, use the default category (Miscellaneous).
        if (empty($categoryId) || !$DB->record_exists('course_categories', array('id' => $categoryId))) {
            // Fall back to the top-level Miscellaneous category (id = 1) or create a Playground category.
            if (!$category = $DB->get_record('course_categories', array('name' => 'Playground'))) {
                $cdata = array();
                $cdata['name'] = 'Playground';
                $cdata['parent'] = 0;
                $cdata['visible'] = 1;
                $categoryId = $DB->insert_record('course_categories', $cdata, true);
            } else {
                $categoryId = $category->id;
            }
        }

        $data = new \stdClass();
        $data->fullname = $fullname;
        $data->shortname = $shortname;
        $data->idnumber = $idnumber; // Unique playground identifier for external integrations
        $data->visible = 1;
        $data->category = $categoryId;
        $data->enablecompletion = 1;
        $data->timecreated = time();
        $data->timemodified = time();
        $data->newsitems = 0;   // ED July 15th, 2019 Needed in order to not have an empty Announcements forum get created in the course
        $data->numsections = 4; // Create course with 4 sections

        $newCourse = create_course($data);

        //Now enrol requesting user as teacher
        $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->enrol_user($role->id, $newCourse->id, $user->username);

        if (!$newCourse) {
            error_log("Cannot create playground course for user: " . $userid);
        }
        

        $url = $CFG->wwwroot . '/local/playground/completed.php?coursetype=sc&id=' . $newCourse->id;

        return $url;
    }

}
