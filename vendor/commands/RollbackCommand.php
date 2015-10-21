<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/6/15
 * Time: 5:39 PM
 */

namespace minion\commands;


use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use minion\config\Environment;
use minion\Connection;
use minion\Console;

class RollbackCommand {

	public function run(Environment $environment) {

		foreach( $environment->servers as $server ) {

			$connection = new Connection(array_merge($config['connection'], $server));

			$releases = $connection->execute("ls {$config['remote']['path']}/releases");

			$releases = explode("\n", trim($releases));

			if( count($releases) > 1 ) {

				$release = $releases[count($releases) - 2];
				$badRelease = $releases[count($releases) - 1];

				if( $release ) {
					$connection->execute("cd {$config['remote']['path']}&&rm -f current&&ln -s -r releases/{$release} current");
					$connection->execute("cd {$config['remote']['path']}/releases&&rm -Rf {$badRelease}");
				}
			}
			else {
				Console::getInstance()->color([SGR::COLOR_FG_WHITE_BRIGHT, SGR::COLOR_BG_RED])
					   ->text('No more releases to rollback. You\'re on the last one!')->lf()->nostyle();
			}
		}
	}

}