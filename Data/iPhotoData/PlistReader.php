<?php

namespace Burwieck\IphotoBundle\Data\iPhotoData;

use Burwieck\IphotoBundle\Data\iPhotoData\PlistException,
	Burwieck\IphotoBundle\Data\iPhotoData\Types\PlistArray,
	Burwieck\IphotoBundle\Data\iPhotoData\Types\PlistDate,
	Burwieck\IphotoBundle\Data\iPhotoData\Types\PlistDict,
	Burwieck\IphotoBundle\Data\iPhotoData\Types\PlistBoolean,
	Burwieck\IphotoBundle\Data\iPhotoData\Types\PlistNumber,
	Burwieck\IphotoBundle\Data\iPhotoData\Types\PlistString;

/**
 * This file read the Apple Plist File and parse it in its types
 *
 * 
 */

class PlistReader
{

	/**
	 * values
	 *
	 * @var array $values
	 */
	protected $values = array();

	/**
	 * current filename to load
	 *
	 * @var string $file
	 */
	protected $file;

	/**
	 * flag wheather file is already loaded
	 *
	 * @var boolean $loaded
	 */
	protected $loaded = false;
	
	/**
	* known types
	* @var array
	*/
	protected $allowedTypes = array(
		'string',
		'real',
		'integer',
		'date',
		'true',
		'false',
		'data',
		'array',
		'dict'
	);

	public function __construct()
	{
	}

	/**
	* file to load
	* @param string $file 
	*/
	public function setFile($file)
	{
		if($this->file !== $file) {
			$this->file = $file;
			$this->loaded = false;
		}
	}

	/**
	* load file
	*/
	public function load()
	{
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        if (!$dom->load($this->file, defined('LIBXML_COMPACT') ? LIBXML_COMPACT : 0)) {
            throw new \InvalidArgumentException(implode("\n", $this->getXmlErrors()));
        }
        $dom->validateOnParse = true;
        $dom->normalizeDocument();
        libxml_use_internal_errors(false);

        $this->parse($dom->documentElement, $this);
        $this->loaded = true;
        return $this;
	}

	/**
	*
	* parse file and fills value property
	* 
	* @param DomNode $node
	* @param DomNode $parent
	*/
	private function parse(\DomNode $node, $parent)
	{
    	if($node->childNodes->length) {
		    foreach($node->childNodes as $childNode) {

		    	if(!in_array($childNode->nodeName, $this->allowedTypes)) {
		    		continue;
		    	}
			    	

			    $previousSibling = $childNode->previousSibling;
			    $key = null;

			    while($previousSibling && $previousSibling->nodeName == '#text' && $previousSibling->previousSibling) {
				    $previousSibling = $previousSibling->previousSibling;
				}

		      	if($previousSibling && $previousSibling->nodeName == 'key') {
			      	$key = $previousSibling->firstChild->nodeValue;
			    }

				switch($childNode->nodeName) {
					case 'array':
						$value = new PlistArray();
						$this->parse($childNode, $value);
						break;

					case 'dict':
						$value = new PlistDict();
						$this->parse($childNode, $value);
						break;

					case 'string':
						$value = new PlistString($childNode->nodeValue);
						break;

					case 'real':
						$value = new PlistNumber(floatval($childNode->nodeValue));
						break;

					case 'integer':
					  	$value = new PlistNumber(intval($childNode->nodeValue));
					  	break;

					case 'true':
						$value = new PlistBoolean($childNode->nodeName == 'true');
						break;

					case 'false':
					  	$value = new PlistBoolean($childNode->nodeName == 'true');
					  	break;

					
					default:
						throw new PlistException($node->nodeValue . ' not implemented');
				}

				if($parent instanceof PlistDict) {
					$parent->append($key, $value);
				} else {
					$parent->append($value);
				}
			}
    	}
    }

	/**
	*
	* append new value to root
	* 
	* @param mixed $value
	*/
    private function append($value)
    {
    	$this->values[] = $value;
    }

	/**
	*
	* return value with given key
	* 
	* @param string $key
	* @return mixed $value
	*/
    public function getValue($key) 
    {
    	if(!$this->loaded) {
    		$this->load();
    	}
    	
    	if(count($this->values) == 1) {
    		return $this->values[0]->getValue($key);
    	} else {
    		return $this->values;
    	}
    }

    /**
     * Retrieves libxml errors and clears them.
     *
     * @return array An array of libxml error strings
     */
    private function getXmlErrors()
    {
        $errors = array();
        foreach (libxml_get_errors() as $error) {
            $errors[] = sprintf('[%s %s] %s (in %s - line %d, column %d)',
                LIBXML_ERR_WARNING == $error->level ? 'WARNING' : 'ERROR',
                $error->code,
                trim($error->message),
                $error->file ? $error->file : 'n/a',
                $error->line,
                $error->column
            );
        }

        libxml_clear_errors();

        return $errors;
    }	

}
