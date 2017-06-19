<?php

namespace minion\Config;


class Code extends ConfigProperty {
	public $scm = 'git';
	public $repo;
	public $branch;
	public $commitHash;
	public $commitAuthor;

	public function setActiveCommit($hash)
    {
        $this->commitHash = $hash;
    }

    public function getActiveCommit()
    {
        return $this->commitHash;
    }

    public function setActiveCommitAuthor($name)
	{
		$this->commitAuthor = $name;
	}

	public function getActiveCommitAuthor()
	{
		return $this->commitAuthor;
	}
}