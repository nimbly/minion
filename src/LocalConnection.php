<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 11/18/16
 * Time: 11:27 AM
 */

namespace minion;


use minion\config\Authentication;
use minion\config\Server;
use minion\interfaces\ConnectionInterface;

class LocalConnection implements ConnectionInterface
{

	public function __construct(Server $server = null, Authentication $authentication = null){}

	public function execute($command, $showCommand = false, $showResponse = false){

		if( $showCommand ){
			Console::getInstance()->inline("\tExecuting <bold>{$command}</bold> ");
		}

		$response = system($command, $status);

		if( $status ) {
			Console::getInstance()->backgroundRed()->brightWhite("FAILED!");
			throw new \Exception($response);
		}

		if( $showCommand ) {
			Console::getInstance()->bold()->out('OK');
		}

		if( $showResponse ) {
			Console::getInstance()->backgroundWhite()->blue(trim($response));
		}

		return $response;
	}

	public function close(){
		return true;
	}

	public function isConnected(){
		return true;
	}

}