<?php

namespace minion\Connections;


use minion\Config\Authentication;
use minion\Config\Server;

class LocalConnection extends ConnectionAbstract
{
	public function execute($command){

	    if( $this->pwd() ){
	        $command = "cd {$this->pwd()}&&{$command}";
        }

		$response = system($command, $status);
		if( $status ) {
            throw new \Exception("Command failed: {$response}");
		}

		return $response;
	}
}