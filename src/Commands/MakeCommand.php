<?php

namespace minion\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeCommand extends Command
{
	protected function configure(): void
	{
		$this->setName("make:command")
			->setDescription("Make a new command")
			->setHelp("Make a new command")
			->addArgument("class", InputArgument::REQUIRED, "Class name of the command to make")
			->addOption("name", null, InputOption::VALUE_OPTIONAL, "The command name to execute");
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = new SymfonyStyle($input, $output);
		$style->title("Making command");

		$className = $input->getArgument("class");
		$filename = "{$className}.php";
		$path = "Commands";

		if( ($commandName = $input->getOption("name")) == false ){
			$commandName = $className;
		}

		if( \file_exists($path) === false ){
			\mkdir("Commands");
		}

		// Does file name already exist?
		if( \file_exists("{$path}/{$filename}") ){
			throw new \Exception("File {$path}/{$filename} already exists");
		}

		// Write new command to disk
		$template = \file_get_contents(__DIR__."/../Templates/Command.php.tpl");
		$template = \preg_replace("/\:ClassName/", $className, $template);
		$template = \preg_replace("/\:CommandName/", $commandName, $template);
		\file_put_contents("{$path}/{$filename}", $template);

		$style->success("Command created");

		return 0;
	}
}