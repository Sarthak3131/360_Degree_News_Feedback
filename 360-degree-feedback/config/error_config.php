<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Check required extensions
if (!extension_loaded('curl')) {
    error_log('cURL extension is not installed. Please install php-curl');
}

if (!extension_loaded('json')) {
    error_log('JSON extension is not installed. Please install php-json');
}
?>
