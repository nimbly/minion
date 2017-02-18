<?php

namespace minion\Commands;


use minion\Config\Config;
use minion\Connections\RemoteConnection;
use minion\Tasks\TaskAbstract;
use minion\Tasks\TaskManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class DeployReleaseCommand extends Command
{

    protected function configure()
    {
        $this->setName('deploy:release')
            ->setDescription('Start a release deployment')
            ->setHelp('Release your code specified environment')
            ->addArgument('environment', InputArgument::REQUIRED, 'Environment to deploy to')
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Config file', 'minion.yml')
            ->addOption('branch', null, InputOption::VALUE_OPTIONAL, 'Branch to use', 'master')
            ->addOption('commit', null, InputOption::VALUE_OPTIONAL, 'Commit to use');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getArgument('environment');

        // Read in the config file
        $configFile = $input->getOption('config');

        if( !file_exists($configFile) ){
            $output->writeln("<error>Config file {$configFile} not found");
            return -1;
        }

        $config = new Config((new Parser)->parse(file_get_contents($configFile)));

        // Get the environment config
        if( ($environment = $config->setEnvironment($env)) == false ) {
            $output->writeln("<error>No environment config found for {$env}</error>");
            return -1;
        }

        $output->writeln("Running <info>{$this->getName()}</info> command");

        // Loop through servers and implement strategy on each
        foreach( $environment->servers as $server ) {

            if( empty($server->strategy) ) {
                $output->writeln("<error>No deployment strategy defined</error>");
            }

            $connection = new RemoteConnection($server, $environment->authentication);

            foreach( $server->strategy as $task ) {

                /** @var TaskAbstract $task */
                $task = TaskManager::create($task, $input, $output);
                $task->run($config, $connection);
            }

            $connection->close();
        }

        return null;
    }
}