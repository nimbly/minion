<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 1:55 PM
 */

namespace minion\Config;


class Remote extends ConfigProperty {

	public $method = 'release';
	public $keepReleases = 5;
	public $path = '/var/www';
	public $releaseDir = 'releases';
	public $symlink = 'current';

	// These are generated at construction time
	public $deployTo = null;
	public $currentRelease = null;
    public $release = null;

	public function __construct(array $data) {
		parent::__construct($data);

		if( $this->method == 'release' ) {
			$this->deployTo = $this->path.'/'.$this->releaseDir;
			$this->currentRelease = $this->path.'/'.$this->symlink;
		} else {
			$this->deployTo = $this->path;
			$this->currentRelease = $this->path;
		}
	}

}