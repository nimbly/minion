<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/6/15
 * Time: 7:20 AM
 */

namespace minion\interfaces;


use minion\config\Environment;
use minion\Connection;

interface TaskInterface {

	public function run(Environment $environment, Connection $connection = null);

}