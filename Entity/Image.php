<?php

namespace Burwieck\IphotoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Burwieck\IphotoBundle\Entity\Image
 *
 * @ORM\Table(name="iphoto___images")
 * @ORM\Entity(repositoryClass="Burwieck\IphotoBundle\Entity\ImageRepository")
  */
class Image
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string $caption
     *
     * @ORM\Column(name="caption", type="string", length=255)
     */
    private $caption;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable="true")
     */
    private $comment;

    /**
     * @var string $filename
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var string $rating
     *
     * @ORM\Column(name="rating", type="integer", nullable="true")
     */
    private $rating;

    /**
     * @var string $ratio
     *
     * @ORM\Column(name="rato", type="float")
     */
    private $ratio;

    /**
     * @var ArrayCollection $faces
     *
     * @ORM\OneToMany(targetEntity="ImageHasFace", mappedBy="image", cascade={"persist"})
     */
    private $faces;

    /**
     * @var ArrayCollection $albums
     *
     * @ORM\ManyToMany(targetEntity="Album", cascade={"persist"})
     * @ORM\JoinTable(name="iphoto___image_has_album") 
     */
    private $albums;

    /**
     * @var ArrayCollection $keywords
     *
     * @ORM\ManyToMany(targetEntity="Keyword")
     * @ORM\JoinTable(name="iphoto___image_has_keyword") 
     */
    private $keywords;    

    /**
    * @Gedmo\Slug(fields={"caption"})
    * @ORM\Column(length=64, unique=true)
    */
    private $slug;

    /**
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", nullable=true, type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * @var datetime $updated_at
     *
     * @ORM\Column(name="updated_at", nullable=true, type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated_at;


    public function __construct() 
    {
        $this->faces = new ArrayCollection();
        $this->albums = new ArrayCollection();
        $this->keywords = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer 
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
     * Set caption
     *
     * @param string $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * Get caption
     *
     * @return string 
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set comment
     *
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
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
     * Set rating
     *
     * @param integer $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set ratio
     *
     * @param float $ratio
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;
    }

    /**
     * Get ratio
     *
     * @return float 
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * add face
     *
     * @param \Burwieck\IphotoBundle\Entity\ImageHasFace $face 
     */
    public function addFace(\Burwieck\IphotoBundle\Entity\ImageHasFace $face)
    {
        $this->faces[] = $face;
    }

    /**
     * Get faces
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFaces()
    {
        return $this->faces;
    }

    /**
     * add album
     *
     * @param \Burwieck\IphotoBundle\Entity\Album $album 
     */
    public function addAlbum(\Burwieck\IphotoBundle\Entity\Album $album)
    {
        $this->albums[] = $album;
        $album->addImage($this);
    }

    /**
     * Get albums
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAlbums()
    {
        return $this->albums;
    }    

    /**
     * add keyword
     *
     * @param \Burwieck\IphotoBundle\Entity\Keyword $keyword 
     */
    public function addKeyword(\Burwieck\IphotoBundle\Entity\Keyword $keyword)
    {
        $this->keywords[] = $keyword;
    }

    /**
     * Get keywords
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getKeywords()
    {
        return $this->keywords;
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
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
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