<?php

namespace minion\Commands;


use minion\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class MakeConfig extends Command
{
    protected function configure()
    {
        $this->setName('make:config')
            ->setDescription('Make a new default configuration file')
            ->addArgument('config', InputArgument::OPTIONAL, 'Name of configuration file', 'minion.yml');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $style->title('Making config file');

        $config = $input->getArgument('config');

        if( file_exists($config) ) {
            throw new \Exception("Config file {$config} already exists");
        }

        if( $style->confirm('Do you want to use the interactive configuration tool?', true) ){
            $environment = Config::interactive($style);
            $data = Yaml::dump($environment, 5);
            $style->warning("Before you can begin deploying releases, you must update your HTTP server configurations to point to \"{$environment['remote']['path']}/{$environment['remote']['symlink']}\" as the base web root.");
        }
        else {
            $data = file_get_contents(__DIR__ . '/../Templates/config.yml.tpl');
            $style->note("Configuration file created as {$config}. Before you can begin deploying code, you must edit the configuration file with your repo, ssh authentication, environment, and server settings.");
            $style->warning("Before you can begin deploying releases, you must update your HTTP server configurations to point to the current release as the base web root.");
        }

        Config::make($config, $data);

        $style->success('Config file created');

        return null;
    }

}