<?php

namespace minion\Config;

class Server extends ConfigProperty
{
	public string $host = "";
	public int $port = 22;
	public array $strategy = [];

	public function __construct(array $server)
	{
		if( isset($server["strategy"]) && \is_string($server["strategy"]) ){
			$server["strategy"] = \array_map("trim", \explode(",", $server["strategy"]));
		}

		parent::__construct($server);
	}
}