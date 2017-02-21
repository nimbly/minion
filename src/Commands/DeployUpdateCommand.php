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

        $style = new SymfonyStyle($input, $output);
        $style->title("Deploying update on <info>{$environment->name}</info>");

        $progressBar = $this->defaultProgressBar($output, count($environment->servers));

        // Loop through servers and run update
        foreach( $environment->servers as $server ) {
            $progressBar->setMessage("Updating <info>{$server->host}</info>");

            $connection = new RemoteConnection($server, $environment->authentication);

            /** @var TaskAbstract $task */
            $task = TaskManager::create('Update', $input, $output);
            $task->run($environment, $connection);

            $progressBar->advance();
            $connection->close();
        }

        $progressBar->setMessage('Done');
        $progressBar->finish();

        $style->writeln("\n");
        $style->success("Update complete");

        return null;
    }

    protected function defaultProgressBar(OutputInterface $output, $max = null)
    {
        $progressBar = new ProgressBar($output, $max);
        $progressBar->setFormatDefinition('custom', " %current%/%max% [%bar%] %percent:3s%% / %message%");
        $progressBar->setEmptyBarCharacter('░'); // light shade character \u2591
        $progressBar->setProgressCharacter('');
        $progressBar->setBarCharacter('▓'); // dark shade character \u2593
        $progressBar->setFormat('custom');
        return $progressBar;
    }
}