<?php

namespace minion\Config;

class Code extends ConfigProperty
{
	public string $scm = 'git';
	public string $repo;
	public string $branch;
	public ?string $commitHash;
	public ?string $commitAuthor;

	public function setActiveCommit(string $hash): void
	{
		$this->commitHash = $hash;
	}

	public function getActiveCommit(): ?string
	{
		return $this->commitHash;
	}

	public function setActiveCommitAuthor(string $name): void
	{
		$this->commitAuthor = $name;
	}

	public function getActiveCommitAuthor(): ?string
	{
		return $this->commitAuthor;
	}
}