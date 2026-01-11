<?php
/**
 * Database upgrade script for the playground plugin.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_playground_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    return true;
}
