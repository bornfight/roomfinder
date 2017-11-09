<?php
/**
 * Create a google app here:
 * https://console.developers.google.com/apis/credentials/oauthclient
 *
 * Export the client_secret.json file
 * Save it to disk, and paste the path to the file into $clientSecretPath
 * For $credentialsPath, use the location on your disk where you want the auth script to create your auth file
 */

include '../vendor/autoload.php';

use degordian\RoomFinder\Adapters\RoomResourceGoogleCalendar;

/**
 * Edit paths to credentials
 */
$appName = 'FindARoom';
$clientSecretPath = '/PATH_TO_CREDENTIALS/client_secret.json';
$credentialsPath = '/PATH_TO_CREDENTIALS/calendar-roomfinder.json';


$roomResourceAdapter = new RoomResourceGoogleCalendar();
$roomResourceAdapter->setConfig([
    'applicationName' => $appName,
    'credentialsPath' => $credentialsPath,
    'clientSecretPath' => $clientSecretPath,
    'scopes' => [\Google_Service_Calendar::CALENDAR],
    'accessType' => 'offline',
]);

if ($roomResourceAdapter->createAuthentication()) {
    echo 'Successful authentication, created file: ' . $credentialsPath . "\n";
} else {
    echo 'Authentication failed' . "\n";
}
