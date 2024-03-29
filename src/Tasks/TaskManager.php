<?php

namespace minion\Tasks;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaskManager
{
	/**
	 * @param string $task
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return TaskAbstract
	 * @throws \Exception
	 */
	public static function create(string $task, InputInterface $input, OutputInterface $output): TaskAbstract
	{
		// Normalize task class name
		$task = \ucfirst(\strtolower($task));

		$taskClass = "\\minion\\Tasks\\{$task}";

		if( \class_exists($taskClass) === false ) {
			throw new \Exception("Task {$taskClass} was not found.");
		}

		/** @var TaskAbstract $task */
		$task = new $taskClass($input, $output);
		return $task;
	}
}