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

namespace local_playground;

use core\navigation\navigation_node;
use core\output\pix_icon;

/**
 * Hook callbacks for local_playground
 */
class hook_callbacks {

    /**
     * Extend the secondary navigation menu
     *
     * @param \core\hook\navigation\secondary_extend $hook
     */
    public static function extend_secondary_navigation(\core\hook\navigation\secondary_extend $hook): void {
        global $PAGE;

        // Only add the link if the user is logged in.
        if (!isloggedin() || isguestuser()) {
            return;
        }

        // Get the secondary navigation view.
        $secondaryview = $hook->get_secondaryview();

        // Only show on courses, modules, and categories (NOT dashboard/my pages)
        $context = $PAGE->context;
        $allowedcontexts = [
            CONTEXT_COURSE,    // Course pages
            CONTEXT_MODULE,    // Activity/resource pages
            CONTEXT_COURSECAT, // Category pages
        ];

        // Only proceed if we're in an allowed context
        if (!in_array($context->contextlevel, $allowedcontexts)) {
            return;
        }

        // Create the navigation node.
        $node = new navigation_node([
            'text' => get_string('pluginname', 'local_playground'),
            'action' => new \moodle_url('/local/playground/index.php'),
            'type' => navigation_node::TYPE_CUSTOM,
            'key' => 'local_playground',
            'icon' => new pix_icon('i/course', ''),
        ]);

        // Ensure the node is visible in secondary navigation.
        $node->showinsecondarynavigation = true;

        // Add the node to the secondary navigation.
        $secondaryview->add_node($node);
    }
}

