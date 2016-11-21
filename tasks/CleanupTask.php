<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/6/15
 * Time: 4:56 PM
 */

namespace minion\tasks;


use minion\config\Context;
use minion\config\Environment;
use minion\interfaces\ConnectionInterface;
use minion\interfaces\TaskInterface;

class CleanupTask implements TaskInterface {

	public function run(Context $context, Environment $environment, ConnectionInterface $connection = null) {
		if( ($connection->execute("if [ -d \"{$environment->remote->deployTo}\" ]; then echo 1; fi")) ) {

			$releases = $connection->execute("ls {$environment->remote->deployTo}");
			$releases = explode("\n", trim($releases));

			if( ($trim = count($releases) - $environment->remote->keepReleases) > 0 ) {
				$releases = array_slice($releases, 0, $trim);
				foreach( $releases as $release ) {
					$context->say("\tPruning {$release}...");
					$connection->execute("rm -Rf {$environment->remote->deployTo}/{$release}");
				}
			}
		}
	}
}