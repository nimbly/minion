<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 2/17/17
 * Time: 12:52 PM
 */

namespace minion\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeConfigCommand extends Command
{
    protected function configure()
    {
        $this->setName('make:config')
            ->setDescription('Make a default configuration file')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'File name', 'minion.yml');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption('name');

        if( file_exists($name) ) {
            $output->writeln("<error>Config file {$name} already exists.</error>");
            return -1;
        }

        else {
            copy(__DIR__ . '/../Templates/config.yml.tpl', $name);
        }

        return null;
    }

}