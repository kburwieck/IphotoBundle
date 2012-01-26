<?php

namespace Burwieck\IphotoBundle\Data\iPhotoData\Types;

class PlistArray
{

	protected $value = array();

	public function append($value)
	{
		$this->value[] = $value;		
	}

	public function getCount()
	{
		return count($this->value);
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
