<?php

namespace minion\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeTask extends Command
{

    protected function configure()
    {
        $this->setName('make:task')
            ->setDescription('Make a new task')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the task to create');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $style->title('Making new task');

        $task = ucfirst(strtolower($input->getArgument('name')));
        $filename = "{$task}.php";
        $path = 'Tasks';

        if( file_exists($path) == false ){
            mkdir($path);
        }

        if( file_exists("{$path}/{$filename}") ){
            throw new \Exception("File {$path}/{$filename} already exists");
        }

        $template = file_get_contents(__DIR__.'/../Templates/Task.php.tpl');
        $template = preg_replace('/\:TaskName/', $task, $template);
        file_put_contents("{$path}/{$filename}", $template);

        $style->success('Task created');

        return null;
    }

}