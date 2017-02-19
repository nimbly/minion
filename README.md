## How it works
**minion** creates a new checkout/clone of your repo for each deployment.
This allows clean installs and easy rollbacks to previous releases. The general idea
is a single directory holds the releases and a symlink points to the current release.

**NOTE**: In order for this to work with your webserver (Apache, Nginx, etc), you **will need**
      to update its configuration to point to the symlink as its webroot.

During deployment, **minion** will apply your deployment **strategy** on each defined
server. A deployment **strategy** is simply a list of **tasks** you would like **minion**
to run.

**minion** has several built-in **tasks** - however, you will need to create and implement
your own tasks to take full advantage. Some examples: install composer, run migrations,
flush caches, restart web service, restart web application, etc.

## Install
`composer require nimbly/minion`

## Initialize
`minion make:init`

## Configure
If you run the `make:init` command, minion will create a couple directories (`Tasks` and `Commands`) as well as generate
a default configuration file (`minion.yml`).

The first several sections of the config file define *global* config options. Each environment defined may optionally
override these global options. 

The global sections are:

* remote
* authentication
* code

#### remote
The remote section defines options on the remote server environment including where to deploy code.

The options for the *remote* sections are as follows:

* **path** [string]
	The absolute path to the code on the server. I.e. the path to deploy code to.

* **releaseDir** [string]
    The path to keep the releases (relative to **path**).
    
* **symlink** [string]
    Name of the symlink to create to point to current release.
    
* **keepReleases** [integer]
	The number of releases to keep before pruning old releases.

#### authentication
The authentication section defines how SSH can authenticate with your servers. You can either authenticate with a username
and password *or* using a username, key and (optionally) a key pass phrase.

**NOTE:** because **minion** uses PHPSECLIB as its core SSH library, you can **only** use RSA keys - DSA keys are
not supported at this time.

The options for the *authentication* sections are as follows:

* **username** [string]
	The SSH username to connect with.

* **password** [string] *optional*
	The SSH password to use (if not using key based authentication).

* **key** [string] *optional*
	Path and file name of RSA key file.

* **passphrase** [string] *optional*
	Pass phrase for key (if using key based authentication).

#### code
The code section defines how and where your code is stored.

* **scm** [git, svn]
	What source code management tool you use.

* **repo** [string]
	Repository URL/location

* **branch** [string] *optional*
	Repository branch (if any)

* **username** [string] *optional*
	Repository username

* **password** [string] *optional*
	Repository password


#### environments
This section is where you define your server groups or environments. Each environment has a unique name and a list of
servers. A server must have a **host** property and a deployment **strategy** consisting of a comma
separated list of tasks to run (in the order specified).

For example:


```yml
environments:
	production:
	    strategy: release, link, prune
		servers:
			- host: web-001.example.com
			- host: web-002.example.com
			- host: web-003.example.com
```
				

Within each environment, you may override some or all global options. For example, if your *staging* environment
has a different set of SSH keys used to authenticate and uses the *staging* branch of your repo, you can define those
changes within the *staging* environment.

For example:

```yml
environments:
	staging:
	    strategy: release, link, prune
		code:
			branch: staging
		authentication:
			username: deploy
			key: staging_id_rsa.pub
		servers:
			- host: staging-001.example.com
			- host: staging-002.example.com
```

A server may also override the environment strategy. This is useful if you have several servers
and need to run a migration as it only needs to be run once.

For example:

```yml
environments:
	staging:

		code:
			branch: staging
			
		authentication:
			username: deploy
			key: staging_id_rsa.pub
			
		strategy: release, link, prune
			
		servers:
			- host: staging-001.example.com
			  strategy: release, link, migrate, prune
			- host: staging-002.example.com
```

## Tasks
A task is one or more shell commands that are issued on the remote server. **minion** is pre-configured with four tasks.

* `release` Creates a new release on the server.
* `link` Symlinks the newly created release to the current release directory (`current` by default).
* `prune` Prunes (deletes) old release directories.
* `update` Does a code update on the current release (`git pull` or `svn up`)

## Extending
**minion** can be extended by creating new custom commands as well as custom tasks.

#### Commands
To create a new command:

`minion make:command <name>`

For example:

`minion make:command CacheCommand`

Custom commands can be found in the `Commands` directory where you run **minion** from.

**minion** commands are powered by **Symfony Console**. Please refer to [http://symfony.com/doc/current/components/console.html]
for documentation.

#### Tasks

To create a new task:

`minion make:task <name>`

For example:

`minion make:task Migrate`

Custom tasks can be found in the `Tasks` directory where you run **minion** from. 