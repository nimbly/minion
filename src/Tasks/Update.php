<?php

namespace minion\Tasks;

use minion\Config\Environment;
use minion\Connections\ConnectionAbstract;

class Update extends TaskAbstract
{
	public function run(Environment $environment, ConnectionAbstract $connection): void
	{
		$connection->cwd($environment->remote->getCurrentRelease());

		$command = match($environment->code->scm) {
			"git" => "git reset HEAD&&git pull",
			"svn" => "svn up",
			default => throw new \Exception("Unsupported SCM")
		};

		$connection->execute($command);
	}
}