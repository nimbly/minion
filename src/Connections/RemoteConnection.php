<?php

namespace minion\Connections;


use minion\Config\Authentication;
use minion\Config\Server;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

class RemoteConnection extends ConnectionAbstract  {

    /** @var string */
	private $id;

	/** @var SSH2 */
	private $connection;

	/**
	 * @param Server $server
	 * @param Authentication $authentication
	 *
	 * @throws \Exception
	 */
	public function __construct(Server $server = null, Authentication $authentication = null) {
		if( $server && $authentication ) {
			$this->connect($server, $authentication);
		}
	}

    /**
	 * @param Server $server
	 * @param Authentication $authentication
	 *
	 * @throws \Exception
	 */
	public function connect(Server $server, Authentication $authentication) {

		$this->id = "{$authentication->username}@{$server->host}:{$server->port}";

		$this->connection = new SSH2($server->host, $server->port);

		// Key based authentication
		if( $authentication->key ) {

			if( file_exists($authentication->key) === false ) {
				throw new \Exception("SSH key file {$authentication->key} not found.");
			}

			if( ($key = file_get_contents($authentication->key)) === false ) {
				throw new \Exception("Error while reading key file {$authentication->key}.");
			}

			// Check for DSA key -- this isn't the best way to do it, but better than nothing
			// PHPSECLIB loadKey does not return FALSE when loading a DSA key...
			if( preg_match('/DSA PRIVATE KEY/i', $key) ) {
				throw new \Exception("PHPSECLIB does not support DSA keys.");
			}

			// Load the key
			$rsa = new RSA;
			if( ($rsa->loadKey($key)) === false ) {
				throw new \Exception("Unknown or unsupported key type {$authentication->key}. Only RSA keys are supported.");
			}

			// Authenticate
			if( ($this->connection->login($authentication->username, $rsa)) == false ) {
				throw new \Exception("Unable to authenticate with provided credentials.");
			}

		// Username and password based authentication
		} else {

			// Authenticate
			if( ($this->connection->login($authentication->username, $authentication->password)) == false ) {
				throw new \Exception("Unable to authenticate with provided credentials");
			}

		}

		// no timeout - for LARGE repos
		$this->connection->setTimeout(null);
	}

	public function isConnected() {
		return $this->connection->isConnected();
	}

	public function execute($command) {
	    if( $this->currentDirectory ){
	        $command = "cd {$this->currentDirectory}&&{$command}";
        }

		$response = $this->connection->exec($command);
		if( $this->connection->getExitStatus() ) {
			throw new \Exception("Command failed: {$response}");
		    return false;
		}

		return $response;
	}

	public function close() {
		$this->connection->disconnect();
	}

}