<?php

namespace minion\Config;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class Environment
{
	public string $name;
	public Code $code;
	public Remote $remote;
	public Authentication $authentication;

	/** @var array  */
	public array $preDeploy = [];

	/** @var array  */
	public array $strategy = [];

	/** @var array */
	public array $postDeploy = [];

	/** @var array<Server> */
	public array $servers = [];

	/** @var array<string,mixed>  */
	protected array $paramBag = [];

	/**
	 * Environment constructor.
	 * @param string $file
	 * @param string $environment
	 * @throws \Exception
	 */
	public function __construct(string $file, string $environment) {

		if( !\file_exists($file) ){
			throw new \Exception("Config file {$file} not found");
		}

		/**
		 * @var array<string,mixed> $config
		 */
		$config = (new Parser)->parse(\file_get_contents($file));

		if( !isset($config["environments"]) ||
			!\is_array($config["environments"]) ||
			!isset($config["environments"][$environment]) ){
			throw new \Exception("Undefined environment \"{$environment}\".");
		}

		$env = $config["environments"][$environment];

		$env["code"] = \array_merge(
			$config["code"] ?? [],
			$env["code"] ?? []
		);

		$env["remote"] = \array_merge(
			$config["remote"] ?? [],
			$env["remote"] ?? []
		);

		$env["authentication"] = \array_merge(
			$config["authentication"] ?? [],
			$env["authentication"] ?? []
		);

		$this->name = $environment;
		$this->code = new Code($env["code"]);
		$this->remote = new Remote($env["remote"]);
		$this->authentication = new Authentication($env["authentication"]);

		if( isset($env["preDeploy"]) ){
			$this->preDeploy = \array_map("trim", explode(",", $env["preDeploy"]));
		} else {
			$this->preDeploy = [];
		}

		if( isset($env["strategy"]) ){
			$this->strategy = \array_map("trim", \explode(",", $env["strategy"]));
		} else {
			$this->strategy = [];
		}

		if( isset($env["postDeploy"]) ){
			$this->postDeploy = \array_map("trim", \explode(",", $env["postDeploy"]));
		} else {
			$this->postDeploy = [];
		}

		if( isset($env["servers"]) && \is_array($env["servers"]) ) {
			foreach( $env["servers"] as $server ) {

				// Use the environment strategy as the default for every server
				if( !isset($server["strategy"]) || empty($server["strategy"]) ) {
					$server["strategy"] = $env["strategy"];
				}

				$this->servers[] = new Server($server);
			}
		}
	}

	/**
	 * Set a custom parameter
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function set(string $name, $value): void
	{
		$this->paramBag[$name] = $value;
	}

	/**
	 * Get a custom parameter
	 *
	 * @param string $name
	 * @return mixed|null
	 */
	public function get(string $name): mixed
	{
		return $this->paramBag[$name] ?? null;
	}

	public function defaultProgressBar(OutputInterface $output, int $max = 0): ProgressBar
	{
		$progressBar = new ProgressBar($output, $max);
		$progressBar->setFormatDefinition("custom", " %current%/%max% [%bar%] %percent:3s%% / %message%");
		$progressBar->setEmptyBarCharacter("░"); // light shade character \u2591
		$progressBar->setProgressCharacter("");
		$progressBar->setBarCharacter("▓"); // dark shade character \u2593
		$progressBar->setFormat("custom");
		return $progressBar;
	}
}