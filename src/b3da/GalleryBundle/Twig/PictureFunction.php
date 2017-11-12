<?php

namespace b3da\GalleryBundle\Twig;

class PictureFunction extends \Twig_Extension
{
    /**
     * @var string $galleryDir
     */
    protected $galleryDir;

    public function __construct($galleryDir) {
        $this->galleryDir = $galleryDir;
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('picture', [$this, 'createPicture'], ['is_safe' => ['html']])
        ];
    }

    public function createPicture($galleryId, $imageFilename) {
        // todo: refactor
        $urlL = $this->galleryDir . '/' . $galleryId . '/1920/' . $imageFilename;
        $urlM = $this->galleryDir . '/' . $galleryId . '/1280/' . $imageFilename;
        $urlS = $this->galleryDir . '/' . $galleryId . '/640/' . $imageFilename;
        return '<picture>
                  <source srcset="' . $urlL . '" media="(min-width: 1280px)">
                  <source srcset="' . $urlM . '" media="(min-width: 640px)">
                  <source srcset="' . $urlS . '" media="(min-width: 200px)">
                  <img src="' . $urlS . '" alt="fallback image">
                </picture>';
    }

    public function getName() {
        return 'picture';
    }
}
