<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 4:51 PM
 */

namespace minion;


use minion\config\Context;
use minion\config\Environment;
use minion\interfaces\ConnectionInterface;
use minion\interfaces\TaskInterface;

class Task {

	/**
	 * @param $task
	 * @param Context $context
	 * @param Environment $environment
	 * @param ConnectionInterface|null $connection
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public static function run($task, Context $context, Environment $environment, ConnectionInterface $connection = null) {

		Console::getInstance()->green()->out("* Running <bold><white>".trim($task)."</white></bold> task");

		// Normalize task class name
		$task = ucfirst(strtolower(trim($task)));

		// What task are we running?
		$taskClass = "\\minion\\tasks\\{$task}Task";
		if( class_exists($taskClass) == false ) {
			throw new \Exception("Task {$taskClass} was not found.");
		}

		/** @var TaskInterface $task */
		$task = new $taskClass;

		// Check for run method
		if( method_exists($task, 'run') === false ) {
			throw new \Exception("Task {$task} run method does not exist");
		}

		return $task->run($context, $environment, $connection);
	}

}