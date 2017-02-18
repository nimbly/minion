<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 10/11/15
 * Time: 4:09 PM
 */

namespace minion\Config;

abstract class ConfigProperty {

	public function __construct(array $data = null) {

		if( $data ) {
			foreach( $data as $property => $value ) {
				if( property_exists($this, $property) ) {
					$this->{$property} = $value;
				}
			}
		}

	}

	public function toArray() {

		$array = [];
		foreach( get_object_vars($this) as $property ) {
			$array[$property] = $this->{$property};
		}

		return $array;

	}

}