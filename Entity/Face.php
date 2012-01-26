<?php

namespace Burwieck\IphotoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Burwieck\IphotoBundle\Entity\Face
 *
 * @ORM\Table(name="iphoto___faces")
 * @ORM\Entity(repositoryClass="Burwieck\IphotoBundle\Entity\FaceRepository")
  */
class Face
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", unique="true")
     * @ORM\Id
     */
    private $id;

    /**
     * @var ArrayCollection $images
     *
     * @ORM\OneToMany(targetEntity="ImageHasFace", mappedBy="face", cascade={"persist"})
     */
    private $images;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $filename
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var string $rect
     *
     * @ORM\Column(name="rect", type="string", length=255)
     */
    private $rect;

    /**
    * @Gedmo\Slug(fields={"name"})
    * @ORM\Column(length=64, unique=true)
    */
    private $slug;

    /**
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * @var datetime $updated_at
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated_at;

    /**
     * set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set filename
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set rect
     *
     * @param string $rect
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

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }    

    /**
     * Get images
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }    

    /**
     * Get image_id
     *
     * @return integer 
     */
    public function getImageId()
    {
        return $this->image_id;
    }

    /**
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }    

}