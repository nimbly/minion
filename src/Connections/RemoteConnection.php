<?php

namespace minion\Connections;


use minion\Config\Authentication;
use minion\Config\Server;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

class RemoteConnection extends ConnectionAbstract
{
	private SSH2 $connection;

	/**
	 * @param Server $server
	 * @param Authentication $authentication
	 *
	 * @throws \Exception
	 */
	public function __construct(Server $server, Authentication $authentication)
	{
		$this->connection = $this->connect($server, $authentication);
		$this->currentDirectory = $this->execute("pwd");
	}

	/**
	 * @param Server $server
	 * @param Authentication $authentication
	 * @throws \Exception
	 */
	protected function connect(Server $server, Authentication $authentication): SSH2
	{
		$connection = new SSH2($server->host, $server->port);

		// Key based authentication
		if( $authentication->key ) {

			if( file_exists($authentication->key) === false ) {
				throw new \Exception("SSH key file {$authentication->key} not found.");
			}

			if( ($key = file_get_contents($authentication->key)) === false ) {
				throw new \Exception("Error while reading key file {$authentication->key}.");
			}

			// Check for DSA key -- this isn't the best way to do it, but better than nothing.
			// PHPSECLIB loadKey does not return FALSE when loading a DSA key.
			if( preg_match('/DSA PRIVATE KEY/i', $key) ) {
				throw new \Exception("PHPSECLIB does not support DSA keys.");
			}

			// Load the key
			$rsa = new RSA;

			// Key requires passphrase
			if( $authentication->passphrase ){
				$rsa->setPassword($authentication->passphrase);
			}

			// Load the key
			if( ($rsa->loadKey($key)) === false ) {
				throw new \Exception("Unknown or unsupported key type {$authentication->key}. Only RSA keys are supported.");
			}

			// Authenticate/login
			if( ($connection->login($authentication->username, $rsa)) == false ) {
				throw new \Exception("Unable to authenticate with provided credentials.");
			}

		// Username and password based authentication
		} else {

			// Authenticate
			if( ($connection->login($authentication->username, $authentication->password)) == false ) {
				throw new \Exception("Unable to authenticate with provided credentials");
			}

		}

		// no timeout - for LARGE repos
		$connection->setTimeout(null);

		return $connection;
	}

	/**
	 * Command to execute
	 *
	 * @param string $command
	 * @return string
	 * @throws \Exception
	 */
	public function execute(string $command): string
	{
		if( $this->pwd() ){
			$command = "cd {$this->pwd()}&&{$command}";
		}

		$response = $this->connection->exec($command);
		if( $this->connection->getExitStatus() ) {
			throw new \Exception("Command failed: {$response}");
		}

		return $response;
	}

	/**
	 * Close the SSH connection
	 */
	public function close(): void
	{
		$this->connection->disconnect();
	}
}