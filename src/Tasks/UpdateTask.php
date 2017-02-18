<?php

namespace minion\Tasks;


use minion\Config\Config;
use minion\Connections\ConnectionAbstract;

class UpdateTask extends TaskAbstract {

	public function run(Config $config, ConnectionAbstract $connection = null) {

	    $connection->cwd($config->environment->remote->currentRelease);

		if( $config->environment->code->scm == 'git' ) {
			$connection->execute("git reset HEAD&&git pull");
		}

		elseif( $config->environment->code->scm == 'svn' ) {
			$connection->execute("svn up");
		}
	}

}