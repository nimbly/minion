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

$input = fopen ("php://stdin","r");
$response = trim(fgets($input));

if( strtolower($response) !== 'yes' ){
	echo "Aborting.\n\n";
	exit;
}

file_put_contents('VERSION', $version);

exec("git commit -am \"Tagging minion version {$version}\"");
exec("git push");
exec("git tag {$version}");
exec("git push --tags");