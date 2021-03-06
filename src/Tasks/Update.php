<?php

namespace minion\Tasks;


use minion\Config\Environment;
use minion\Connections\ConnectionAbstract;

class Update extends TaskAbstract {

	public function run(Environment $environment, ConnectionAbstract $connection) {

	    $connection->cwd($environment->remote->getCurrentRelease());

		if( $environment->code->scm == 'git' ) {
			$connection->execute("git reset HEAD&&git pull");
		}

		elseif( $environment->code->scm == 'svn' ) {
			$connection->execute("svn up");
		}
	}

}