<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 2:10 PM
 */

namespace minion\Config;


class Config {

	/** @var Code */
	public $code = null;

	/** @var Remote */
	public $remote = null;

	/** @var Authentication */
	public $authentication = null;

	/** @var Environment[] */
	public $environments = [];

	/** @var Environment */
	public $environment;


	/**
	 * @param array $config
	 *
	 * @throws \Exception
	 */
	public function __construct(array $config) {

		// Create the root/default config objects
		$this->code = new Code(isset($config['code']) ? $config['code'] : []);
		$this->remote = new Remote(isset($config['remote']) ? $config['remote'] : []);
		$this->authentication = new Authentication(isset($config['authentication']) ? $config['authentication'] : []);

		if( isset($config['environments']) && is_array($config['environments']) ) {
			foreach( $config['environments'] as $name => $environment ) {

				$environment['code'] = array_merge(
					isset($config['code']) ? $config['code'] : [],
					isset($environment['code']) ? $environment['code'] : []
				);

				$environment['remote'] = array_merge(
					isset($config['remote']) ? $config['remote'] : [],
					isset($environment['remote']) ? $environment['remote'] : []
				);

				$environment['authentication'] = array_merge(
					isset($config['authentication']) ? $config['authentication'] : [],
					isset($environment['authentication']) ? $environment['authentication'] : []
				);

				$this->environments[$name] = new Environment($name, $environment);
			}
		}
	}


	/**
	 * @param $name
	 * @return bool|Environment
	 */
	public function setEnvironment($name) {

		foreach( $this->environments as $environment ) {
			if( $environment->name == $name ) {
			    $this->environment = $environment;
				return $this->environment;
			}
		}

		return false;
	}
}