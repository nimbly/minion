<?php

namespace minion\Connections;

class LocalConnection extends ConnectionAbstract
{
	public function execute(string $command): ?string
	{
		if( $this->pwd() ){
			$command = "cd {$this->pwd()}&&{$command}";
		}

		$response = \system($command, $status);

		if( $status ) {
			throw new \Exception("Command failed: {$response}");
		}

		return $response;
	}
}