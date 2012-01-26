<?php

namespace Burwieck\IphotoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Burwieck\IphotoBundle\Entity\Album
 *
 * @ORM\Table(name="iphoto___albums")
 * @ORM\Entity(repositoryClass="Burwieck\IphotoBundle\Entity\AlbumRepository")
  */
class Album
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var ArrayCollection $images
     *
     * @ORM\OneToMany(targetEntity="Image", mappedBy="albums")
     * @ORM\JoinTable(name="iphoto___image_has_album")
     */
    private $images;        

    /**
    * @Gedmo\Slug(fields={"name"})
    * @ORM\Column(length=64, unique=true)
    */
    private $slug;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=255, nullable="true")
     */
    private $type;

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


    public function __construct() 
    {
        $this->images = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @return integer 
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
     * Get images
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add image
     *
     * @param Burwieck\IphotoBundle\Entity\Image $image
     */
    public function addImage(\Burwieck\IphotoBundle\Entity\Image $image)
    {
        $this->images[] = $image;
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
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
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