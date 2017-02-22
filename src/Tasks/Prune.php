<?php

namespace minion\Tasks;


use minion\Config\Environment;
use minion\Connections\ConnectionAbstract;

class Prune extends TaskAbstract {

	public function run(Environment $environment, ConnectionAbstract $connection) {
		if( ($connection->execute("if [ -d \"{$environment->remote->getReleases()}\" ]; then echo 1; fi")) ) {

			$releases = $connection->execute("ls {$environment->remote->getReleases()}");
			$releases = explode("\n", trim($releases));

			if( ($trim = count($releases) - $environment->remote->keepReleases) > 0 ) {
				$releases = array_slice($releases, 0, $trim);

				foreach( $releases as $release ) {
					$connection->execute("rm -Rf {$environment->remote->getReleases()}/{$release}");
				}
			}
		}
	}
}