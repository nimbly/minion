<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 2/12/17
 * Time: 9:00 PM
 */

namespace minion\tasks;


use minion\config\Context;
use minion\config\Environment;
use minion\interfaces\ConnectionInterface;
use minion\interfaces\TaskInterface;

class LinkTask implements TaskInterface
{

    public function run(Context $context, Environment $environment, ConnectionInterface $connection = null)
    {
        if( empty($environment->remote->release) ){
            $context->say("\tSkipping. No release found.");
            return;
        }

        $context->say("\tLinking new release");

        // Remove old symlink
        $connection->execute("rm -f {$environment->remote->currentRelease}");

        // Create new symlink
        $connection->execute("cd {$environment->remote->path}&&ln -s -r {$environment->remote->releaseDir}/{$environment->remote->release} {$environment->remote->symlink}");
    }
}