<?php
require_once '../Libraries/vendor/autoload.php';

use Appwrite\Client;
use Appwrite\Services\Account;

// Appwrite Configuration
$client = new Client();
$client
    ->setEndpoint('https://cloud.appwrite.io/v1') // Replace with your Appwrite endpoint
    ->setProject('accessframe'); // Replace with your Appwrite project ID

$account = new Account($client);

// Define success and failure redirect URLs
$successUrl = 'http://localhost'; // Replace with your success URL
$failureUrl = 'http://localhost'; // Replace with your failure URL

try {
    // Step 1: Redirect to Google OAuth Login
    if (!isset($_GET['oauth'])) {
        $provider = 'google'; // Specify Google as the OAuth provider
        $oauthUrl = "https://cloud.appwrite.io/v1/account/sessions/oauth2/google?project=accessframe&success=$successUrl&failure=$failureUrl";

        // Redirect to the Google login page
        header("Location: $oauthUrl");
        exit();
    }

    // Step 2: Handle Successful Login and Fetch User Details
    $user = $account->get(); // Get the authenticated user's details
    echo "<h1>Login Successful</h1>";
    echo "<p>Welcome, " . htmlspecialchars($user['name']) . "!</p>";
    echo "<pre>" . print_r($user, true) . "</pre>"; // Debugging user data

} catch (\Exception $e) {
    // Handle errors
    echo "<h1>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
