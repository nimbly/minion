<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 3/14/16
 * Time: 2:20 PM
 */

namespace minion\commands;


use minion\config\Context;
use minion\interfaces\CommandInterface;
use minion\RemoteConnection;
use minion\Task;

class TaskCommand implements CommandInterface {

	public function run(Context $context)
    {

		// Get environment argument
		if( ($env = $context->getArgument(['e', 'environment'])) === null ) {
			throw new \Exception("No environment specified");
		}

		// Get task argument
		if( ($task = $context->nextNamedArgument()) == null ){
			throw new \Exception("No task specified. Specify a task with either -t or --task argument.");
		}

		// Get the environment config
		if( ($environment = $context->config->getEnvironment($env)) == false ) {
			throw new \Exception("No environment config found for \"{$env}\".");
		}

		// loop through servers and run task
		foreach( $environment->servers as $server ) {
			$connection = new RemoteConnection($server, $environment->authentication);
			Task::run($task, $context, $environment, $connection);
			$connection->close();
		}

	}

}