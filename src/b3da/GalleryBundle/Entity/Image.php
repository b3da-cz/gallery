<?php

namespace b3da\GalleryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="image")
 */
class Image
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $filename;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateCreated;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $width;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $height;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $mainColor;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isSpherical;

    /**
     * @ORM\OneToMany(targetEntity="Visit", mappedBy="image")
     */
    private $visits;


    public function __construct() {
        $this->visits = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title ? $this->title : 'image ' . $this->id;
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
     * Set position
     *
     * @param integer $position
     *
     * @return Image
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return Image
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
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
     * Set title
     *
     * @param string $title
     *
     * @return Image
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
     * Set description
     *
     * @param string $description
     *
     * @return Image
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Image
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
     * Set width
     *
     * @param integer $width
     *
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set mainColor
     *
     * @param string $mainColor
     *
     * @return Image
     */
    public function setMainColor($mainColor)
    {
        $this->mainColor = $mainColor;

        return $this;
    }

    /**
     * Get mainColor
     *
     * @return string
     */
    public function getMainColor()
    {
        return $this->mainColor;
    }

    /**
     * Set isSpherical
     *
     * @param boolean $isSpherical
     *
     * @return Image
     */
    public function setIsSpherical($isSpherical)
    {
        $this->isSpherical = $isSpherical;

        return $this;
    }

    /**
     * Get isSpherical
     *
     * @return boolean
     */
    public function getIsSpherical()
    {
        return $this->isSpherical;
    }

    /**
     * Add visit
     *
     * @param \b3da\GalleryBundle\Entity\Visit $visit
     *
     * @return Image
     */
    public function addVisit(\b3da\GalleryBundle\Entity\Visit $visit)
    {
        $visit->setImage($this);
        $this->visits[] = $visit;

        return $this;
    }

    /**
     * Remove visit
     *
     * @param \b3da\GalleryBundle\Entity\Visit $visit
     */
    public function removeVisit(\b3da\GalleryBundle\Entity\Visit $visit)
    {
        $visit->setImage(null);
        $this->visits->removeElement($visit);
    }

    /**
     * Get visits
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * Get visitCount
     *
     * @return integer
     */
    public function getVisitCount()
    {
        return count($this->visits);
    }
}
