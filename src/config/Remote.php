<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 1:55 PM
 */

namespace minion\config;


class Remote extends ConfigProperty {

	public $method = 'release';
	public $keepReleases = 5;
	public $path = '/var/www';
	public $deployPath = '/var/www';

	public function __construct(array $data) {
		parent::__construct($data);

		if( $this->method == 'release' ) {
			$this->deployPath = $this->path.'/current';
		} else {
			$this->deployPath = $this->path;
		}

	}

}