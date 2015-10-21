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

}