<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 2/17/17
 * Time: 12:27 PM
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

        // Loop through servers and run update
        foreach( $environment->servers as $server ) {
            $connection = new RemoteConnection($server, $environment->authentication);

            /** @var TaskAbstract $task */
            $task = TaskManager::create('Update', $input, $output);
            $task->run($config, $connection);

            $connection->close();
        }

        return null;
    }
}