<?php
// This is a test script to check the get_tev_claim.php response

// Simulate a GET request with a sample claim ID
$_GET['id'] = 1; // Replace with an actual claim ID from your database

// Start output buffering to capture the output
ob_start();

// Include the get_tev_claim.php file
include 'get_tev_claim.php';

// Get the output
$output = ob_get_clean();

// Output the raw response and content type
header('Content-Type: text/plain');
echo "=== RAW RESPONSE ===\n\n";
echo $output;

// Try to decode the JSON
$json = json_decode($output, true);
echo "\n\n=== PARSED JSON ===\n\n";
print_r($json);

// Check for JSON errors
echo "\n\n=== JSON ERRORS ===\n";
switch (json_last_error()) {
    case JSON_ERROR_NONE:
        echo ' - No errors';
    break;
    case JSON_ERROR_DEPTH:
        echo ' - Maximum stack depth exceeded';
    break;
    case JSON_ERROR_STATE_MISMATCH:
        echo ' - Underflow or the modes mismatch';
    break;
    case JSON_ERROR_CTRL_CHAR:
        echo ' - Unexpected control character found';
    break;
    case JSON_ERROR_SYNTAX:
        echo ' - Syntax error, malformed JSON';
    break;
    case JSON_ERROR_UTF8:
        echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
    break;
    default:
        echo ' - Unknown error';
    break;
}
?>
