#!/bin/php
<?php

$currentVersion = file_get_contents('VERSION');

if( isset($argv[1]) ) {
    $version = $argv[1];
} else {
	echo "\nERROR: You must provide a version.\n\n";
	exit;
}

echo "\nTag and submit a new release of minion.\n\n";
echo "Current version: {$currentVersion}\n";
echo "New version: {$version}\n\n";

echo "Are you sure you want to do this?  Type 'yes' to continue: ";

$input = fopen("php://stdin", "r");
$response = trim(fgets($input));

if( strtolower($response) !== 'yes' ){
	echo "Aborting.\n\n";
	exit;
}

// Update composer.json file with latest version number
$composer = json_decode(file_get_contents('composer.json'));
$composer->version = (string)$version;
file_put_contents('composer.json', json_encode($composer));

// Write version number to disk
file_put_contents('VERSION', $version);

// Commit & push changes, tag commit and push again
exec("git commit -am \"Tagging minion version {$version}\"");
exec("git push");
exec("git tag {$version}");
exec("git push --tags");