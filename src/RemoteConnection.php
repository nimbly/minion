<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/4/15
 * Time: 9:07 PM
 */

namespace minion;


use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use minion\config\Authentication;
use minion\config\Server;
use minion\interfaces\ConnectionInterface;
use phpseclib\Crypt\RSA;

class RemoteConnection implements ConnectionInterface  {


	private $id = null;

	/** @var $_connection \phpseclib\Net\SSH2 */
	private $_connection = null;


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

		$this->_connection = new \phpseclib\Net\SSH2($server->host, $server->port);

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
			if( ($this->_connection->login($authentication->username, $rsa)) == false ) {
				throw new \Exception("Unable to authenticate with provided credentials.");
			}

		// Username and password based authentication
		} else {

			// Authenticate
			if( ($this->_connection->login($authentication->username, $authentication->password)) == false ) {
				throw new \Exception("Unable to authenticate with provided credentials");
			}

		}

		// no timeout - for LARGE repos
		$this->_connection->setTimeout(null);

	}

	public function isConnected() {
		return $this->_connection->isConnected();
	}

	public function execute($command, $showCommand = false, $showResponse = false) {

		if( $showCommand ){
			Console::getInstance()->inline("\tExecuting <bold>{$command}</bold> ");
		}

		$response = $this->_connection->exec($command);

		if( $this->_connection->getExitStatus() ) {
			Console::getInstance()->backgroundRed()->brightWhite("FAILED!");
			throw new \Exception($response);
		}

		if( $showCommand ) {
			Console::getInstance()->bold('OK');
		}


		if( $showResponse ) {
			Console::getInstance()->backgroundWhite()->blue(trim($response));
		}

		return $response;
	}

	public function close() {
		$this->_connection->disconnect();
	}

}