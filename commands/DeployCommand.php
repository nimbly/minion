<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 7:31 PM
 */

namespace minion\commands;

use minion\config\Context;
use minion\LocalConnection;
use minion\RemoteConnection;
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

		if( $environment->preDeploy ){
			$connection = new LocalConnection;
			foreach( $environment->preDeploy as $task ){
				Task::run($task, $context, $environment, $connection);
			}
		}

		// loop through servers and implement strategy on each
		foreach( $environment->servers as $server ) {

			if( empty($server->strategy) ) {
				throw new \Exception("No strategy defined! Please define a global, environmental, or per-server strategy. See the manual for defining strategies.");
			}

			$connection = new RemoteConnection($server, $environment->authentication);

			foreach( $server->strategy as $task ) {
				Task::run($task, $context, $environment, $connection);
			}

			$connection->close();
		}

		if( $environment->postDeploy ){
			$connection = new LocalConnection;
			foreach( $environment->postDeploy as $task ){
				Task::run($task, $context, $environment, $connection);
			}
		}

	}

}