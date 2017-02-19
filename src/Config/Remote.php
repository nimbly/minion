<?php

namespace minion\Config;


class Remote extends ConfigProperty {

	public $keepReleases = 5;
	public $path = '/var/www';
	public $releaseDir = 'releases';
	public $symlink = 'current';

    protected $activeRelease;

    public function getReleases()
    {
        return "{$this->path}/{$this->releaseDir}";
    }

    public function getCurrentRelease()
    {
        return "{$this->path}/{$this->symlink}";
    }

    public function setActiveRelease($dirname)
    {
        $this->activeRelease = $dirname;
    }

    public function getActiveRelease()
    {
        return "{$this->getReleases()}/{$this->activeRelease}";
    }
}