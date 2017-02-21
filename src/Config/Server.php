<?php

namespace minion\Config;


class Server extends ConfigProperty {

	public $host = null;
	public $port = 22;
	public $strategy = [];

	public function __construct(array $server) {
		parent::__construct($server);
		$this->strategy = array_map('trim', explode(',', $server['strategy']));
	}

}