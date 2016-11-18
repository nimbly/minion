<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 2:10 PM
 */

namespace minion\config;


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
	public $strategy = [];

	/** @var Server[] */
	public $servers = [];

	public function __construct($name, $environment) {

		$this->name = $name;

		$this->code = new Code($environment['code']);
		$this->remote = new Remote($environment['remote']);
		$this->authentication = new Authentication($environment['authentication']);
		$this->strategy = isset($environment['strategy']) ? explode(',', $environment['strategy']) : [];

		if( isset($environment['servers']) && is_array($environment['servers']) ) {
			foreach( $environment['servers'] as $server ) {

				if( !isset($server['strategy']) || empty($server['strategy']) ) {
					$server['strategy'] = $environment['strategy'];
				}

				$this->servers[] = new Server($server);

			}
		}
	}
}