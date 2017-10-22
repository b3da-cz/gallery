<?php

namespace b3da\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gallery")
 */
class Gallery
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateCreated;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $thumbScale;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $thumbMargin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isPublic;

    /**
     * @ORM\ManyToMany(targetEntity="Image", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="gallery_image",
     *      joinColumns={@ORM\JoinColumn(name="gallery_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $images;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist"})
     * @ORM\JoinColumn(name="front_image_id", referencedColumnName="id")
     */
    protected $frontImage;

    public function __construct() 
    {
        $this->thumbScale = 1.3;
        $this->thumbMargin = 4;
        $this->images = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Gallery
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Gallery
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Gallery
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return float
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set thumbScale
     *
     * @param float $thumbScale
     *
     * @return Gallery
     */
    public function setThumbScale($thumbScale)
    {
        $this->thumbScale = $thumbScale;

        return $this;
    }

    /**
     * Get thumbScale
     *
     * @return float
     */
    public function getThumbScale()
    {
        return $this->thumbScale;
    }

    /**
     * Set thumbMargin
     *
     * @param float $thumbMargin
     *
     * @return Gallery
     */
    public function setThumbMargin($thumbMargin)
    {
        $this->thumbMargin = $thumbMargin;

        return $this;
    }

    /**
     * Get thumbMargin
     *
     * @return integer
     */
    public function getThumbMargin()
    {
        return $this->thumbMargin;
    }

    /**
     * Set isPublic
     *
     * @param boolean $isPublic
     *
     * @return Gallery
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic
     *
     * @return boolean
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Add image
     *
     * @param \b3da\GalleryBundle\Entity\Image $image
     *
     * @return Gallery
     */
    public function addImage(\b3da\GalleryBundle\Entity\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \b3da\GalleryBundle\Entity\Image $image
     */
    public function removeImage(\b3da\GalleryBundle\Entity\Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Check for image
     *
     * @param \b3da\GalleryBundle\Entity\Image $image
     *
     * @return boolean
     */
    public function isImageInGallery(\b3da\GalleryBundle\Entity\Image $image)
    {
        return $this->images->contains($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set frontImage
     *
     * @param \b3da\GalleryBundle\Entity\Image $frontImage
     *
     * @return Gallery
     */
    public function setFrontImage(\b3da\GalleryBundle\Entity\Image $frontImage = null)
    {
        $this->frontImage = $frontImage;

        return $this;
    }

    /**
     * Get frontImage
     *
     * @return \b3da\GalleryBundle\Entity\Image
     */
    public function getFrontImage()
    {
        return $this->frontImage;
    }
}
