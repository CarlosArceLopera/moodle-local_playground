<?php
/**
 * AJAX endpoint for playground course creation.
 *
 * @package    local_playground
 * @copyright  2026 York University UIT It Innovation & Academic Technologies
 * @author     Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Define AJAX_SCRIPT to suppress debugging output in the response
define('AJAX_SCRIPT', true);

// Prevent session updates during AJAX calls - reduces session lock contention
// This is especially important with database or Redis session handlers
define('NO_SESSION_UPDATE', true);

// Set JSON header early to ensure proper response format
header('Content-Type: application/json');

// Try to load config and handle cache/session initialization errors
try {
    require_once(__DIR__ . '/../../../config.php');
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server configuration error. Please check cache configuration.',
    ]);
    exit;
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server configuration error. Please check cache configuration.',
    ]);
    exit;
}

global $CFG;
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/classes/request.php');

// Verify session key early to prevent CSRF attacks (Moodle 5.1 AJAX standard)
if (!confirm_sesskey()) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid session key. Please refresh the page and try again.'
    ]);
    exit;
}

try {
    require_login();

    // Check if user is eligible to create playground courses
    if (!local_playground_is_user_eligible()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'You do not have permission to create playground courses'
        ]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Authentication error: ' . $e->getMessage()
    ]);
    exit;
}

$action = required_param('action', PARAM_TEXT);
$title = optional_param('title', "", PARAM_TEXT);

global $USER, $CFG;

$REQUEST = new \local_playground\request();

switch ($action) {
    case 'create_playground_course':
        try {
            $url = $REQUEST->createSandboxCourse($USER->id, $title);

            // Return JSON response for better error handling
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'url' => $url
            ]);
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('Playground course creation error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());

            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => (!empty($CFG->debugdisplay) && $CFG->debugdisplay) ? $e->getTraceAsString() : null
            ]);
        }
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unknown action: ' . $action]);
        break;
}