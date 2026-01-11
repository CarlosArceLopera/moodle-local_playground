<?php
/**
 * AJAX endpoint for playground course creation.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');
require_once('locallib.php');
require_once($CFG->dirroot . '/local/playground/classes/request.php');

$action = required_param('action', PARAM_TEXT);
$title = optional_param('title', "", PARAM_TEXT);

$REQUEST = new \local_playground\request();

global $CFG, $USER;

switch ($action) {

    case 'create_playground_course':
        $url = $REQUEST->createSandboxCourse($USER->id, $title);
        echo $url;
        break;
}