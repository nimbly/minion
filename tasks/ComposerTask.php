<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 11/18/16
 * Time: 9:02 AM
 */

namespace minion\tasks;


use minion\config\Context;
use minion\config\Environment;
use minion\interfaces\ConnectionInterface;
use minion\interfaces\TaskInterface;

class ComposerTask implements TaskInterface {

	public function run(Context $context, Environment $environment, ConnectionInterface $connection = null){
		$connection->execute("cd {$context->config->remote->currentRelease}&&composer install");
	}

}