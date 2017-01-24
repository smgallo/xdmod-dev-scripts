#!/usr/bin/env php
<?php

$xdmodHome = ( getenv('XDMOD_HOME') ? getenv('XDMOD_HOME') : '/data/www/xdmod' );
$linker = $xdmodHome . '/share/configuration/linker.php';
if ( ! is_file($linker) ) {
    exit("Linker file not found '$linker'" . PHP_EOL);
}

require_once $linker;

// --------------------------------------------------------------------------------
// Process arguments

$scriptOptions = array(
    'user' => null
);

$options = array(
    'u:' => 'user:'
);

$args = getopt(implode('', array_keys($options)), $options);

foreach ($args as $arg => $value) {
    switch ($arg) {

        case 'u':
        case 'user':
            // Merge array because long and short options are grouped separately
            $scriptOptions['user'] = $value;
            break;

        default:
            usage_and_exit("Invalid option: $arg");
            break;
    }
}  // foreach ($args as $arg => $value)

if ( null === $scriptOptions['user'] ) {
    exit("Must specify user" . PHP_EOL);
}

// --------------------------------------------------------------------------------
// Verify user

print "Search user profile for user '" . $scriptOptions['user'] . "'" . PHP_EOL;

if ( INVALID === ($uid = XDUser::userExistsWithUsername($scriptOptions['user'])) ) {
    exit("User not found: '" . $scriptOptions['user'] . "'" . PHP_EOL);
}

// --------------------------------------------------------------------------------
// Display saved charts

$user = XDUser::getUserByID($uid);
print "Found user: '" . $user->getUsername() . "' with email '" . $user->getEmailAddress() . "'" . PHP_EOL;

$userProfile = $user->getProfile();
$charts = $userProfile->fetchValue('queries_store');
$chartData = ( isset($charts['data']) ? $charts['data'] : array());
$numCharts = 0;

print "Found " . count($chartData) . " Saved charts" . PHP_EOL . PHP_EOL;

foreach ( $chartData as $chart ) {
    print "Chart #" . ++$numCharts . ", Name: " . $chart['name'] . PHP_EOL
        . print_r( json_decode($chart['config']) , true)
        . PHP_EOL . PHP_EOL;
}
print PHP_EOL;

?>
