<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 2/17/17
 * Time: 1:14 PM
 */

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

class TaskCommand extends Command
{

    protected function configure()
    {
        $this->setName('task')
            ->setDescription('Run a single task')
            ->addArgument('task', InputArgument::REQUIRED, 'Name of task to run')
            ->addArgument('environment', InputArgument::REQUIRED, 'Environment to run task')
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Config file to use', 'minion.yml')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host to run task on');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Running {$input->getArgument('task')} in {$input->getArgument('environment')}</info>");

        if( ($host = $input->getOption('host')) ){
            $output->writeln("<info>Running it only on host {$host}</info>");
        }

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

        if( ($host = $input->getOption('host')) == false ){
            $host = null;
        }

        // Loop through servers and run task
        foreach( $environment->servers as $server ) {
            $connection = new RemoteConnection($server, $environment->authentication);

            if( !$host ||
                ($host && $host == $server->host) ){
                /** @var TaskAbstract $task */
                $task = TaskManager::create($task, $input, $output);
                $task->run($config, $connection);
            }
        }

        return null;
    }
}