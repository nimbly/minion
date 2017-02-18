<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 2/17/17
 * Time: 12:17 PM
 */

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
            ->addArgument('command', InputArgument::REQUIRED, 'Name of the command to make');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $command = ucfirst(strtolower($input->getArgument('command')));
        $filename = "{$command}Command.php";
        $path = "Commands";

        if( file_exists($path) == false ){
            $output->writeln("<error>Commands directory not found. Have you run \"minion init\" yet?</error>");
        }

        $output->writeln("<info>Creating {$command} command</info>");

        if( file_exists("{$path}/{$filename}") ){
            $output->writeln("<error>File \"{$path}/{$filename}\" already exists</error>");
            return -1;
        }

        $template = file_get_contents(__DIR__.'/../Templates/Command.php.tpl');
        $template = preg_replace('/\:CommandName/', "{$command}Command", $template);
        file_put_contents("{$path}/{$filename}", $template);

        return null;
    }

}