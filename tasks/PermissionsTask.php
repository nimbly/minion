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
use minion\interfaces\ConnectionInterface;
use minion\interfaces\TaskInterface;

class PermissionsTask implements TaskInterface {

	public function run(Context $context, Environment $environment, ConnectionInterface $connection = null) {

		$currentRelease = $environment->remote->currentRelease;

		// Make directories
		$context->say("\tCreating required directories...");
		$connection->execute("mkdir -p {$currentRelease}/bootstrap/cache");
		$connection->execute("mkdir -p {$currentRelease}/storage/app/public");
		$connection->execute("mkdir -p {$currentRelease}/storage/framework/cache");
		$connection->execute("mkdir -p {$currentRelease}/storage/framework/sessions");
		$connection->execute("mkdir -p {$currentRelease}/storage/framework/views");
		$connection->execute("mkdir -p {$currentRelease}/storage/logs");

		// Change ownership
		$context->say("\tChanging ownership....");
		$connection->execute("sudo chown fedora.apache {$currentRelease}/storage -R");
		$connection->execute("sudo chown fedora.apache {$currentRelease}/bootstrap/cache -R");

		// Change permissions
		$context->say("\tChanging permissions...");
		$connection->execute("sudo chmod 770 {$currentRelease}/storage -R");
		$connection->execute("sudo chmod 770 {$currentRelease}/bootstrap/cache -R");

		// Link up the environment
		$context->say("\tLinking environment config...");
		$connection->execute("cd {$currentRelease}&&ln -s .env.{$environment->name} .env");

	}

}