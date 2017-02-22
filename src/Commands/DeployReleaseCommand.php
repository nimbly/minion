<?php

namespace minion\Commands;


use minion\Config\Environment;
use minion\Connections\LocalConnection;
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

        $style = new SymfonyStyle($input, $output);
        $style->title("Deploying new release on <info>{$environment->name}</info>");

        // Pre-deploy tasks
        if( $environment->preDeploy ){
            $style->section("Running <info>pre-deploy</info> tasks");
            $this->doLocalTasks($environment->preDeploy, $environment, $input, $output);
            $style->writeln("");
        }

        $style->section("Applying release strategy on servers");

        // Loop through servers and implement strategy on each
        foreach( $environment->servers as $server ) {
            if( empty($server->strategy) ) {
                throw new \Exception("No deployment strategy defined for \"{$environment->name}\"");
            }

            $style->comment($server->host);

            $connection = new RemoteConnection($server, $environment->authentication);
            $progressBar = $this->defaultProgressBar($output, count($server->strategy));

            $progressBar->setMessage('Connecting');
            $progressBar->display();

            foreach( $server->strategy as $task ) {
                $progressBar->setMessage("Running <info>{$task}</info> task");

                /** @var TaskAbstract $task */
                $task = TaskManager::create($task, $input, $output);
                $task->run($environment, $connection);

                $progressBar->advance();
            }

            $progressBar->setMessage("Done");
            $progressBar->finish();
            $connection->close();
            $style->writeln("\n");
        }

        // Post deploy tasks
        if( $environment->postDeploy ){
            $style->writeln("");
            $style->section("Running <info>post-deploy</info> tasks");
            $this->doLocalTasks($environment->postDeploy, $environment, $input, $output);
            $style->writeln("");
        }

        $style->writeln("");
        $style->success("Release complete");

        return null;
    }

    protected function doLocalTasks(array $tasks, Environment $environment, InputInterface $input, OutputInterface $output)
    {
        // Local tasks
        if( $tasks ){
            $connection = new LocalConnection;
            $progressBar = $this->defaultProgressBar($output, count($environment->preDeploy));
            foreach( $tasks as $task ){
                $progressBar->setMessage("Running <info>{$task}</info> task");

                /** @var TaskAbstract $task */
                $task = TaskManager::create($task, $input, $output);
                $task->run($environment, $connection);

                $progressBar->advance();
            }

            $progressBar->setMessage("Done");
            $progressBar->finish();
        } else {
            $output->writeln('<info>None</info>');
        }

        $output->writeln('');
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