<?php
/**
 * Hook callback implementations for the playground plugin.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_playground;

use core\navigation\navigation_node;
use core\output\pix_icon;

/**
 * Hook callbacks for local_playground
 */
class hook_callbacks {

    /**
     * Extend the secondary navigation menu for courses, modules, and categories
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

    /**
     * Inject playground button into my/courses page via footer HTML
     * This uses the before_footer_html_generation hook which fires on all pages
     *
     * @param \core\hook\output\before_footer_html_generation $hook
     */
    public static function inject_mycourses_button(\core\hook\output\before_footer_html_generation $hook): void {
        global $PAGE, $USER, $OUTPUT;

        // Check if we're on my/courses page
        if ($PAGE->pagetype !== 'my-index') {
            return;
        }

        // Additional check for the courses.php file
        if (strpos($PAGE->url->get_path(), '/my/courses.php') === false) {
            return;
        }

        // Only show if user is logged in and not a guest
        if (!isloggedin() || isguestuser()) {
            return;
        }

        // Only show if user has courses
        $user_courses = enrol_get_all_users_courses($USER->id, true);
        if (empty($user_courses)) {
            return;
        }

        // Generate the button HTML with CSS to position it at top right
        $button_url = new \moodle_url('/local/playground/index.php');
        $button_text = get_string('create_playground_course', 'local_playground');

        $button_html = $OUTPUT->render_from_template('local_playground/mycourses_button', ['button_url' => $button_url, 'button_text' => $button_text]);

        // Inject the button HTML before the footer
        $hook->add_html($button_html);
    }
}
