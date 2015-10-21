<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 2:07 PM
 */

namespace minion\config;


class Code extends ConfigProperty {

	public $scm = 'git';
	public $repo = null;
	public $branch = null;
	public $username = null;
	public $password = null;

}