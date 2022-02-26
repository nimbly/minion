<?php

namespace minion\Commands;

use minion\Config\Environment;
use minion\Connections\LocalConnection;
use minion\Connections\RemoteConnection;
use minion\Tasks\TaskManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeployRelease extends Command
{
	protected function configure(): void
	{
		$this->setName("deploy:release")
			->setDescription("Start a release deployment")
			->setHelp("Creates a new release of your code on the specified environment")
			->addArgument("environment", InputArgument::REQUIRED, "Environment to deploy to")
			->addOption("config", null, InputOption::VALUE_OPTIONAL, "Config file", "minion.yml")
			->addOption("branch", null, InputOption::VALUE_OPTIONAL, "Branch to use")
			->addOption("commit", null, InputOption::VALUE_OPTIONAL, "Commit to use");
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$environment = new Environment(
			$input->getOption("config"),
			$input->getArgument("environment")
		);

		$style = new SymfonyStyle($input, $output);
		$style->title("Deploying new release on <info>{$environment->name}</info>");

		// Pre-deploy tasks
		if( $environment->preDeploy ){
			$style->section("Running <info>pre-deploy</info> tasks");
			$this->doLocalTasks($environment->preDeploy, $environment, $input, $output);
			$style->newLine();
		}

		$style->section("Applying strategy on servers");

		// Loop through servers and implement strategy on each
		foreach( $environment->servers as $server ) {
			if( empty($server->strategy) ) {
				throw new \Exception("No deployment strategy defined for \"{$environment->name}\"");
			}

			$style->comment($server->host);

			$connection = new RemoteConnection($server, $environment->authentication);
			$progressBar = $environment->defaultProgressBar($output, count($server->strategy));

			$progressBar->setMessage("Connecting");
			$progressBar->display();

			foreach( $server->strategy as $task ) {
				$progressBar->setMessage("Running <info>{$task}</info> task");

				$task = TaskManager::create($task, $input, $output);
				$task->run($environment, $connection);

				$progressBar->advance();
			}

			$progressBar->setMessage("Done");
			$progressBar->finish();
			$connection->close();
			$style->newLine(2);
		}

		// Post deploy tasks
		if( $environment->postDeploy ){
			$style->newLine();
			$style->section("Running <info>post-deploy</info> tasks");
			$this->doLocalTasks($environment->postDeploy, $environment, $input, $output);
			$style->newLine();
		}

		$style->newLine();
		$style->success("Release complete");

		return 0;
	}

	protected function doLocalTasks(array $tasks, Environment $environment, InputInterface $input, OutputInterface $output): void
	{
		// Local tasks
		if( $tasks ){
			$connection = new LocalConnection;
			$progressBar = $environment->defaultProgressBar($output, \count($environment->preDeploy));
			foreach( $tasks as $task ){
				$progressBar->setMessage("Running <info>{$task}</info> task");

				$task = TaskManager::create($task, $input, $output);
				$task->run($environment, $connection);

				$progressBar->advance();
			}

			$progressBar->setMessage("Done");
			$progressBar->finish();
			$output->writeln("");
		}
	}
}