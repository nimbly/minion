## Usage
**minion \<command\> [arguments]**

**minion** expects a YML config file named **minion.yml** to be in the directory where you run minion from.

## Commands
* **deploy** Deploy a new release
* **task** Update current release without doing a deploy
* **rollback** Remove current release and rollback to previous


## minion.yml
The configuration file format for **minion** is YML.

The first several sections of the config file define *global* config options. Each environment defined may optionally
override these global options. 

The global sections are:

* remote
* authentication
* code


#### remote
The remote section defines options on the remote server environment including where to deploy code and what method should
be used to deploy code.

The options for the *remote* sections are as follows:

* **method** [release, update]

	What method **minion** should use to deploy code.
	
	**release** creates a new directory with the release timestamp as its name
	and clones the repo defined in the **code** section. It then creates a symlink **current** to point to this newly created
	release directory. If you use this method, you will need to update your web server configuration to point to the **current**
	directory as its web root.
	
	**update** simply updates the existing code base using the SCM's update
	command (git pull or svn up).

* **keepReleases** [integer]

	If **method** is **release**, the number of releases to keep before pruning old directories.
	
* **path** [string]

	The absolute path to the code on the server. I.e. the path to deploy code to.


#### authentication
The authentication section defines how to SSH authenticate with your servers. You can either authenticate with a username
and password *or* using a username, key and (optionally) a key pass phrase.

**Please note** because **minion** uses PHPSECLIB as its core SSH library, you can **only** use RSA keys - DSA keys are
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
servers. A server must have a **host** property and optionally a **migrate** property. The **migrate** property, if set
and **true**, will trigger a database migration to happen on that server after the deploy.

For example:


```yml
environments:
	production:
	    strategy: deploy, permissions, cleanup
		servers:
			- host: web-001.example.com
			  migrate: true
				
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
 
		code:
			branch: staging
			
		authentication:
			username: deploy
			key: staging_id_rsa.pub
			
		servers:
			- host: staging-001.example.com
			  migrate: true
			- host: staging-002.example.com
```

## Customizing
**minion** can be extended by creating new custom commands and actions as well as custom tasks.

New commands should be placed in the **commands** directory and tasks in the **tasks** directory.
