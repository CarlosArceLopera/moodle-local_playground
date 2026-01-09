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
 * Note: Navigation is now handled via hooks in db/hooks.php
 * - secondary_extend hook: Adds to secondary nav in courses/activities/categories
 * - before_footer_html_generation hook: Adds visible content on dashboard/my pages
 * See classes/hook_callbacks.php for implementation.
 */



