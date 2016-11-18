<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 2:10 PM
 */

namespace minion\config;


use Symfony\Component\Yaml\Parser;

class Context {

	/** @var string */
	public $command = null;

	/** @var array */
	public $arguments = [];

	/** @var Config */
	public $config = null;

	/**
	 * @param array $arguments
	 * @param string $configFile
	 *
	 * @throws \Exception
	 */
	public function __construct($arguments, $configFile) {

		if( !isset($arguments[1]) ) {
			echo "Missing command.";
			exit;
		}

		// First argument passed in is command
		$this->command = $arguments[1];

		// convert the args into a NvP array
		$args = implode(' ', array_slice($arguments, 2));

		// Process the remaining arguments
		if( preg_match_all('/\-\-?([a-zA-Z0-9]+)((\s?\=\s?)?([^\-]+))?/', $args, $matches, PREG_SET_ORDER) ) {
			foreach( $matches as $option ) {
				$this->arguments[$option[1]] = isset($option[4]) ? trim($option[4]) : ''; // keep it as empty string!
			}
		}

		// Does file exist?
		if( file_exists($configFile) === false ) {
			throw new \Exception("config file {$configFile} not found.");
		}

		// Try to read file
		if( ($configData = file_get_contents($configFile)) === false ) {
			throw new \Exception("config file {$configFile} not readable.");
		}

		// Try to parse the yml file (throws an exception if failed)
		$yamlParser = new Parser;

		// Create the config
		$this->config = new Config($yamlParser->parse($configData));

	}

	/**
	 * @param $arg
	 * @return null|string
	 */
	public function getArgument($arg) {

		if( is_array($arg) ) {
			foreach( $this->arguments as $argument => $value ) {
				if( in_array($argument, $arg) ) {
					return $value;
				}
			}
		}

		elseif( isset($this->arguments[$arg]) ) {
			return $this->arguments[$arg];
		}

		return null;
	}


}