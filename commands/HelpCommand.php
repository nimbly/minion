<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 3:02 PM
 */

namespace minion\commands;


use minion\config\Environment;
use minion\Console;

class HelpCommand {

	public function run(Environment $environment) {

		$helpText = "\n\nMINION usage:\n\nminion <command>[:<action>] [<environment>] [<arguments>]\n\nAvailable commands:\n";


		// Get list of commands installed
		$dir = dir('commands');

		while( ($command = $dir->read()) ) {
			if( preg_match('/^([\w\d_\-\.]+)Command\.php$/', $command, $match) ) {
				$helpText .= strtolower("\t$match[1]\n");
			}
		};

		Console::getInstance()->text($helpText);

	}

}