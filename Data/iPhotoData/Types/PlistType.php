<?php

namespace Burwieck\IphotoBundle\Data\iPhotoData\Types;

abstract class PlistType
{
	protected $value;

	public function __construct($value = null)
	{
		if($value) {
			$this->setValue($value);
		}
	}

	public function setValue($value)
	{
		$this->value = $value;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function toArray()
	{
		return $this->getValue();
	}

	public function __toString()
	{
		return $this->value;
	}
}
