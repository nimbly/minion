<?php

namespace minion\Config;


use Symfony\Component\Yaml\Parser;

class Environment {

	/** @var string */
	public $name = null;

	/** @var Code */
	public $code = null;

	/** @var Remote */
	public $remote = null;

	/** @var Authentication */
	public $authentication = null;

	/** @var array  */
	public $preDeploy = [];

	/** @var array  */
	public $strategy = [];

	/** @var array */
	public $postDeploy = [];

	/** @var Server[] */
	public $servers = [];

    /**
     * Environment constructor.
     * @param $configFile
     * @param $environment
     * @throws \Exception
     */
    public function __construct($configFile, $environment) {

        if( !file_exists($configFile) ){
            throw new \Exception("Config file {$configFile} not found");
        }

        $config = (new Parser)->parse(file_get_contents($configFile));

        if( !isset($config['environments']) ||
            !is_array($config['environments']) ||
            !isset($config['environments'][$environment]) ){
            throw new \Exception("Undefined environment \"{$environment}\"");
        }

        $env = $config['environments'][$environment];

        $env['code'] = array_merge(
            isset($config['code']) ? $config['code'] : [],
            isset($env['code']) ? $env['code'] : []
        );

        $env['remote'] = array_merge(
            isset($config['remote']) ? $config['remote'] : [],
            isset($env['remote']) ? $env['remote'] : []
        );

        $env['authentication'] = array_merge(
            isset($config['authentication']) ? $config['authentication'] : [],
            isset($env['authentication']) ? $env['authentication'] : []
        );

		$this->name = $environment;
		$this->code = new Code($env['code']);
		$this->remote = new Remote($env['remote']);
		$this->authentication = new Authentication($env['authentication']);

		if( isset($env['preDeploy']) ){
            $this->preDeploy = array_map('trim', explode(',', $env['preDeploy']));
        } else {
		    $this->preDeploy = [];
        }

        if( isset($env['strategy']) ){
            $this->strategy = array_map('trim', explode(',', $env['strategy']));
        } else {
            $this->strategy = [];
        }

        if( isset($env['postDeploy']) ){
            $this->postDeploy = array_map('trim', explode(',', $env['postDeploy']));
        } else {
            $this->postDeploy = [];
        }

		if( isset($env['servers']) && is_array($env['servers']) ) {
			foreach( $env['servers'] as $server ) {

			    // Use the environment strategy as the default for every server
				if( !isset($server['strategy']) || empty($server['strategy']) ) {
					$server['strategy'] = $env['strategy'];
				}

				$this->servers[] = new Server($server);
			}
		}
	}
}