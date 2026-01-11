<?php
/**
 * Hook definitions for the playground plugin.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\navigation\secondary_extend::class,
        'callback' => \local_playground\hook_callbacks::class . '::extend_secondary_navigation',
        'priority' => 500,
    ],
    [
        'hook' => \core\hook\output\before_footer_html_generation::class,
        'callback' => \local_playground\hook_callbacks::class . '::inject_mycourses_button',
        'priority' => 500,
    ],
];

