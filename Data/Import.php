<?php
/*
 * This file is part of the Burwieck iPhoto package.
 *
 * (c) Kai Tobias Burwieck <kai@burwieck.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Burwieck\IphotoBundle\Data;

use Burwieck\IphotoBundle\Data\iPhotoData\AlbumData,
	Symfony\Component\Finder\Finder,
	Symfony\Component\Filesystem\Filesystem,
	Burwieck\IphotoBundle\Entity;



class Import 
{

	/**
	 * @var container
	 */
	protected $container;

	/**
	 * @var Symfony\Component\Filesystem\Filesystem
	 */
	protected $fileSystem;

	/**
	 * @var string $targetPath
	 */
	protected $targetPath;

	/**
	 * @var Burwieck\IphotoBundle\Data\iPhotoData\AlbumData
	 */
	protected $albumData;

	/**
	 * @var array importConfig
	 */
	protected $importConfig;

	/**
	 * placeholer for import
	 * @var array imagesToImport
	 */
	protected $imagesToImport = array();

	/**
	 * placeholer for import
	 * @var array keywordsToImport
	 */
	protected $keywordsToImport = array();

	/**
	 * placeholer for import
	 * @var array facesToImport
	 */
	protected $facesToImport = array();

	/**
	 * placeholer for import
	 * @var array albumsToImport
	 */	
	protected $albumsToImport = array();


	public function __construct($container, AlbumData $albumData, $iphotoPath, $targetPath, $importConfig)
	{
		$this->container = $container;
		$this->albumData = $albumData;
		$this->targetPath = $targetPath;
		$this->albumData->setSource($iphotoPath);
		$this->importConfig = $importConfig;
		$this->fileSystem = new Filesystem();
	}

	/**
	 * get AlbumData Instance
	 *
	 * @return Burwieck\IphotoBundle\Data\iPhotoData\AlbumData
	 */
	public function getAlbumData()
	{
		return $this->albumData;
	}

    /**
     * Get Doctrine
     *
     * @return \Doctrine
     */
	private function getDoctrine()
	{
		return $this->container->get('doctrine');
	}

    /**
     * sets the target path
     *
     * @param string $path
     */
	public function setTargetPath($path)
	{
		$this->targetPath = $path;
	}

    /**
     * returns target path
     *
     * @return string $path
     */
	public function getTargetPath() 
	{
		return $this->targetPath;
	}

    /**
     * get config parameter 
     *
     * @var string $key
     * @var mixed $defaultValue
     * @return mixed
     */
	public function getImportConfig($key, $defaultValue = null)
	{
		$value = $defaultValue;
		if(array_key_exists($key, $this->importConfig)) {
			$value = $this->importConfig[$key];
		}
		return $value;
	}


	/**
	 * prepare import
	 *
	 * @return array
	 */
	public function prepareImport()
	{
		// holder for images, keywords and faces to import
		$this->imagesToImport = array();
		$this->keywordsToImport = array();
		$this->facesToImport = array();
		$this->albumsToImport = array();

		// get all albums from AlbumData
		$albums = $this->getAlbumData()->get('List of Albums')->toArray();

		// get all keywords from AlbumData
		$keywords = $this->getAlbumData()->get('List of Keywords')->toArray();
	
		// generate Array of KeywordIds to Import
		$importKeywordsIds = array();
		$configKeywords = $this->getImportConfig('keywords');
		foreach($configKeywords as $keyword)
		{
			$index = array_search($keyword, $keywords);
			if(null !== $index) {
				$importKeywordsIds[] = $index;
			}
		}

		// get all faces from AlbumData
		$faces = $this->getAlbumData()->get('List of Faces')->toArray();

		// generate Array of FaceIds to Import
		$importFaceIds = array();
		$configFaces = $this->getImportConfig('faces');
		foreach($faces as $face)
		{
			if(in_array($face['name'], $configFaces)) {
				$importFaceIds[] = $face['key'];
			}
		}

		$images = $this->getAlbumData()->get('Master Image List')->toArray();
		foreach($images as $key => $image) 
		{
			$importImage = false;

			// check for required keyword
			if(count($importKeywordsIds) && isset($image['Keywords'])) {
				foreach($image['Keywords'] as $keyword) {
					if(in_array($keyword, $importKeywordsIds)) {
						$importImage = true;
					}
				}
			}
			// check for required face
			if(count($importFaceIds) && isset($image['Faces'])) {
				foreach($image['Faces'] as $face) {
					if(in_array($face['face key'], $importFaceIds)) {
						$importImage = true;
					}
				}
			}
			
			// if no filter for import defined, import all images
			if(!count($configKeywords) && !count($configFaces)) {
				$importImage = true;
			}

			// image is valid for import
			if($importImage) {
				if(isset($image['Keywords'])) {
					foreach($image['Keywords'] as $keyword) {
						if(!in_array($keyword, $this->keywordsToImport)) {
							$this->keywordsToImport[] = $keyword;
						}
					}
				}
				if(isset($image['Faces'])) {
					foreach($image['Faces'] as $face) {
						if(!in_array($face['face key'], $this->facesToImport)) {
							// if face is defined
							if(isset($faces[$face['face key']])) {
								$this->facesToImport[] = $face['face key'];
							}
						}
					}
				}
				foreach($albums as $indexId => $album) {
					if(in_array($key, $album['KeyList']) && !in_array($indexId, $this->albumsToImport)) {
						$this->albumsToImport[] = $indexId;
					}
				}

				$this->imagesToImport[$key] = $image;

			}
		}
	}

    /**
     * starts import
     *
     * @return array
     */
	public function startImport()
	{
		set_time_limit(0);
		$entityManager = $this->getDoctrine()->getEntityManager();

		$albums = $this->getAlbumData()->get('List of Albums')->toArray();
		$keywords = $this->getAlbumData()->get('List of Keywords')->toArray();
		$faces = $this->getAlbumData()->get('List of Faces')->toArray();
		$images = $this->getAlbumData()->get('Master Image List')->toArray();

		$configKeywords = $this->getImportConfig('keywords');
		$configFaces = $this->getImportConfig('faces');

		// counter for import
		$importedImages = 0;
		$importedKeywords = 0;
		$importedFaces = 0;
		$importedAlbums = 0;

		// after how many images the entitymanger will presist the data
		$batchSize = 20;

		$a = 0;
		foreach($this->albumsToImport as $id)
		{
			$album = $albums[$id];
			if(isset($album['Album Type']) && $album['Album Type'] == 'Regular') {	
				$id = $album['AlbumId'];
				$dbAlbum = $entityManager->find("\Burwieck\IphotoBundle\Entity\Album", $id);
				if(!$dbAlbum) {
					++$importedAlbums;
					$dbAlbum = new \Burwieck\IphotoBundle\Entity\Album();
				}

				$dbAlbum->setId($id);
				$dbAlbum->setName($album['AlbumName']);
				$dbAlbum->setType(isset($album['Album Type']) ? $album['Album Type'] : null);
	            $entityManager->persist($dbAlbum);
	            ++$a;
	            $entityManager->flush();
	            $entityManager->clear();
	        }
		}

		// start import keywords in database
		$k = 0;
		foreach($this->keywordsToImport as $id)
		{
			$keyword = $keywords[$id];
			if(!in_array($keyword, $configKeywords)) {
				$dbKeyword = $entityManager->find("\Burwieck\IphotoBundle\Entity\Keyword", $id);
				if(!$dbKeyword) {
					++$importedKeywords;
					$dbKeyword = new \Burwieck\IphotoBundle\Entity\Keyword();
				}
				$dbKeyword->setId($id);
				$dbKeyword->setName($keyword);
	            $entityManager->persist($dbKeyword);
	            ++$k;
			}
            $entityManager->flush();
            $entityManager->clear();
		}

		// start import faces in database
		$f = 0;
		foreach($this->facesToImport as $id)
		{
			$face = $faces[$id];

			$dbFace = $entityManager->find("\Burwieck\IphotoBundle\Entity\Face", $id);
			if(!$dbFace) {
				++$importedFaces;
				$dbFace = new \Burwieck\IphotoBundle\Entity\Face();
			}
			$dbFace->setId($id);
			$dbFace->setName($face['name']);
			// find sand save its recangle.
			if(isset($face['key image']) && isset($images[$face['key image']])) {
				$image = $images[$face['key image']];
				$rect = '';
				foreach($images[$face['key image']]['Faces'] as $imageFace) {
					if($imageFace['face key'] == $id) {
						$rect = str_replace(array('{', '}', ' '), '', $imageFace['rectangle']);
						$dbFace->setRect($rect);
					}
				}
				$dbFace->setFilename($this->copyAndCropFaceImage($image['ImagePath'], $rect));
			}
            $entityManager->persist($dbFace);
            ++$f;

            $entityManager->flush();
            $entityManager->clear();
		}

		// start import in database
		$i = 0;
		foreach($this->imagesToImport as $id => $image)
		{
			// import image
			$dbImage = $this->findImage($id);
			if(!$dbImage) {
				++$importedImages;
				$dbImage = $this->saveImage($id, $image);
			}
			foreach($dbImage->getFaces() as $face) {
				$entityManager->remove($face);
			}
			$entityManager->flush();
			$dbImage->getFaces()->clear();

			if(isset($image['Faces'])) {
				foreach($image['Faces'] as $face) {
					if(in_array($face['face key'], $this->facesToImport)) {
						$dbFace = $entityManager->find("\Burwieck\IphotoBundle\Entity\Face", $face['face key']);
						if($dbFace) {
							$imageHasFace = new \Burwieck\IphotoBundle\Entity\ImageHasFace();
							$imageHasFace->setImage($dbImage);
							$imageHasFace->setFace($dbFace);
							$imageHasFace->setRect(str_replace(array('{', '}', ' '), '', $face['rectangle']));
							$dbImage->addFace($imageHasFace);
						}
					}
				}
			}

			$dbImage->getKeywords()->clear();
			if(isset($image['Keywords'])) {
				foreach($image['Keywords'] as $keywordId) {
					if(in_array($keywordId, $this->keywordsToImport)) {
						$dbKeyword = $entityManager->find("\Burwieck\IphotoBundle\Entity\Keyword", $keywordId);
						if($dbKeyword) {
							$dbImage->addKeyword($dbKeyword);
						}
					}
				}
			}

			$dbImage->getAlbums()->clear();
			foreach($albums as $album) {
				if(in_array($id, $album['KeyList'])) {
					$dbAlbum = $this->findAlbum($album['AlbumId'], $album);
					if($dbAlbum) {
						$dbImage->addAlbum($dbAlbum);
					}
				}
			}
			            
            $entityManager->persist($dbImage);
            if (($i % $batchSize) == 0) {
                $entityManager->flush();
                $entityManager->clear();
            }
            unset($dbImage);
            ++$i;
        }
        $entityManager->flush();
        $entityManager->clear();

        $this->result = array(
        	'images' => array(
	        	'count' => count($images),
	        	'new' => $importedImages
	        ),
	        'keywords' => array(
		        'count' => count($keywords),
		        'new' => $importedKeywords
		    ),
		    'faces' => array(
		    	'count' => count($faces),
		    	'new' => $importedFaces
			),
			'albums' => array(
		    	'count' => count($albums),
		    	'new' => $importedAlbums
			),
	    );
	}

	private function importAlbum($id, $album)
	{
		$dbAlbum = $this->findAlbum($id);
		if(!$dbAlbum) {
			$dbAlbum = $this->saveAlbum($id, $album);
		}
		return $dbAlbum;
	}	

	private function findAlbum($id)
	{
		return $this->getDoctrine()->getEntityManager()->find("\Burwieck\IphotoBundle\Entity\Album", $id);
	}

	private function saveAlbum($id, $album)
	{
        $dbAlbum = new \Burwieck\IphotoBundle\Entity\Album();
        $dbAlbum->setId($id);
        $dbAlbum->setName($album['AlbumName']);
        return $dbAlbum;
    }

	private function importImage($id, $image)
	{
		$dbImage = $this->findImage($id);
		if(!$dbImage) {
			$dbImage = $this->saveImage($id, $image);
		}
		return $dbImage;
	}

	private function findImage($id)
	{
		return $this->getDoctrine()->getEntityManager()->find("\Burwieck\IphotoBundle\Entity\Image", $id);
	}

	private function saveImage($id, $image)
	{
        $dbImage = new \Burwieck\IphotoBundle\Entity\Image();
        $dbImage->setId($id);
        $dbImage->setCaption($image['Caption']);
        $dbImage->setComment($image['Comment']);
        $dbImage->setRatio($image['Aspect Ratio']);
        $dbImage->setRating($image['Rating']);
        $dbImage->setFilename($this->copyImage($image['ImagePath']));
        $dbImage->setUpdatedAt(new \DateTime(date('Y-m-d H:i:s', floatval($image['DateAsTimerInterval'])  + 978307200)));
        $dbImage->setCreatedAt(new \DateTime(date('Y-m-d H:i:s', floatval($image['ModDateAsTimerInterval']) + 978307200)));
		return $dbImage;		
	}

	private function copyAndCropFaceImage($imagePath, $rect)
	{
		list($left, $top, $width, $height) = explode(',', $rect);

		$iPhotoPath = $this->getAlbumData()->get('Archive Path') . '';
		$targetPath = $this->getTargetPath() . '/faces';
		$relativePath = substr($imagePath, strlen($iPhotoPath));
		$webPath = realpath($this->container->get('kernel')->getRootDir() . '/../web');


		$source = $iPhotoPath . $relativePath;
		$target = $targetPath . $relativePath;

		// copy image to cache dir
		$cachePath = realpath($this->container->get('kernel')->getCacheDir());
		$pathInfo = pathinfo($imagePath);
		$cacheName = $cachePath . '/' . $pathInfo['basename'];
		$this->fileSystem->copy($source, $cacheName);

		// crop image at its rectangle
        $imagine = new \Imagine\Gd\Imagine();
        $image = $imagine->open($cacheName);
        $size = $image->getSize();

        $imageWidth = $size->getWidth();
        $imageHeight = $size->getHeight();

        $w = $imageWidth * $width;
        $h = $imageHeight * $height;

        $x = $imageWidth * $left;
        $y = $imageHeight - ($imageHeight * $top) - $h;

        if($w < 585) {
        	$x -= ((585 - $w) / 2);
        	$w = 585;
        } else {
        	$w = 585;
        }
        if($h < 585) {
        	$y -= ((585 - $h) / 2);
        	$h = 585;
        } else {
        	$h = 585;
        }

        if($x < 0) {
        	$w += ($x * -1);
        	$x = 0;
        }
        if($y < 0) {
        	$y += ($y * -1);
        	$y = 0;
        }
        // increase height
        /*
    	$x = $x > 100 ? $x - 100 : 0;
    	$y = $y - 100 > 0 ? $y - 100 : 0;
    	$w = $w + $x + 200 < $imageWidth ? $w + 200 : $w + ($imageWidth - $x - $w);
    	$h = $h + $y + 200 < $imageHeight ? $h + 200 : $h + ($imageHeight - $y - $h);
    	*/


        $faceImage = $image->crop(new \Imagine\Image\Point($x,$y), new \Imagine\Image\Box($w, $h));

        $this->fileSystem->mkdir(dirname($target));
        $faceImage->save($target);
        unlink($cacheName);

		return substr(realpath($targetPath), strlen($webPath)) . $relativePath;


	}

	private function copyImage($imagePath)
	{
		$iPhotoPath = $this->getAlbumData()->get('Archive Path') . '';
		$targetPath = $this->getTargetPath();
		$relativePath = substr($imagePath, strlen($iPhotoPath));

		$webPath = realpath($this->container->get('kernel')->getRootDir() . '/../web');

		$this->fileSystem->copy($iPhotoPath . $relativePath, $targetPath . $relativePath);
		return substr(realpath($targetPath), strlen($webPath)) . $relativePath;
	}

}
