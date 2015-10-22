<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/22/15
 * Time: 9:09 AM
 */

namespace minion;

use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use minion\config\Environment;
use minion\factories\CommandFactory;

class App {

	public function run(array $arguments) {

		$version = file_get_contents('VERSION');

		Console::getInstance()->bold()->text("MINION {$version}")->lf()->normal();

		$startTime = microtime(true);

		// @todo allow this to be a command line option
		$configFile = 'minion.yml';

		// Build the environment object
		try {

			$environment = new Environment($arguments, $configFile);
			CommandFactory::run($environment);

		} catch( \Exception $e ) {

			Console::getInstance()->color([SGR::COLOR_FG_WHITE_BRIGHT, SGR::COLOR_BG_RED])
				   ->text("\n[ERROR] ".$e->getMessage())->lf()->nostyle()->bold()->text('MINION exiting.')->nostyle()->lf();

			exit();
		}

		Console::getInstance()->lf()->bold()->text("MINION done.")->normal()->text(' time='.round(microtime(true) - $startTime, 3).'s')->lf();

	}

}