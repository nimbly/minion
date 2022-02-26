<?php

namespace minion\Config;

class Remote extends ConfigProperty
{
	public int $keepReleases = 5;
	public string $path = "/var/www";
	public string $releaseDir = "releases";
	public string $symlink = "current";
	protected ?string $activeRelease;

	public function getReleases(): string
	{
		return "{$this->path}/{$this->releaseDir}";
	}

	public function getCurrentRelease(): string
	{
		return "{$this->path}/{$this->symlink}";
	}

	public function setActiveRelease(string $dirname): void
	{
		$this->activeRelease = $dirname;
	}

	public function getActiveRelease(): string
	{
		return "{$this->getReleases()}/{$this->activeRelease}";
	}
}