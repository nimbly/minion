<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 5:59 PM
 */

namespace minion\tasks;


use minion\config\Environment;
use minion\Connection;
use minion\interfaces\TaskInterface;

class UpdateTask implements TaskInterface {

	public function run(Environment $environment, Connection $connection = null) {

		// UPDATE method
		if( $environment->remote->method == 'update' ) {
			$path = $environment->remote->path;

		// RELEASES method
		} else {
			$path = "{$environment->remote->path}/current";
		}

		// What SCM?
		if( $environment->code->scm == 'git' ) {
			$connection->execute("cd {$path}&&git reset HEAD&&git pull", true);
		}

		elseif( $environment->code->scm == 'svn' ) {
			$connection->execute("cd {$path}&&svn up", true);
		}

	}

}