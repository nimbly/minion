<?php

namespace minion\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCommandCommand extends Command
{

    protected function configure()
    {
        $this->setName('make:command')
            ->setDescription('Make a new command')
            ->setHelp('Make a new command')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the command to make');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $command = ucfirst($input->getArgument('name'));
        $filename = "{$command}.php";
        $path = "Commands";

        if( file_exists($path) == false ){
            mkdir('Commands');
        }

        $output->writeln("<info>Creating {$command} command</info>");

        // Does file name already exist?
        if( file_exists("{$path}/{$filename}") ){
            $output->writeln("<error>File \"{$path}/{$filename}\" already exists</error>");
            return -1;
        }

        // Write new command to disk
        $template = file_get_contents(__DIR__.'/../Templates/Command.php.tpl');
        $template = preg_replace('/\:CommandName/', $command, $template);
        file_put_contents("{$path}/{$filename}", $template);

        return null;
    }

}