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
 * ************************************************************************ */

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

    public function enrolUser($roleid, $courseid, $username) {
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

        $idNumber = 'sandbox_' . $userid . '_';

        for ($i = 0; $i < 1000; $i++) {
            if (!$DB->get_record('course', array('idnumber' => $idNumber . $i))) {
                $idNumber = $idNumber . $i;
                break;
            }
        }

        $user = $DB->get_record('user', array('id' => $userid));
        $fullname = "My Playground Course " . $title;
        $shortname = get_string('playground_course_name', 'local_playground') . ' ' . fullname($user) . ' ' . $i;

        //Does category playground exist? if not create it
        if (!$category = $DB->get_record('course_categories', array('name' => 'Playground'))) {

            $cdata = array();
            $cdata['name'] = 'Playground';
            $cdata['parent'] = 0;
            $cdata['visible'] = 1;

            $categoryId = $DB->insert_record('course_categories', $cdata, true);
        } else {
            $categoryId = $category->id;
        }

        $data = new \stdClass();
        $data->fullname = $fullname;
        $data->shortname = $shortname;
        $data->idnumber = $idNumber;
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
        $this->enrolUser($role->id, $newCourse->id, $user->username);

        if (!$newCourse) {
            error_log("Cannot create playground course for user: " . $userid);
        }
        

        $url = $CFG->wwwroot . '/local/playground/completed.php?coursetype=sc&id=' . $newCourse->id;

        return $url;
    }

}
