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

		// @todo allow this to be a command line option
		$configFile = 'minion.yml';

		// Build the environment object. This object gets passed all the way through the stack.
		try {

			$this->context = new Context($arguments, $configFile);
			Command::run($this->context);

		} catch( \Exception $e ) {

			echo("[ERROR] {$e->getMessage()}\n");

		}

		//Console::getInstance()->lf()->bold()->text("MINION done.")->normal()->text(' time='.round(microtime(true) - $startTime, 3).'s')->lf();

	}

}