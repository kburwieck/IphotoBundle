<?php

namespace Burwieck\IphotoBundle\Data\iPhotoData;

use Burwieck\IphotoBundle\Data\iPhotoData\PlistReader;

/**
 * This file handles AlbumData.xml
 *
 * 
 */

class AlbumData
{

	/**
	 *
	 * @var Burwieck\IphotoBundle\Data\iPhotoData/PlistReader $reader
	 **/
	protected $reader;

	public function __construct(PlistReader $reader)
	{
		$this->reader = $reader;
	}

	/**
	 *
	 * set source file
	 *
	 * @param string $file
	 **/
	public function setSource($file)
	{
		if(is_dir($file)) {
			$file = $file . '/AlbumData.xml';
		}
		$this->reader->setFile($file);
	}

	/**
	 *
	 * return value by given key
	 *
	 * @return mixed $value
	 **/
	public function get($key)
	{
		return $this->reader->getValue($key);
	}

}
