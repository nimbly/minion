<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/6/15
 * Time: 4:54 PM
 */

namespace minion\tasks;


use minion\config\Context;
use minion\config\Environment;
use minion\Connection;
use minion\interfaces\TaskInterface;

class PermissionsTask implements TaskInterface {

	public function run(Context $context, Environment $environment, Connection $connection = null) {

		// Make directories
		$connection->execute("mkdir -p {$environment->remote->deployPath}/bootstrap/cache");
		$connection->execute("mkdir -p {$environment->remote->deployPath}/storage/app/public");
		$connection->execute("mkdir -p {$environment->remote->deployPath}/storage/framework/cache");
		$connection->execute("mkdir -p {$environment->remote->deployPath}/storage/framework/sessions");
		$connection->execute("mkdir -p {$environment->remote->deployPath}/storage/framework/views");
		$connection->execute("mkdir -p {$environment->remote->deployPath}/storage/logs");

		// Change ownership
		$connection->execute("sudo chown fedora.apache {$environment->remote->deployPath}/storage -R");
		$connection->execute("sudo chown fedora.apache {$environment->remote->deployPath}/bootstrap/cache -R");

		// Change permissions
		$connection->execute("sudo chmod 770 {$environment->remote->deployPath}/storage -R");
		$connection->execute("sudo chmod 770 {$environment->remote->deployPath}/bootstrap/cache -R");

		// Link up the environment
		$connection->execute("cd {$environment->remote->deployPath}&&ln -s .env.{$environment->name} .env");

	}

}