<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 2:10 PM
 */

namespace minion\config;


use minion\Console;
use minion\factories\CommandFactory;
use Symfony\Component\Yaml\Parser;

class Environment {

	/** @var string */
	public $command = 'Help';

	/** @var string */
	public $action = null;

	/** @var string */
	public $environment = null;

	/** @var array */
	public $arguments = array();

	/** @var \minion\config\Code */
	public $code = null;

	/** @var \minion\config\Remote */
	public $remote = null;

	/** @var \minion\config\Authentication */
	public $authentication = null;

	/** @var \minion\config\Server[] */
	public $servers = [];


	/**
	 * @param array $arguments
	 * @param string $file
	 *
	 * @throws \Exception
	 */
	public function __construct($arguments, $file) {

		// Does this command have an action?
		if( isset($arguments[1]) ) {
			if( preg_match('/^(\w+)\:(\w+)$/', $arguments[1], $match) ) {
				$this->command = $match[1];
				$this->action = $match[2];
			} else {
				$this->command = $arguments[1];
			}
		}

		// Pull out environment option
		$this->environment = isset($arguments[2]) ? $arguments[2] : null;

		// Pull out all other arguments
		$this->arguments = array_slice($arguments, 3);

		// Load config data
		$config = $this->_loadConfig($file);

		// Do we have a config for this environment?
		if( $this->environment &&
			(array_key_exists('environments', $config) === false ||
			array_key_exists($this->environment, $config['environments']) == false) ) {
			throw new \Exception("No configuration for environment \"{$this->environment}\" was found.");
		}

		// Shortcut to environment config data (this will get merged into the default config data)
		$environmentConfig = isset($config['environments'][$this->environment]) ? $config['environments'][$this->environment] : [];

		// Create the config objects
		$this->authentication = new Authentication(array_merge((isset($config['authentication']) ? $config['authentication'] : []), (isset($environmentConfig['authentication']) ? $environmentConfig['authentication'] : [])));
		$this->code = new Code((isset($config['code']) ? $config['code'] : []), (isset($environmentConfig['code']) ? $environmentConfig['code'] : []));
		$this->remote = new Remote((isset($config['remote']) ? $config['remote'] : []), (isset($environmentConfig['remote']) ? $environmentConfig['remote'] : []));

		if( isset($environmentConfig['servers']) ) {
			foreach( $environmentConfig['servers'] as $server ) {
				$this->servers[] = new Server($server);
			}
		}
	}

	/**
	 * @param $file
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	private function _loadConfig($file) {
		Console::getInstance()->text("Loading configuration file {$file}...");

		// Does file exist?
		if( file_exists($file) === false ) {
			throw new \Exception("file not found");
		}

		// Try to read file
		if( ($configData = file_get_contents($file)) === false ) {
			throw new \Exception('file not readable.');
		}

		// Try to parse the yml file (throws an exception if failed)
		$yamlParser = new Parser;
		$config = $yamlParser->parse($configData);

		Console::getInstance()->bold()->text('OK')->nostyle()->lf();

		return $config;
	}

}