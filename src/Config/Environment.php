<?php

namespace minion\Config;


use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
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

	/** @var array  */
	protected $paramBag = [];

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

    /**
     * Set a custom parameter
     *
     * @param $name
     * @param $value
     */
	public function set($name, $value)
    {
        $this->paramBag[$name] = $value;
    }

    /**
     * Get a custom parameter
     *
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        if( array_key_exists($name, $this->paramBag) ){
            return $this->paramBag[$name];
        }

        return null;
    }

    public function defaultProgressBar(OutputInterface $output, $max = null)
    {
        $progressBar = new ProgressBar($output, $max);
        $progressBar->setFormatDefinition('custom', " %current%/%max% [%bar%] %percent:3s%% / %message%");
        $progressBar->setEmptyBarCharacter('░'); // light shade character \u2591
        $progressBar->setProgressCharacter('');
        $progressBar->setBarCharacter('▓'); // dark shade character \u2593
        $progressBar->setFormat('custom');
        return $progressBar;
    }
}