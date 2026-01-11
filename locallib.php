<?php
/**
 * Local library functions for the playground plugin.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Creates the Moodle page header
 * @global stdobject $CFG
 * @global moodle_database $DB
 * @param string $url Current page url
 * @param string $pagetitle  Page title
 * @param string $pageheading Page heading (Note hard coded to site fullname)
 * @param array $context The page context (SYSTEM, COURSE, MODULE etc)
 * @return HTML Contains page information and loads all Javascript and CSS
 */
function local_playground_page($url, $pagetitle, $pageheading, $context) {
    global $CFG, $PAGE, $SITE;

    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('local_playground',
            current_language());

    $PAGE->set_url($CFG->wwwroot . $url);
    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($pageheading);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_context($context);
    //Language strings for Javascript
    $PAGE->requires->strings_for_js(array_keys($strings), 'local_playground');
}

