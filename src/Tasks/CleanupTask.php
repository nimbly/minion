<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/6/15
 * Time: 4:56 PM
 */

namespace minion\Tasks;


use minion\Config\Config;
use minion\Connections\ConnectionAbstract;

class CleanupTask extends TaskAbstract {

	public function run(Config $config, ConnectionAbstract $connection = null) {
		if( ($connection->execute("if [ -d \"{$config->environment->remote->deployTo}\" ]; then echo 1; fi")) ) {

			$releases = $connection->execute("ls {$config->environment->remote->deployTo}");
			$releases = explode("\n", trim($releases));

			if( ($trim = count($releases) - $config->environment->remote->keepReleases) > 0 ) {
				$releases = array_slice($releases, 0, $trim);

				$this->output->writeln("\t<info>Pruning old releases</info>");
				foreach( $releases as $release ) {
					$connection->execute("rm -Rf {$config->environment->remote->deployTo}/{$release}");
				}
			}
		}
	}
}