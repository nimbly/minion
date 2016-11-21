<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 2:10 PM
 */

namespace minion\config;


use minion\Console;
use Symfony\Component\Yaml\Parser;

class Context {

	/** @var string */
	public $command = null;

	/** @var array */
	public $arguments = [];

	/** @var array  */
	public $namedArguments = [];

	/** @var Config */
	public $config = null;

	/**
	 * @param array $arguments
	 *
	 * @throws \Exception
	 */
	public function __construct(array $arguments) {

		$script = array_shift($arguments);

		// Process the named arguments first
		for( $i = 0; $i < count($arguments); $i++ ){
			if( preg_match('/^[^\-]+/', $arguments[$i]) ){
				$this->namedArguments[] = $arguments[$i];
			}
			else {
				$i++;
			}
		}

		// convert the args into a NvP array
		$args = implode(' ', $arguments);

		// Process the remaining options
		if( preg_match_all('/\-\-?([a-zA-Z0-9]+)((\s?\=\s?)?([^\-]+))?/', $args, $matches, PREG_SET_ORDER) ) {
			foreach( $matches as $option ) {
				$this->arguments[$option[1]] = isset($option[4]) ? trim($option[4]) : ''; // keep it as empty string!
			}
		}

		if( ($this->command = array_shift($this->namedArguments)) === null ) {
			$this->command = 'help';
			echo "Usage: minion <command> [arguments]\n";
			exit;
		}

		// MAKE is the only command that does not require nor need a config file
		if( $this->command != 'init' ) {

			// Was there a config option passed?
			if( ($configFile = $this->getArgument(['config'])) === null ){
				$configFile = 'minion.yml';
			}

			// Check for file existence
			if( file_exists($configFile) == false ) {
				throw new \Exception("config file {$configFile} not found.");
			}

			// Try to read file
			if(( $configData = file_get_contents($configFile)) === false ) {
				throw new \Exception("config file {$configFile} not readable.");
			}

			// Try to parse the yml file (throws an exception if failed)
			$yamlParser = new Parser;

			// Create the config
			$this->config = new Config($yamlParser->parse($configData));
		}

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

	public function nextNamedArgument(){
		return array_shift($this->namedArguments);
	}

	public function say($message){
		Console::getInstance()->comment($message);
	}

	public function getConfig(){

	}

}