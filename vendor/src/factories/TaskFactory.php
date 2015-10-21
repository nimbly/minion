<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 4:51 PM
 */

namespace minion\factories;


use minion\config\Environment;
use minion\Connection;
use minion\Console;

class TaskFactory {

	/**
	 * @param $task
	 * @param Environment $environment
	 * @param Connection|null $connection
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public static function run($task, Environment $environment, Connection $connection = null) {

		Console::getInstance()->text("\nRunning ")->bold()->text($task)->nostyle()->text(' task')->lf();

		// What task are we running?
		$taskClass = "\\minion\\tasks\\{$task}Task";
		if( class_exists($taskClass) == false ) {
			throw new \Exception("Task {$task} was not found.");
		}

		$task = new $taskClass;

		// Check for run method
		if( method_exists($task, 'run') === false ) {
			throw new \Exception("Task {$task} run method does not exist");
		}

		return $task->run($environment, $connection);
	}

}