<?php

namespace minion\Commands;

use minion\Config\Environment;
use minion\Connections\RemoteConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OpcacheResetCommand extends Command
{
    protected function configure()
    {
        $this->setName('opcache:flush')
            ->setDescription('Reset (flush) the OpCache')
            ->addArgument('environment', InputArgument::REQUIRED, 'Environment')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host')
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Config file', 'minion.yml');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = new Environment(
            $input->getOption('config'),
            $input->getArgument('environment')
        );

        $host = $input->getOption('host');

        foreach( $environment->servers as $server )
        {
            if( !$host ||
                ($host && $host == $server->host) ){
                $connection = new RemoteConnection($server, $environment->authentication);
                $connection->execute("cd {$environment->remote->getCurrentRelease()}&&sudo vendor/bin/cachetool opcache:reset --fcgi=/var/run/php-fpm/www.sock");
                $connection->close();
            }
        }
    }
}