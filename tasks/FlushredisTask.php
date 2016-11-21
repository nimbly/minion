<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 6/17/16
 * Time: 1:45 PM
 */

namespace minion\tasks;


use minion\config\Context;
use minion\config\Environment;
use minion\interfaces\ConnectionInterface;
use minion\interfaces\TaskInterface;

class FlushredisTask implements TaskInterface {

	public function run(Context $context, Environment $environment, ConnectionInterface $connection = null) {
		$connection->execute("sudo redis-cli flushdb");
	}
}