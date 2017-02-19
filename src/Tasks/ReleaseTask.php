<?php

namespace minion\Tasks;


use minion\Config\Environment;
use minion\Connections\ConnectionAbstract;

class ReleaseTask extends TaskAbstract {

	public function run(Environment $environment, ConnectionAbstract $connection = null) {

		// Make sure releases directory exists
		$connection->execute("mkdir -p {$environment->remote->getReleases()}");

		// Create a new release
		$release = date('YmdHis');

		$environment->remote->setActiveRelease($release);

		$this->output->writeln("\t<info>Creating release directory {$release}</info>");

		if( ($branch = $this->input->getOption('branch')) === null ) {
			$branch = $environment->code->branch;
		}

		if( ($commit = $this->input->getOption('commit')) === null ) {
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

				$this->output->writeln("\t<info>Cloning {$environment->code->repo}</info>");
				break;

			case 'svn':
				$command = "svn checkout {$environment->code->repo} {$release}";
				$this->output->writeln("\t<info>Checking out {$environment->code->repo}</info>");
				break;

			default:
			    $this->output->writeln("<error>Unsupported SCM: {$environment->code->scm} should be one of \"git\"or \"svn\"</error>");
			    return -1;
		}

		// Execute release
		$connection->execute("cd {$environment->remote->getReleases()}&&{$command}");

		return null;
	}

}