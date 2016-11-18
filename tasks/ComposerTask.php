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
use minion\Connection;
use minion\interfaces\TaskInterface;

class ComposerTask implements TaskInterface {

	public function run(Context $context, Environment $environment, Connection $connection = null){
		$connection->execute("cd {$context->config->remote->deployPath}&&composer install");
	}

}