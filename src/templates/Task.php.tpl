<?php

namespace minion\tasks;

use minion\config\Context;
use minion\config\Environment;
use minion\interfaces\ConnectionInterface;
use minion\interfaces\TaskInterface;

class :TaskName implements TaskInterface
{

	public function run(Context $context, Environment $environment, ConnectionInterface $connection = null)
	{
		// TODO: Implement run() method.
	}

}