<?php

namespace minion\Connections;


abstract class ConnectionAbstract
{
    /**
     * @var string
     */
    protected $currentDirectory;

    /**
     * Set the working directory for all commands
     *
     * @param string|null $directory
     */
    public function cwd($directory = null)
    {
        $this->currentDirectory = $directory;
    }

    /**
     * Get the current working directory
     *
     * @return string
     */
    public function pwd()
    {
        return $this->currentDirectory;
    }

	abstract public function execute($command);
}