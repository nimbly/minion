<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/6/15
 * Time: 5:39 PM
 */

namespace minion\commands;


use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use minion\config\Context;
use minion\config\Environment;
use minion\Connection;
use minion\Console;
use minion\interfaces\CommandInterface;
use minion\RemoteConnection;

class RollbackCommand implements CommandInterface {

	public function run(Context $context) {

		// Check for required environment param
		if( ($env = $context->getArgument(['e', 'environment'])) === null ) {
			throw new \Exception("No environment specified");
		}

		// Get the environment config
		if( ($environment = $context->config->getEnvironment($env)) == false ) {
			throw new \Exception("No environment config found for \"{$env}\".");
		}

		// Specific release to roll back to?
		$specificRelease = $context->getArgument(['r', 'release']);

		foreach( $environment->servers as $server ) {

			$connection = new RemoteConnection($server, $environment->authentication);

			// get the current releases
			$releases = $connection->execute("ls {$environment->remote->path}/releases");
			$releases = explode("\n", trim($releases));

			$release = null;

			// Specific release to rollback to
			if( $specificRelease ) {
				foreach($releases as $r ) {
					if( $specificRelease == $r ) {
						$release = $r;
					}
				}

				if( !$release ) {
					throw new \Exception("Release {$specificRelease} not found");
				}
			}

			// Just rollback to previous release
			elseif( count($releases) > 1 ) {
				$release = $releases[count($releases) - 2];
			}

			else {
				throw new \Exception("No previous release available");
			}

			// Do the rollback
			$connection->execute("cd {$environment->remote->path}&&rm -f current&&ln -s -r releases/{$release} current");
		}
	}

}