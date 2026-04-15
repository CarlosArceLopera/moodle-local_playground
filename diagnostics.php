<?php
/**
 * Diagnostic script to check server configuration — site admins only.
 *
 * Access this via: /local/playground/diagnostics.php
 *
 * @package    local_playground
 */

require_once(__DIR__ . '/../../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

header('Content-Type: text/plain');
echo "=== PLAYGROUND PLUGIN DIAGNOSTICS ===\n\n";

echo "1. Checking __DIR__...\n";
echo "   Plugin directory: " . __DIR__ . "\n\n";

echo "2. Checking \$CFG object...\n";
global $CFG;
echo "   isset(\$CFG): " . (isset($CFG) ? "YES" : "NO") . "\n";
echo "   is_object(\$CFG): " . (is_object($CFG) ? "YES" : "NO") . "\n\n";

if (!isset($CFG) || !is_object($CFG)) {
    echo "   ✗ CRITICAL: \$CFG is not properly initialized!\n";
    exit;
}

echo "3. Checking critical \$CFG properties...\n";
$critical_props = ['wwwroot', 'dataroot', 'dirroot', 'libdir', 'dbtype', 'dbhost', 'dbname', 'dbuser'];

foreach ($critical_props as $prop) {
    $exists = property_exists($CFG, $prop);
    $value = $exists ? $CFG->$prop : 'NOT SET';
    $status = ($exists && !empty($value)) ? "✓" : "✗";
    echo "   $status \$CFG->$prop = ";
    if (in_array($prop, ['dbpass', 'dbuser'])) {
        echo ($exists && !empty($value)) ? "[SET]" : "[NOT SET]";
    } else {
        echo "$value";
    }
    echo "\n";
}
echo "\n";

echo "4. Checking database connection...\n";
global $DB;
echo "   isset(\$DB): " . (isset($DB) ? "YES" : "NO") . "\n";
if (isset($DB)) {
    try {
        $DB->get_record_sql("SELECT 1 as test");
        echo "   ✓ Database connection working\n\n";
    } catch (Exception $e) {
        echo "   ✗ Database connection error: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "   ✗ \$DB not initialized\n\n";
}

echo "5. Checking file paths...\n";
echo "   \$CFG->dirroot: " . $CFG->dirroot . "\n";
echo "   \$CFG->dirroot exists: " . (is_dir($CFG->dirroot) ? "YES" : "NO") . "\n";
echo "   \$CFG->libdir: " . $CFG->libdir . "\n";
echo "   \$CFG->libdir exists: " . (is_dir($CFG->libdir) ? "YES" : "NO") . "\n";
echo "   \$CFG->dataroot exists: " . (is_dir($CFG->dataroot) ? "YES" : "NO") . "\n\n";

echo "6. Checking plugin files...\n";
$plugin_files = ['locallib.php', 'version.php', 'classes/request.php', 'ajax.php'];
foreach ($plugin_files as $file) {
    $path = __DIR__ . '/' . $file;
    $status = file_exists($path) ? "✓" : "✗";
    echo "   $status $file\n";
}
echo "\n";

echo "7. Testing eligibility check...\n";
require_once(__DIR__ . '/locallib.php');
global $USER;
try {
    $eligible = local_playground_is_user_eligible();
    echo "   Current user: " . $USER->username . " (id=" . $USER->id . ")\n";
    echo "   User is " . ($eligible ? "ELIGIBLE" : "NOT ELIGIBLE") . " to create playground courses\n\n";
} catch (Exception $e) {
    echo "   ✗ Eligibility check error: " . $e->getMessage() . "\n\n";
}

echo "=== DIAGNOSTICS COMPLETE ===\n";

