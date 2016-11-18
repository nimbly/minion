<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 3/13/16
 * Time: 7:48 PM
 */

namespace minion\interfaces;


use minion\config\Context;

interface CommandInterface {

	public function run(Context $context);

}