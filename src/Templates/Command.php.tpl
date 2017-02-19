<?php

namespace minion\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class :CommandName extends Command
{
    protected function configure()
    {
        $this->setName(':CommandName');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
    }
}