<?php

namespace minion\Tasks;


use minion\Config\Config;
use minion\Connections\ConnectionAbstract;

class LinkTask extends TaskAbstract
{
    public function run(Config $config, ConnectionAbstract $connection = null)
    {
        if( empty($config->environment->remote->release) ){
            $this->output->writeln("\t<info>Skipping. No release found.");
            return;
        }

        $this->output->writeln("\t<info>Linking new release</info>");

        // Remove old symlink
        $connection->execute("rm -f {$config->environment->remote->currentRelease}");

        // Create new symlink
        $connection->execute("cd {$config->environment->remote->path}&&ln -s -r {$config->environment->remote->releaseDir}/{$config->environment->remote->release} {$config->environment->remote->symlink}");
    }
}