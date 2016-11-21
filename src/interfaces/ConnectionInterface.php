<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 11/18/16
 * Time: 11:24 AM
 */

namespace minion\interfaces;


use minion\config\Authentication;
use minion\config\Server;

interface ConnectionInterface
{

	public function __construct(Server $server = null, Authentication $authentication = null);
	public function execute($command, $showCommands = false, $showResponse = false);
	public function close();
	public function isConnected();

}