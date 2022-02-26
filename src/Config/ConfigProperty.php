<?php

namespace minion\Config;

abstract class ConfigProperty
{
	public function __construct(array $data = [])
	{
		if( $data ) {
			foreach( $data as $property => $value ) {
				if( \property_exists($this, $property) ) {
					$this->{$property} = $value;
				}
			}
		}
	}

	public function toArray(): array
	{
		$array = [];
		foreach( \get_object_vars($this) as $property ) {
			$array[$property] = $this->{$property};
		}

		return $array;
	}
}