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
use phpseclib\Crypt\RSA;

class Connection {


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

			Console::getInstance()->text("Connecting to {$this->id}...");

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

			Console::getInstance()->bold()->text('OK')->nostyle()->lf();


		// Username and password based authentication
		} else {

			Console::getInstance()->text("Connecting to {$this->id}...");

			// Authenticate
			if( ($this->_connection->login($authentication->username, $authentication->password)) == false ) {
				throw new \Exception("Unable to authenticate with provided credentials");
			}

			Console::getInstance()->bold()->text('OK')->nostyle()->lf();
		}

		// no timeout - for LARGE repos
		$this->_connection->setTimeout(null);

	}

	public function isConnected() {
		return $this->_connection->isConnected();
	}

	public function execute($command, $logResponse = false) {

		Console::getInstance()->text("Executing ")->bold()->text($command)->nostyle()->text(" on remote server...");

		$response = $this->_connection->exec($command);

		if( $this->_connection->getExitStatus() ) {
			Console::getInstance()->color([SGR::COLOR_FG_WHITE_BRIGHT, SGR::COLOR_BG_RED])
				   ->text("FAILED!")->nostyle()->lf();
			throw new \Exception($response);
		}

		Console::getInstance()->bold()->text('OK')->nostyle()->lf();

		if( $logResponse ) {
			Console::getInstance()->color([SGR::COLOR_BG_WHITE, SGR::COLOR_FG_BLUE])->text(trim($response))->lf()->nostyle();
		}

		return $response;

	}

	public function close() {

		Console::getInstance()->text("Closing connection to {$this->id}...");
		$this->_connection->disconnect();
		\minion\Console::getInstance()->bold()->text('OK')->nostyle()->lf();

	}

}