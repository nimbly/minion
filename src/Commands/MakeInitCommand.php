<?php

namespace minion\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeInitCommand extends Command
{

    protected function configure()
    {
        $this->setName('make:init')
            ->setDescription('Initialize minion installation')
            ->setHelp('Initialize minion installation by creating a Tasks folder and a default configuration file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if( !file_exists('Commands') ){
            mkdir('Commands');
        }

        if( !file_exists('Tasks') ){
            mkdir('Tasks');
        }

        // Make a config file
        $command = $this->getApplication()->find('make:config');
        $command->run(new ArrayInput([]), $output);

        // Create a migrate task
        $command = $this->getApplication()->find('make:task');
        $command->run(new ArrayInput(['name' => 'Migrate']), $output);
    }

}