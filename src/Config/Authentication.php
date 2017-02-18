<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 1:57 PM
 */

namespace minion\Config;


class Authentication extends ConfigProperty {

	public $username = 'user';
	public $password = '';
	public $key = null;
	public $passphrase = null;

}