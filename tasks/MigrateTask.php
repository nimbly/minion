<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 7:34 PM
 */

namespace minion\tasks;


use minion\config\Context;
use minion\config\Environment;
use minion\interfaces\ConnectionInterface;
use minion\interfaces\TaskInterface;

class MigrateTask implements TaskInterface {

	public function run(Context $context, Environment $environment, ConnectionInterface $connection = null) {
		$connection->execute("cd {$environment->remote->currentRelease}/migrations&&./phinx migrate -e {$environment->name}", true);
	}

}