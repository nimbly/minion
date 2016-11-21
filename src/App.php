<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/22/15
 * Time: 9:09 AM
 */

namespace minion;

use minion\config\Context;

class App {

	/** @var string */
	protected $version = null;

	/** @var Context */
	protected $context;

	public function run(array $arguments) {

		// read version from command line
		$this->version = file_get_contents(__DIR__.'/../VERSION');

		$startTime = microtime(true);

		// Build the environment object. This object gets passed all the way through the stack.
		try {

			Console::getInstance()->backgroundDarkGray()->brightBlue("# minion // v{$this->version} #");

			$this->context = new Context($arguments);
			Command::run($this->context);

		} catch( \Exception $e ) {
			Console::getInstance()->backgroundRed()->white("[ERROR] {$e->getMessage()}");
			//echo("[ERROR] {$e->getMessage()}\n");
		}

		Console::getInstance()->backgroundDarkGray()->brightBlue("# minion completed #");
		//Console::getInstance()->lightGray('time='.round(microtime(true) - $startTime, 3).'s');

	}

}