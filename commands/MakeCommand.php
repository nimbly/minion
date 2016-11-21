<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 11/18/16
 * Time: 3:34 PM
 */

namespace minion\commands;


use minion\config\Context;
use minion\interfaces\CommandInterface;

class MakeCommand implements CommandInterface
{

	public function run(Context $context){

		if( ($type = $context->nextNamedArgument()) == null ){
			throw new \Exception('Missing the thing to make');
		}

		$name = $context->nextNamedArgument();

		switch( strtolower($type) ){

			case 'command':
				if( empty($name) ){
					throw new \Exception("Name required");
				}

				$name = ucfirst(strtolower($name));
				$filename = "{$name}Command.php";
				$path = "commands";

				if( file_exists($path) == false ){
					throw new \Exception('Commands directory not found. Have you run "minion init" yet?');
				}

				$context->say("Creating <bold>{$name}</bold> command");

				if( file_exists("{$path}/{$filename}") ){
					throw new \Exception("File \"{$path}/{$filename}\" already exists");
				}

				$command = file_get_contents(__DIR__.'/../src/templates/Command.php.tpl');
				$command = preg_replace('/\:CommandName/', "{$name}Command", $command);
				file_put_contents("{$path}/{$filename}", $command);
				break;

			case 'task':
				if( empty($name) ){
					throw new \Exception("Name required");
				}

				$name = ucfirst(strtolower($name));
				$filename = "{$name}Task.php";
				$path = "tasks";

				if( file_exists($path) == false ){
					throw new \Exception('Tasks directory not found. Have you run "minion init" yet?');
				}

				$context->say("Creating <bold>{$name}</bold> task");

				if( file_exists("{$path}/{$filename}") ){
					throw new \Exception("File \"{$path}/{$filename}\" already exists");
				}

				$command = file_get_contents(__DIR__.'/../src/templates/Task.php.tpl');
				$command = preg_replace('/\:TaskName/', "{$name}Task", $command);
				file_put_contents("{$path}/{$filename}", $command);

				break;

			case 'config':
				$context->say("Creating new default config file");

				if( !$name ) {
					$name = 'minion.yml';
				}

				if( file_exists($name) ){
					throw new \Exception("Config file \"{$name}\" already exists");
				}

				copy(__DIR__.'/../src/templates/config.yml.tpl', $name);
				break;

			default:
				throw new \Exception('Unsupported type');

		}

	}

}