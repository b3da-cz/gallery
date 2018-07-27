<?php

namespace b3da\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @ORM\Entity()
 * @ORM\Table(name="about")
 */
class About
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $heading;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $subheading;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $text;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isActive;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    protected $image;

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
     * @return About
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
     * Set heading
     *
     * @param string $heading
     *
     * @return About
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * Get heading
     *
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Set subheading
     *
     * @param string $subheading
     *
     * @return About
     */
    public function setSubheading($subheading)
    {
        $this->subheading = $subheading;

        return $this;
    }

    /**
     * Get subheading
     *
     * @return string
     */
    public function getSubheading()
    {
        return $this->subheading;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return About
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return About
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set image
     *
     * @param \b3da\GalleryBundle\Entity\Image $image
     *
     * @return About
     */
    public function setImage(\b3da\GalleryBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \b3da\GalleryBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
