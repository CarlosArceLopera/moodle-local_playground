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
require_once(__DIR__ . '/../../../config.php');
require_once('locallib.php');

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

$id = optional_param('id', 0, PARAM_INT); //List id

require_login(1, false); //Use course 1 because this has nothing to do with an actual course, just like course 1

$context = context_system::instance();


$pagetitle = get_string('pluginname', 'local_playground');
$pageheading = get_string('pluginname', 'local_playground');

echo local_playground_page($CFG->wwwroot . '/local/playground/index.php', $pagetitle, $pageheading, $context);

$HTMLcontent = '';
//**********************
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();

//**********************
//*** DISPLAY CONTENT **
//**********************-
echo $OUTPUT->render_from_template('local_playground/step1', null);
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();

