<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 2:16 PM
 */

namespace minion\config;


class Server extends ConfigProperty {

	public $host = null;
	public $port = 22;
	public $migrate = false;

}