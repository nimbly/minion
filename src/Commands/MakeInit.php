<?php

namespace minion\Commands;

use minion\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class MakeInit extends Command
{
	protected function configure(): void
	{
		$this->setName("make:init")
			->setDescription("Initialize minion installation")
			->setHelp("Initialize minion installation by creating a Tasks folder and a configuration file.")
			->addArgument("config", InputArgument::OPTIONAL, "Config file", "minion.yml");
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = new SymfonyStyle($input, $output);
		$style->title("Initializing <info>minion</info>");

		if( \file_exists("Tasks") == false ) {
			\mkdir("Tasks");
		}

		if( \file_exists("Commands") == false ) {
			\mkdir("Commands");
		}

		$config = (string) $input->getArgument("config");

		if( \file_exists($config) ) {
			throw new \Exception("Config file {$config} already exists");
		}

		$style = new SymfonyStyle($input, $output);

		if( $style->confirm("Do you want to use the interactive configuration tool?", true) ){
			$environment = Config::interactive($style);
			$data = Yaml::dump($environment, 5);
			$style->warning("Before you can begin deploying releases, you must update your HTTP server configurations to point to \"{$environment["remote"]["path"]}/{$environment["remote"]["symlink"]}\" as the base web root.");
		}
		else {
			$data = \file_get_contents(__DIR__ . "/../Templates/config.yml.tpl");
			$style->note("Configuration file created as {$config}. Before you can begin deploying code, you must edit the configuration file with your repo, ssh authentication, environment, and server settings.");
			$style->warning("Before you can begin deploying releases, you must update your HTTP server configurations to point to the current release as the base web root.");
		}

		Config::make($config, $data);

		$style->success("Done");

		return 0;
	}
}