<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 1:00 PM
 */

namespace minion;



use League\CLImate\CLImate;

class Console {

	private static $_self = null;

	/** @var CLImate */
	private $_console = null;

	public static function getInstance() {

		if( self::$_self === null ) {
			self::$_self = new self;
			self::$_self->_console = new CLImate;
		}

		return self::$_self->_console;

	}

}