<?php

namespace minion\Tasks;

use minion\Config\Environment;
use minion\Connections\ConnectionAbstract;

class Update extends TaskAbstract
{
	public function run(Environment $environment, ConnectionAbstract $connection): void
	{
		$connection->cwd($environment->remote->getCurrentRelease());

		switch( $environment->code->scm ) {
			case "git":
				$command = "git reset HEAD&&git pull";
				break;

			case "svn":
				$command = "svn up";
				break;

			default:
				throw new \Exception("Unsupported SCM");
		}

		$connection->execute($command);
	}
}