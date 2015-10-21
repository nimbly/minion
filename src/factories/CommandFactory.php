<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 7:31 PM
 */

namespace minion\factories;


use minion\config\Environment;
use minion\Console;

class CommandFactory {

	/**
	 * @param \minion\config\Environment $environment
	 *
	 * @return boolean
	 * @throws \Exception
	 */
	public static function run(Environment $environment) {

		$commandClass = "\\minion\\commands\\".ucwords(strtolower($environment->command))."Command";

		if( class_exists($commandClass) === false ) {
			throw new \Exception("Unknown command {$environment->command}");
		}

		if( $environment->action ) {
			Console::getInstance()->text('Running ')->bold()->text($environment->command.':'.$environment->action)->nostyle()->text(' command.')->lf();
		} else {
			Console::getInstance()->text('Running ')->bold()->text($environment->command)->nostyle()->text(' command.')->lf();
		}

		$command = new $commandClass;

		if( $environment->action ) {

			$action = $environment->action;

		} else {

			$action = 'run';

		}

		if( method_exists($command, $action) === false ) {
			throw new \Exception("Command action \"{$action}\" does not exist!");
		}


		$command->{$action}($environment);

		if( $environment->action ) {
			Console::getInstance()->text('Command ')->bold()->text($environment->command.':'.$environment->action)->nostyle()->text(' done!')->lf();
		} else {
			Console::getInstance()->text('Command ')->bold()->text($environment->command)->nostyle()->text(' done!')->lf();
		}

		return true;
	}

}