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
        $button_text = get_string('create_sandbox_course', 'local_playground');

        $button_html = <<<HTML
<style>
    /* Position the playground button in the header action area */
    #local-playground-mycourses-button {
        display: inline-block;
        margin: 0;
        padding: 0;
    }
    
    #local-playground-mycourses-button form {
        display: inline-block;
    }
    
    #local-playground-mycourses-button .btn {
        margin: 0;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
</style>

<div id="local-playground-mycourses-button">
    <form action="{$button_url}" method="get" class="d-inline">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-flask" aria-hidden="true"></i>
            {$button_text}
        </button>
    </form>
</div>

<script>
    // Move the playground button to the header area to align with course management buttons
    document.addEventListener('DOMContentLoaded', function() {
        var playgroundButton = document.getElementById('local-playground-mycourses-button');
        if (playgroundButton) {
            // Find the header actions container or create one
            var headerActionsContainer = document.querySelector('.page-header-actions');
            
            if (headerActionsContainer) {
                // Move button to header actions
                headerActionsContainer.appendChild(playgroundButton.cloneNode(true));
                playgroundButton.remove();
            } else {
                // Alternative: try to find the btn-group and add to it
                var btnGroup = document.querySelector('.btn-group');
                if (btnGroup) {
                    // Insert after the btn-group
                    btnGroup.parentNode.insertBefore(playgroundButton, btnGroup.nextSibling);
                    playgroundButton.style.marginLeft = '10px';
                }
            }
        }
    });
</script>
HTML;

        // Inject the button HTML before the footer
        $hook->add_html($button_html);
    }
}
