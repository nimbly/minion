<?php

namespace minion\Connections;


use minion\Config\Authentication;
use minion\Config\Server;

class LocalConnection extends ConnectionAbstract
{
	public function execute($command){

	    if( $this->currentDirectory ){
	        $command = "cd {$this->currentDirectory}&&{$command}";
        }

		$response = system($command, $status);

		if( $status ) {
            return false;
		}

		return $response;
	}
}