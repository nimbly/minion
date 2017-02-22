<?php

namespace minion\Commands;


use minion\Config\Environment;
use minion\Connections\RemoteConnection;
use minion\Tasks\TaskAbstract;
use minion\Tasks\TaskManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeployUpdate extends Command
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

        $style = new SymfonyStyle($input, $output);
        $style->title("Deploying update on <info>{$environment->name}</info>");

        $progressBar = $environment->defaultProgressBar($output, count($environment->servers));

        // Loop through servers and run update
        foreach( $environment->servers as $server ) {
            $progressBar->setMessage("{$server->host}");

            $connection = new RemoteConnection($server, $environment->authentication);

            /** @var TaskAbstract $task */
            $task = TaskManager::create('Update', $input, $output);
            $task->run($environment, $connection);

            $progressBar->advance();
            $connection->close();
        }

        $progressBar->setMessage('Done');
        $progressBar->finish();

        $style->newLine(2);
        $style->success("Update complete");

        return null;
    }
}