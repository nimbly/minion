<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 11/19/16
 * Time: 3:03 PM
 */

namespace minion\commands;


use minion\config\Context;
use minion\interfaces\CommandInterface;

class InitCommand implements CommandInterface
{

	public function run(Context $context)
	{

		if( !file_exists('commands') ){
			mkdir('commands');
		}

		if( !file_exists('tasks') ){
			mkdir('tasks');
		}

		if( !file_exists('minion.yml') ) {
			copy(__DIR__ . '/../src/templates/config.yml.tpl', 'minion.yml');
		}

	}

}