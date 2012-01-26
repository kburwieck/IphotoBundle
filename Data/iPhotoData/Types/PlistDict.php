<?php

namespace Burwieck\IphotoBundle\Data\iPhotoData\Types;

use Burwieck\IphotoBundle\Data\iPhotoData\Types\PlistArray;

class PlistDict implements \IteratorAggregate
{
	protected $value = array();

	public function getIterator() 
	{
        return new \ArrayIterator($this->value);
    }

	public function append($key, $value)
	{
		$this->value[$key] = $value;		
	}

	public function getValue($key)
	{
		if(array_key_exists($key, $this->value)) {
			return $this->value[$key];
		}
	}

	public function toArray() 
	{
		$result = array();

		foreach($this->value as $key => $value) {
			$result[$key] = $value->toArray();
		}
		return $result;
	}
}
