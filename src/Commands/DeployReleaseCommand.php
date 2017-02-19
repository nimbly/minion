<?php

namespace minion\Commands;


use minion\Config\Environment;
use minion\Connections\LocalConnection;
use minion\Connections\RemoteConnection;
use minion\Tasks\TaskAbstract;
use minion\Tasks\TaskManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
        $environment = new Environment(
            $input->getOption('config'),
            $input->getArgument('environment')
        );

        $output->writeln("Running <info>{$this->getName()}</info> command");

        // Pre-deploy (runs before deploy using a local connection)
        if( $environment->preDeploy ){
            $output->writeln("Running <info>pre-deploy</info> tasks");
            $connection = new LocalConnection;
            foreach( $environment->preDeploy as $task ){
                /** @var TaskAbstract $task */
                $task = TaskManager::create($task, $input, $output);
                $task->run($environment, $connection);
            }
        }

        // Loop through servers and implement strategy on each
        foreach( $environment->servers as $server ) {

            $output->writeln("Deploying to <info>{$server->host}</info>");

            if( empty($server->strategy) ) {
                $output->writeln("<error>No deployment strategy defined</error>");
            }

            $connection = new RemoteConnection($server, $environment->authentication);

            foreach( $server->strategy as $task ) {

                /** @var TaskAbstract $task */
                $task = TaskManager::create($task, $input, $output);
                $task->run($environment, $connection);
            }

            $connection->close();
        }

        // Post-deploy (runs after deploy using a local connection)
        if( $environment->postDeploy ){
            $output->writeln("Running <info>post-deploy</info> tasks");
            $connection = new LocalConnection;
            foreach( $environment->postDeploy as $task ){
                /** @var TaskAbstract $task */
                $task = TaskManager::create($task, $input, $output);
                $task->run($environment, $connection);
            }
        }

        $output->writeln("Done");

        return null;
    }
}