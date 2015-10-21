<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 7:31 PM
 */

namespace minion\commands;

use minion\config\Environment;
use minion\Connection;
use minion\factories\TaskFactory;

class DeployCommand {

	public $actions = [

		'release' => [
			'description' => 'Create a new release by cloning repo',
		],

		'update' => [
			'description' => 'Update existing release',
		],

	];


	/**
	 * @param Environment $environment
	 *
	 * @throws \Exception
	 */
	public function release(Environment $environment) {

		foreach( $environment->servers as $server ) {

			$connection = new Connection($server, $environment->authentication);

			// Do the deploy
			TaskFactory::run('Release', $environment, $connection);

			// Check/update permissions
			TaskFactory::run('Permissions', $environment, $connection);

			// Should we run migrations on this server?
			if( $server->migrate ) {

				TaskFactory::run('Migrate', $environment, $connection);
			}

			// Run some cleanup
			TaskFactory::run('Cleanup', $environment, $connection);

			$connection->close();
		}
	}

	/**
	 * @param Environment $environment
	 */
	public function update(Environment $environment) {

		foreach( $environment->servers as $server ) {

			$connection = new Connection($server, $environment->authentication);

			// Do the deploy
			TaskFactory::run('Update', $environment, $connection);

			// Check/update permissions
			TaskFactory::run('Permissions', $environment, $connection);

			// Should we run migrations on this server?
			if( $server->migrate ) {
				TaskFactory::run('Migrate', $environment, $connection);
			}

			$connection->close();
		}
	}

}