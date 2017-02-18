<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 2/17/17
 * Time: 12:20 PM
 */

namespace minion\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeTaskCommand extends Command
{

    protected function configure()
    {
        $this->setName('make:task')
            ->setDescription('Make a new task')
            ->setHelp('Make a new task')
            ->addArgument('task', InputArgument::REQUIRED, 'Name of the task to create');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $task = ucfirst(strtolower($input->getArgument('task')));
        $filename = "{$task}Task.php";
        $path = "Tasks";

        if( file_exists($path) == false ){
            $output->writeln("<error>Tasks directory not found. Have you run \"minion init\" yet?</error>");
        }

        $output->writeln("<info>Creating {$task} task</info>");

        if( file_exists("{$path}/{$filename}") ){
            $output->writeln("<error>File \"{$path}/{$filename}\" already exists</error>");
            return -1;
        }

        $template = file_get_contents(__DIR__.'/../Templates/Task.php.tpl');
        $template = preg_replace('/\:TaskName/', "{$task}Task", $template);
        file_put_contents("{$path}/{$filename}", $template);

        return null;
    }

}