<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 7:34 PM
 */

namespace minion\tasks;


use minion\config\Environment;
use minion\Connection;
use minion\interfaces\TaskInterface;

class MigrateTask implements TaskInterface {

	public function run(Environment $environment, Connection $connection = null) {

		$connection->execute("cd {$environment->remote->path}/current/migrations&&./phinx migrate -e {$environment->environment}", true);

	}

}