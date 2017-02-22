<?php

namespace minion\Commands;

use minion\Config\Environment;
use minion\Connections\RemoteConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OpcacheStatusCommand extends Command
{
    protected function configure()
    {
        $this->setName('opcache:status')
            ->setAliases(['status:opcache'])
            ->setDescription('Get the OpCache status')
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

        $style = new SymfonyStyle($input, $output);

        $style->title('Getting OpCache status');

        foreach( $environment->servers as $server )
        {
            if( !$host ||
                ($host && $host == $server->host) ){
                $connection = new RemoteConnection($server, $environment->authentication);
                $response = $connection->execute("cd {$environment->remote->getCurrentRelease()}&&sudo vendor/bin/cachetool opcache:status --fcgi=/var/run/php-fpm/www.sock");
                $style->writeln($response);
                $connection->close();
            }
        }
    }
}