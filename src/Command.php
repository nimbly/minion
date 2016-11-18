<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 7:31 PM
 */

namespace minion;


use minion\config\Context;
use minion\interfaces\CommandInterface;

class Command {

	/**
	 * @param Context $context
	 *
	 * @return CommandInterface
	 * @throws \Exception
	 */
	public static function run(Context $context) {

		$commandClass = "\\minion\\commands\\".ucwords(strtolower($context->command))."Command";

		if( class_exists($commandClass) === false ) {
			throw new \Exception("Unknown command {$context->command}");
		}

		/** @var CommandInterface $command */
		$command = new $commandClass;

		// Check for run method
		if( method_exists($command, 'run') === false ) {
			throw new \Exception("Command {$context->command} run method does not exist");
		}

		return $command->run($context);

	}

}