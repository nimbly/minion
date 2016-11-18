<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 7:31 PM
 */

namespace minion\commands;

use minion\config\Context;
use minion\Connection;
use minion\interfaces\CommandInterface;
use minion\Task;

class DeployCommand implements CommandInterface {

	public function run(Context $context) {

		// Check for required environment param
		if( ($env = $context->getArgument(['e', 'environment'])) === null ) {
			throw new \Exception("No environment specified");
		}

		// Get the environment config
		if( ($environment = $context->config->getEnvironment($env)) == false ) {
			throw new \Exception("No environment config found for \"{$env}\".");
		}

		// loop through servers and implement strategy on each
		foreach( $environment->servers as $server ) {

			if( empty($server->strategy) ) {
				throw new \Exception("No strategy defined! Please define a global strategy, an environment strategy, or a per-server strategy. See manual for more info on strategies.");
			}

			$connection = new Connection($server, $environment->authentication);

			foreach( $server->strategy as $task ) {
				Task::run($task, $context, $environment, $connection);
			}

			$connection->close();
		}

		$message = trim(`whoami`) . " deployed new API release to {$env}.";
		exec("curl -X POST \"https://hooks.slack.com/services/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\" -d '{\"text\": \"{$message}\"}'");
	}

}