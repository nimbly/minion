<?php

namespace minion\Tasks;


use minion\Config\Environment;
use minion\Connections\ConnectionAbstract;

class UpdateTask extends TaskAbstract {

	public function run(Environment $environment, ConnectionAbstract $connection = null) {

	    $connection->cwd($environment->remote->currentRelease);

		if( $environment->code->scm == 'git' ) {
			$connection->execute("git reset HEAD&&git pull");
		}

		elseif( $environment->code->scm == 'svn' ) {
			$connection->execute("svn up");
		}
	}

}