<?php

namespace minion\Commands;


use minion\Config\Environment;
use minion\Connections\RemoteConnection;
use minion\Tasks\TaskAbstract;
use minion\Tasks\TaskManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

        $environment = new Environment(
            $input->getOption('config'),
            $input->getArgument('environment')
        );

        $host = $input->getOption('host');

        // Loop through servers and run task
        foreach( $environment->servers as $server ) {
            $connection = new RemoteConnection($server, $environment->authentication);

            if( !$host ||
                ($host && $host == $server->host) ){
                /** @var TaskAbstract $task */
                $task = TaskManager::create($task, $input, $output);
                $task->run($environment, $connection);
            }
        }

        return null;
    }
}