<?php

namespace minion\Tasks;


use minion\Config\Environment;
use minion\Connections\ConnectionAbstract;

class LinkTask extends TaskAbstract
{
    public function run(Environment $environment, ConnectionAbstract $connection = null)
    {
        if( empty($environment->remote->getActiveRelease()) ){
            $this->output->writeln("\t<info>Skipping. No active release found.");
            return;
        }

        $this->output->writeln("\t<info>Linking new release</info>");

        // Remove old symlink
        $this->output->writeln("Removing old symlink");
        $connection->execute("rm -f {$environment->remote->getCurrentRelease()}");

        // Create new symlink
        $this->output->writeln("Creating new symlink: {$environment->remote->path}");
        $connection->execute("cd {$environment->remote->path}&&ln -s {$environment->remote->getActiveRelease()} {$environment->remote->symlink}");
    }
}