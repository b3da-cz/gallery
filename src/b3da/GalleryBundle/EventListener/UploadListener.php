<?php

namespace b3da\GalleryBundle\EventListener;

use b3da\GalleryBundle\Entity\Gallery;
use b3da\GalleryBundle\Entity\Image;
use b3da\GalleryBundle\Util\ImageResizer;
use Doctrine\ORM\EntityManager;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadListener
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ImageResizer
     */
    private $imageResizer;

    /**
     * @var string
     */
    private $galleryDir;

    public function __construct(EntityManager $em, $imageResizer, $galleryDirectory)
    {
        $this->em = $em;
        $this->imageResizer = $imageResizer;
        $this->galleryDir = $galleryDirectory;
    }

    public function onUpload(PostPersistEvent $event)
    {
        $request = $event->getRequest();
        $galleryId = $request->query->get('galleryId');
        $gallery = $this->em->getRepository(Gallery::class)->find($galleryId);
        if (!$gallery) {
            $response = $event->getResponse();
            $response['error'] = true;
            $response['data'] = 'no gallery found by id ' . $galleryId;
            return $response;
        }
        /** @var UploadedFile $file */
        $file = $event->getFile();
        $image = new Image();
        $image->setDateCreated(new \DateTime());
        $image->setFilename($file->getFileName());

        $this->makeThumbnails($image, $galleryId);

        $resized1920pximagePath = $this->galleryDir . '/' . $galleryId . '/1920/' . $image->getFilename();
        $imageSizes = getimagesize($resized1920pximagePath);
        list($x, $y) = $imageSizes;
        $image->setWidth($x);
        $image->setHeight($y);

        $allImagesInGallery = $gallery->getImages();
        if(count($allImagesInGallery) > 0) {
            $image->setPosition($allImagesInGallery[count($allImagesInGallery) - 1]->getPosition() + 10);
        } else {
            $image->setPosition(1);
        }
        $gallery->addImage($image);


        try {
            $this->em->persist($image);
//            $this->em->flush();
            $image->setMainColor($this->imageResizer->getMainColorForImage($this->galleryDir . '/' . $galleryId . '/640/' . $image->getFilename()));
            $this->em->flush();
        } catch (\Exception $e) {
            $response = $event->getResponse();
            $response['error'] = true;
            $response['data'] = 'error when persisting image ' . $image->getFilename() . ', msg: ' . $e->getMessage();
            return $response;
        }
        $response = $event->getResponse();
        $response['success'] = true;
        return $response;
    }

    protected function makeThumbnails(Image $image, $galleryId) {
        $sizes = [
            640,
            1280,
            1920,
        ];
        foreach ($sizes as $size) {
            $fullImagePath = $this->galleryDir . '/' . $galleryId . '/' . $image->getFilename();
            $resizedFullImagePath = $this->galleryDir . '/' . $galleryId . '/' . $size . '/' . $image->getFilename();
            $this->imageResizer->resizeImageExternally(
                $fullImagePath,
                $resizedFullImagePath,
                $size,
                0,
                false,
                true
            );
        }
    }
}
