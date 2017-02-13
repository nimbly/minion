<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 4:46 PM
 */

namespace minion\tasks;


use minion\config\Context;
use minion\config\Environment;
use minion\interfaces\ConnectionInterface;
use minion\interfaces\TaskInterface;

class ReleaseTask implements TaskInterface {

	public function run(Context $context, Environment $environment, ConnectionInterface $connection = null) {

		// Make sure releases directory exists
		$connection->execute("mkdir -p {$environment->remote->path}/{$environment->remote->releaseDir}");

		// Create a new release
		$environment->remote->release = date('YmdHis');
		$context->say("\tCreating release directory {$environment->remote->release}");

		if( ($branch = $context->getArgument(['b', 'branch'])) === null ) {
			$branch = $environment->code->branch;
		}

		if( ($commit = $context->getArgument(['c', 'commit'])) === null ) {
			$commit = null;
		}

		// What SCM command?
		switch( strtolower($environment->code->scm) ) {
			case 'git':
				if( $commit ) {
					$command = "git clone {$environment->code->repo} --branch={$branch} {$environment->remote->release}&&cd {$environment->remote->release}&&git checkout {$commit}";
				}
				else {
					$command = "git clone {$environment->code->repo} --depth=1 --branch={$branch} {$environment->remote->release}";
				}

                $context->say("\tCloning {$environment->code->repo}");
				break;

			case 'svn':
				$command = "svn checkout {$environment->code->repo} {$environment->remote->release}";
                $context->say("\tChecking out {$environment->code->repo}");
				break;

			default:
				throw new \Exception("Unsupported SCM: {$environment->code->scm} should be one of GIT or SVN");
		}

		// Execute release
		$connection->execute("cd {$environment->remote->deployTo}&&{$command}");
	}

}