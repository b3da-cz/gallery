<?php

namespace b3da\GalleryBundle\Util;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;


class GalleryNamer implements NamerInterface
{
    /**
     * @var Request $request
     */
    private $request;

    public function __construct(RequestStack $requestStack){
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Creates a gallery directory name for the file being uploaded.
     *
     * @param FileInterface $file
     * @return string
     */
    public function name(FileInterface $file)
    {
        $galleryId = $this->request->query->get('galleryId');
        return $galleryId . '/' . md5(uniqid()) . '.' . $file->getExtension();
    }
}
