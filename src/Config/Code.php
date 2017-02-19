<?php

namespace minion\Config;


class Code extends ConfigProperty {

	public $scm = 'git';
	public $repo;
	public $branch;
	public $username;
	public $password;

}