<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/6/15
 * Time: 4:54 PM
 */

namespace minion\tasks;


use minion\config\Environment;
use minion\Connection;
use minion\interfaces\TaskInterface;

class PermissionsTask implements TaskInterface {

	public function run(Environment $environment, Connection $connection = null) {

		if( $environment->remote->method == 'update' ) {
			$path = "{$environment->remote->path}/app/resources";
		} else {
			$path = "{$environment->remote->path}/current/app/resources";
		}

		$connection->execute("sudo chmod g+w {$path} -R");
		$connection->execute("sudo chgrp apache {$path} -R");

	}

}