<?php

namespace Burwieck\IphotoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Burwieck\IphotoBundle\Entity\ImageHasFace
 *
 * @ORM\Table(name="iphoto___image_has_face")
 * @ORM\Entity()
 */
class ImageHasFace
{
    /**
     * @var Image $image
     * 
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="faces")
     * @ORM\id
     */
    private $image;

    /**
     * @var Face $face
     * 
     * @ORM\ManyToOne(targetEntity="Face")
     * @ORM\id
     */
    private $face;

    /**
     * @var string $rect
     *
     * @ORM\Column(name="rect", type="string", length=80, nullable="true")
     */
    private $rect;

    /**
     * Set image
     *
     * @param Burwieck\IphotoBundle\Entity\Image $image
     */
    public function setImage(\Burwieck\IphotoBundle\Entity\Image $image)
    {
        $this->image = $image;
    }

    /**
     * Get Image
     *
     * @return Burwieck\IphotoBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set face
     *
     * @param Burwieck\IphotoBundle\Entity\Face $face
     */
    public function setFace(\Burwieck\IphotoBundle\Entity\Face $face)
    {
        $this->face = $face;
    }

    /**
     * Get Face
     *
     * @return Burwieck\IphotoBundle\Entity\Face
     */
    public function getFace()
    {
        return $this->face;
    }    

    /**
     * Set rect
     *
     * @param string $ect
     */
    public function setRect($rect)
    {
        $this->rect = $rect;
    }

    /**
     * Get rect
     *
     * @return string 
     */
    public function getRect()
    {
        return $this->rect;
    }
}