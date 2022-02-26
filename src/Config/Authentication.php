<?php

namespace minion\Config;

class Authentication extends ConfigProperty
{
	public string $username = "user";
	public ?string $password;
	public ?string $key;
	public ?string $passphrase;
}