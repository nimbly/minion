<?php

namespace minion\Connections;

abstract class ConnectionAbstract
{
	protected string $currentDirectory = "";

	/**
	 * Set the working directory for all commands
	 *
	 * @param string $directory
	 */
	public function cwd(string $directory): void
	{
		$this->currentDirectory = $directory;
	}

	/**
	 * Get the current working directory
	 *
	 * @return string
	 */
	public function pwd(): string
	{
		return $this->currentDirectory;
	}

	/**
	 * Execute a command.
	 *
	 * @param string $command
	 * @return string|null
	 */
	abstract public function execute(string $command): ?string;
}