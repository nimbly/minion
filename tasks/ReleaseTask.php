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
use minion\Connection;
use minion\interfaces\TaskInterface;

class ReleaseTask implements TaskInterface {

	public function run(Context $context, Environment $environment, Connection $connection = null) {

		// Make sure releases directory exists
		$connection->execute("mkdir -p {$environment->remote->path}/releases");

		// Create a new release
		$release = date('YmdHis');

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
					$command = "git clone {$environment->code->repo} --branch={$branch} {$release}&&cd {$release}&&git checkout {$commit}";
				}
				else {
					$command = "git clone {$environment->code->repo} --depth=1 --branch={$branch} {$release}";
				}
				break;

			case 'svn':
				$command = "svn checkout {$environment->code->repo} {$release}";
				break;

			default:
				throw new \Exception("Unsupported SCM: {$environment->code->scm} should be one of GIT or SVN");
		}

		// Execute release
		$connection->execute("cd {$environment->remote->path}/releases&&{$command}");

		// Remove old symlink
		$connection->execute("rm -f {$environment->remote->path}/current");

		// Create new symlink
		$connection->execute("cd {$environment->remote->path}&&ln -s -r releases/{$release} current");

	}

}