<?php

namespace minion\Config;

class Server extends ConfigProperty
{
	public string $host;
	public int $port = 22;
	public array $strategy = [];

	public function __construct(array $server)
	{
		parent::__construct($server);
		$this->strategy = \array_map("trim", \explode(",", $server["strategy"]));
	}
}