<?php

namespace minion\Tasks;

use minion\Config\Environment;
use minion\Connections\ConnectionAbstract;

class Symlink extends TaskAbstract
{
	public function run(Environment $environment, ConnectionAbstract $connection): void
	{
		if( empty($environment->remote->getActiveRelease()) ){
			throw new \Exception("No active release in progress");
		}

		// Remove old symlink
		$connection->execute("rm -f {$environment->remote->getCurrentRelease()}");

		// Create new symlink
		$connection->execute("cd {$environment->remote->path}&&ln -s {$environment->remote->getActiveRelease()} {$environment->remote->symlink}");
	}
}