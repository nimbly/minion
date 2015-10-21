<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 4:46 PM
 */

namespace minion\tasks;


use minion\config\Environment;
use minion\Connection;
use minion\interfaces\TaskInterface;

class ReleaseTask implements TaskInterface {

	public function run(Environment $environment, Connection $connection = null) {

		// Make sure releases directory exists
		$connection->execute("mkdir -p {$environment->remote->path}/releases");

		// Create a new release
		$release = date('YmdHis');

		// An override branch/tag was specified
		if( isset($environment->arguments[0]) ) {
			$branch = $environment->arguments[0];
		} else {
			$branch = $environment->code->branch;
		}

		// What SCM command?
		switch( strtolower($environment->code->scm) ) {
			case 'git':
				$scmCheckoutCommand = "git clone {$environment->code->repo} --depth=1 --branch {$branch} {$release}";
				break;

			case 'svn':
				$scmCheckoutCommand = "svn checkout {$environment->code->repo} {$release}";
				break;

			default:
				throw new \Exception("Unsupported SCM: {$environment->code->scm} should be one of GIT or SVN");
				exit;
		}

		// Execute deploy
		$connection->execute("cd {$environment->remote->path}/releases&&{$scmCheckoutCommand}");

		// Create symlink
		$connection->execute("cd {$environment->remote->path}&&rm -f current&&ln -s -r releases/{$release} current");

	}

}