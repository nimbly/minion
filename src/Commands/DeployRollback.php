<?php

namespace minion\Commands;

use minion\Config\Environment;
use minion\Connections\RemoteConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeployRollback extends Command
{
	protected function configure(): void
	{
		$this->setName("deploy:rollback")
			->setDescription("Rollback last deploy")
			->addArgument("environment", InputArgument::REQUIRED, "Environment")
			->addOption("release", null, InputOption::VALUE_OPTIONAL, "Release to rollback to")
			->addOption("config", null, InputOption::VALUE_OPTIONAL, "Config file", "minion.yml");
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$environment = new Environment(
			$input->getOption("config"),
			$input->getArgument("environment")
		);

		$style = new SymfonyStyle($input, $output);
		$style->title("Rolling back on <info>{$environment->name}</info>");

		// Specific release to roll back to?
		$specificRelease = $input->getOption("release");

		$progressBar = $environment->defaultProgressBar($output, count($environment->servers));

		foreach( $environment->servers as $server ) {
			$progressBar->setMessage("{$server->host}");

			$connection = new RemoteConnection($server, $environment->authentication);

			// get the current releases
			$releases = $connection->execute("ls {$environment->remote->getReleases()}");
			$releases = explode("\n", trim($releases));

			$release = null;

			// Specific release to rollback to
			if( $specificRelease ) {
				foreach($releases as $r ) {
					if( $specificRelease == $r ) {
						$release = $r;
					}
				}

				if( !$release ) {
					throw new \Exception("Release {$specificRelease} not found");
				}
			}

			// Just rollback to previous release
			elseif( count($releases) > 1 ) {
				$release = $releases[count($releases) - 2];
			}

			else {
				throw new \Exception("No previous release available to rollback to");
			}

			$progressBar->advance();

			// Do the rollback (symlink to previous release)
			$connection->execute("cd {$environment->remote->path}&&rm -f current&&ln -s -r {$environment->remote->releaseDir}/{$release} {$environment->remote->symlink}");
			$connection->close();
		}

		$progressBar->setMessage("Done");
		$progressBar->finish();

		$style->newLine(2);
		$style->success("Rollback complete");

		return 0;
	}
}