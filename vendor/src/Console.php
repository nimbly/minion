<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/5/15
 * Time: 1:00 PM
 */

namespace minion;


use Bramus\Ansi\Ansi;
use Bramus\Ansi\Writers\StreamWriter;

class Console {

	private static $_self = null;

	/** @var Ansi */
	private $_console = null;

	public static function getInstance() {

		if( self::$_self === null ) {
			self::$_self = new self;
			self::$_self->_console = new Ansi(new StreamWriter());
		}

		return self::$_self->_console;

	}

}