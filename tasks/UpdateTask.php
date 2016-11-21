<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 5:59 PM
 */

namespace minion\tasks;


use minion\config\Context;
use minion\config\Environment;
use minion\interfaces\ConnectionInterface;
use minion\interfaces\TaskInterface;

class UpdateTask implements TaskInterface {

	public function run(Context $context, Environment $environment, ConnectionInterface $connection = null) {

		// What SCM?
		if( $environment->code->scm == 'git' ) {
			$connection->execute("cd {$environment->remote->currentRelease}&&git reset HEAD&&git pull");
		}

		elseif( $environment->code->scm == 'svn' ) {
			$connection->execute("cd {$environment->remote->currentRelease}&&svn up");
		}

	}

}