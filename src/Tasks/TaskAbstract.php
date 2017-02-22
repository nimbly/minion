<?php

namespace minion\Tasks;

use minion\Config\Environment;
use minion\Connections\ConnectionAbstract;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class TaskAbstract {

    /** @var InputInterface  */
    protected $input;

    /** @var OutputInterface  */
    protected $output;

    /**
     * TaskInterface constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output){
        $this->input = $input;
        $this->output = $output;
    }

    abstract public function run(Environment $environment, ConnectionAbstract $connection);
}