<?php

namespace minion\Tasks;


use minion\Config\Config;
use minion\Connections\ConnectionAbstract;

class ReleaseTask extends TaskAbstract {

	public function run(Config $config, ConnectionAbstract $connection = null) {

		// Make sure releases directory exists
		$connection->execute("mkdir -p {$config->environment->remote->path}/{$config->environment->remote->releaseDir}");

		// Create a new release
		$config->environment->remote->release = date('YmdHis');

		$this->output->writeln("\t<info>Creating release directory {$config->environment->remote->release}</info>");

		if( ($branch = $this->input->getOption('branch')) === null ) {
			$branch = $config->environment->code->branch;
		}

		if( ($commit = $this->input->getOption('commit')) === null ) {
			$commit = null;
		}

		// What SCM command?
		switch( strtolower($config->environment->code->scm) ) {
			case 'git':
				if( $commit ) {
					$command = "git clone {$config->environment->code->repo} --branch={$branch} {$config->environment->remote->release}&&cd {$config->environment->remote->release}&&git checkout {$commit}";
				}
				else {
					$command = "git clone {$config->environment->code->repo} --depth=1 --branch={$branch} {$config->environment->remote->release}";
				}

				$this->output->writeln("\t<info>Cloning {$config->environment->code->repo}</info>");
				break;

			case 'svn':
				$command = "svn checkout {$config->environment->code->repo} {$config->environment->remote->release}";
				$this->output->writeln("\t<info>Checking out {$config->environment->code->repo}</info>");
				break;

			default:
			    $this->output->writeln("<error>Unsupported SCM: {$config->environment->code->scm} should be one of \"git\"or \"svn\"</error>");
			    return -1;
		}

		// Execute release
		$connection->execute("cd {$config->environment->remote->deployTo}&&{$command}");

		return null;
	}

}