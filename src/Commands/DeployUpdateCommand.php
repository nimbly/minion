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

class DeployUpdateCommand extends Command
{

    protected function configure()
    {
        $this->setName('deploy:update')
            ->setDescription('Update the existing release')
            ->addArgument('environment', InputArgument::REQUIRED, 'Environment to update')
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Config file', 'minion.yml');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = new Environment(
            $input->getOption('config'),
            $input->getArgument('environment')
        );

        $output->writeln("Running <info>{$this->getName()}</info> command");

        // Loop through servers and run update
        foreach( $environment->servers as $server ) {
            $connection = new RemoteConnection($server, $environment->authentication);

            /** @var TaskAbstract $task */
            $task = TaskManager::create('Update', $input, $output);
            $task->run($environment, $connection);

            $connection->close();
        }

        return null;
    }
}