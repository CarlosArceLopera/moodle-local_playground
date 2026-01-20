<?php
/**
 * Diagnostic script to check server configuration
 *
 * Access this via: https://preview.eclass.yorku.ca/local/playground/diagnostics.php
 *
 * @package    local_playground
 */

header('Content-Type: text/plain');
echo "=== PLAYGROUND PLUGIN DIAGNOSTICS ===\n\n";

echo "1. Checking __DIR__...\n";
echo "   Plugin directory: " . __DIR__ . "\n\n";

echo "2. Checking two-level config.php setup...\n";
$config_bootstrap = __DIR__ . '/../../../config.php';
$config_real = __DIR__ . '/../../../../config.php';
echo "   Bootstrap config: $config_bootstrap\n";
echo "   Bootstrap exists: " . (file_exists($config_bootstrap) ? "YES" : "NO") . "\n";
if (file_exists($config_bootstrap)) {
    echo "   Bootstrap is readable: " . (is_readable($config_bootstrap) ? "YES" : "NO") . "\n";
}
echo "   Real config: $config_real\n";
echo "   Real config exists: " . (file_exists($config_real) ? "YES" : "NO") . "\n";
if (file_exists($config_real)) {
    echo "   Real config is readable: " . (is_readable($config_real) ? "YES" : "NO") . "\n";
}
echo "\n";

echo "3. Attempting to load config.php...\n";
try {
    require_once($config_bootstrap);
    echo "   ✓ config.php loaded successfully\n\n";
} catch (Exception $e) {
    echo "   ✗ ERROR loading config.php: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo "   " . str_replace("\n", "\n   ", $e->getTraceAsString()) . "\n\n";
    exit;
}

echo "4. Checking \$CFG object...\n";
global $CFG;
echo "   isset(\$CFG): " . (isset($CFG) ? "YES" : "NO") . "\n";
echo "   is_object(\$CFG): " . (is_object($CFG) ? "YES" : "NO") . "\n\n";

if (!isset($CFG) || !is_object($CFG)) {
    echo "   ✗ CRITICAL: \$CFG is not properly initialized!\n";
    exit;
}

echo "5. Checking critical \$CFG properties...\n";
$critical_props = ['wwwroot', 'dataroot', 'dirroot', 'libdir', 'dbtype', 'dbhost', 'dbname', 'dbuser'];

foreach ($critical_props as $prop) {
    $exists = property_exists($CFG, $prop);
    $value = $exists ? $CFG->$prop : 'NOT SET';
    $status = ($exists && !empty($value)) ? "✓" : "✗";
    echo "   $status \$CFG->$prop = ";

    // Don't show sensitive info
    if (in_array($prop, ['dbpass', 'dbuser'])) {
        echo ($exists && !empty($value)) ? "[SET]" : "[NOT SET]";
    } else {
        echo "$value";
    }
    echo "\n";
}
echo "\n";

echo "6. Checking database connection...\n";
global $DB;
echo "   isset(\$DB): " . (isset($DB) ? "YES" : "NO") . "\n";
if (isset($DB)) {
    try {
        $dbtest = $DB->get_record_sql("SELECT 1 as test");
        echo "   ✓ Database connection working\n\n";
    } catch (Exception $e) {
        echo "   ✗ Database connection error: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "   ✗ \$DB not initialized\n\n";
}

echo "7. Checking file paths and lib/setup.php locations...\n";
echo "   \$CFG->dirroot: " . (isset($CFG->dirroot) ? $CFG->dirroot : 'NOT SET') . "\n";
echo "   \$CFG->dirroot exists: " . (isset($CFG->dirroot) && is_dir($CFG->dirroot) ? "YES" : "NO") . "\n";
echo "   \$CFG->libdir: " . (isset($CFG->libdir) ? $CFG->libdir : 'NOT SET') . "\n";
echo "   \$CFG->libdir exists: " . (isset($CFG->libdir) && is_dir($CFG->libdir) ? "YES" : "NO") . "\n";
echo "   \$CFG->dataroot exists: " . (isset($CFG->dataroot) && is_dir($CFG->dataroot) ? "YES" : "NO") . "\n";

echo "\n   Checking where lib/setup.php might be:\n";
$possible_lib_paths = [
    'From real config dir' => dirname($config_real) . '/lib/setup.php',
    'From bootstrap config dir' => dirname($config_bootstrap) . '/lib/setup.php',
];

if (isset($CFG->dirroot)) {
    $possible_lib_paths['From $CFG->dirroot'] = $CFG->dirroot . '/lib/setup.php';
}

foreach ($possible_lib_paths as $label => $path) {
    $exists = file_exists($path);
    $status = $exists ? "✓" : "✗";
    echo "   $status $label:\n";
    echo "      $path\n";
}
echo "\n";

echo "8. Checking plugin files...\n";
$plugin_files = ['locallib.php', 'version.php', 'classes/request.php', 'ajax.php'];
foreach ($plugin_files as $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    $status = $exists ? "✓" : "✗";
    echo "   $status $file\n";
}
echo "\n";

echo "9. Testing require_login()...\n";
try {
    require_login();
    global $USER;
    echo "   ✓ User authentication works\n";
    echo "   Current user ID: " . ($USER->id ?? 'NOT SET') . "\n";
    echo "   Current username: " . ($USER->username ?? 'NOT SET') . "\n\n";
} catch (Exception $e) {
    echo "   ✗ Authentication error: " . $e->getMessage() . "\n\n";
}

echo "10. Testing eligibility check...\n";
require_once(__DIR__ . '/locallib.php');
try {
    $eligible = local_playground_is_user_eligible();
    echo "   User is " . ($eligible ? "ELIGIBLE" : "NOT ELIGIBLE") . " to create playground courses\n\n";
} catch (Exception $e) {
    echo "   ✗ Eligibility check error: " . $e->getMessage() . "\n\n";
}

echo "=== DIAGNOSTICS COMPLETE ===\n\n";

if (empty($CFG->libdir)) {
    echo "⚠️  CRITICAL ISSUE FOUND:\n";
    echo "   \$CFG->libdir is not set!\n";
    echo "   This is causing your array_key_exists() error.\n\n";
    echo "   The server's config.php should have:\n";
    echo "   require_once(__DIR__ . '/lib/setup.php');\n\n";
    echo "   But lib/setup.php might be in a different location on your server.\n";
    echo "   Check section 7 above to see where lib/setup.php actually exists.\n";
} else {
    echo "✅ All critical configuration properties are set.\n";
    echo "\nIf you're still having issues:\n";
    echo "1. Check the eligibility settings (allowed user types, ID number prefixes)\n";
    echo "2. Check server error logs for 'PLAYGROUND:' messages\n";
    echo "3. Ensure the user has the correct profile field values\n";
}

