<?php

namespace b3da\GalleryBundle\Controller;

use b3da\GalleryBundle\Entity\Gallery;
use b3da\GalleryBundle\Entity\Image;
use b3da\GalleryBundle\Entity\Visit;
use b3da\GalleryBundle\Form\Type\GalleryType;
use b3da\GalleryBundle\Form\Type\ImageType;
use b3da\GalleryBundle\Form\Type\PasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="b3gallery.admin.index")
     */
    public function indexAction()
    {
        $galleries = $this->getDoctrine()->getRepository(Gallery::class)->findBy([],['position' => 'ASC']);
        $allVisits = $this->getDoctrine()->getRepository(Visit::class)->findAll();
        $countAllVisits = count($allVisits);
        $countGalleriesPublic = 0;
        $countGalleriesLocked = 0;
        $countGalleriesPrivate = 0;
        foreach ($galleries as $gallery) {
            if ($gallery->getIsPublic()) {
                $countGalleriesPublic++;
            } else {
                $countGalleriesPrivate++;
            }
            if ($gallery->getPassword() > '') {
                $countGalleriesLocked++;
            }
        }
        return $this->render('b3daGalleryBundle:Admin:index.html.twig', [
            'galleries' => $galleries,
            'countAllVisits' => $countAllVisits,
            'countGalleriesPublic' => $countGalleriesPublic,
            'countGalleriesPrivate' => $countGalleriesPrivate,
            'countGalleriesLocked' => $countGalleriesLocked,
        ]);
    }

    /**
     * @Route("/list", name="b3gallery.admin.list")
     */
    public function listAction()
    {
        return $this->render('b3daGalleryBundle:Admin:list.html.twig', [
            'galleries' => $this->getDoctrine()->getRepository(Gallery::class)->findBy([],['position' => 'ASC']),
        ]);
    }

    /**
     * @Route("/gallery/{id}", defaults={"id" = ""}, name="b3gallery.admin.gallery")
     */
    public function galleryAction(Request $request, $id)
    {
        if ($id > 0) {
            $gallery = $this->getDoctrine()->getRepository(Gallery::class)->find($id);
        } else {
            $gallery = new Gallery();
        }
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$gallery->getId()) {
                $gallery->setDateCreated(new \DateTime());
                $allGalleries = $this->getDoctrine()->getRepository(Gallery::class)->findAll();
                if(count($allGalleries) > 0) {
                    $gallery->setPosition($allGalleries[count($allGalleries) - 1]->getPosition() + 10);
                } else {
                    $gallery->setPosition(1);
                }
            }

            $em = $this->getDoctrine()->getManager();
            try {
                if (!$gallery->getId()) {
                    $em->persist($gallery);
                }
                $em->flush();
                return $this->redirectToRoute('b3gallery.admin.gallery', ['id' => $gallery->getId()]);
            } catch (\Exception $e) {
                exit(dump($e));
            }
        }
        return $this->render('b3daGalleryBundle:Admin:gallery.html.twig', [
            'gallery' => $gallery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gallery/{id}/lock", name="b3gallery.admin.gallery_lock")
     */
    public function galleryLockAction(Request $request, $id)
    {
        $gallery = $this->getDoctrine()->getRepository(Gallery::class)->find($id);
        $form = $this->createForm(PasswordFormType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $flushNeeded = false;
            $plainPass = $form->getData()['password'];
            if ($gallery->getPassword() > '' && !$plainPass) {
                $gallery->setPassword(null);
                $flushNeeded = true;
            }
            if ($plainPass > '') {
                // todo: bCrypt is better option here, maybe create PasswordEncoder service?
                $hash = hash('sha256', $this->getParameter('gallery_password_salt') . $plainPass);
                $gallery->setPassword($hash);
                $flushNeeded = true;
            }

            if ($flushNeeded) {
                $em = $this->getDoctrine()->getManager();
                try {
                    $em->flush();
                    return $this->redirectToRoute('b3gallery.admin.gallery', ['id' => $gallery->getId()]);
                } catch (\Exception $e) {
                    exit(dump($e));
                }
            } else {
                return $this->redirectToRoute('b3gallery.admin.gallery', ['id' => $gallery->getId()]);
            }
        }
        return $this->render('b3daGalleryBundle:Admin:galleryLock.html.twig', [
            'gallery' => $gallery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gallery/{id}/preview", name="b3gallery.admin.gallery_preview")
     */
    public function galleryPreviewAction($id)
    {
        return $this->render('b3daGalleryBundle:Admin:preview.html.twig', [
            'gallery' => $this->getDoctrine()->getRepository(Gallery::class)->find($id),
        ]);
    }

    /**
     * @Route("/gallery/{galleryId}/image/{imageId}", name="b3gallery.admin.image_detail")
     */
    public function imageDetailAction(Request $request, $galleryId, $imageId)
    {
        $image = $this->getDoctrine()->getRepository(Image::class)->find($imageId);
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->flush();
                return $this->redirectToRoute('b3gallery.admin.gallery', ['id' => $galleryId]);
            } catch (\Exception $e) {
                exit(dump($e));
            }
        }
        return $this->render('b3daGalleryBundle:Admin:image.html.twig', [
            'galleryId' => $galleryId,
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gallery/{galleryId}/frontimage/{imageId}", name="b3gallery.admin.gallery_frontimage")
     */
    public function setGaleryFrontImageAction($galleryId, $imageId)
    {
        $gallery = $this->getDoctrine()->getRepository(Gallery::class)->find($galleryId);
        $image = $this->getDoctrine()->getRepository(Image::class)->find($imageId);
        $gallery->setFrontImage($image);
        $em = $this->getDoctrine()->getManager();
        try {
            $em->flush();
            return $this->redirectToRoute('b3gallery.admin.gallery', ['id' => $galleryId]);
        } catch (\Exception $e) {
            exit(dump($e));
        }
    }

    /**
     * @Route("/gallery/{galleryId}/thumb-setting/{scale}/{margin}", name="b3gallery.admin.gallery_thumb_setting")
     */
    public function setGaleryThumbSettingAction($galleryId, $scale, $margin)
    {
        $gallery = $this->getDoctrine()->getRepository(Gallery::class)->find($galleryId);
        $gallery->setThumbScale($scale);
        $gallery->setThumbMargin($margin);
        $em = $this->getDoctrine()->getManager();
        try {
            $em->flush();
            return $this->redirectToRoute('b3gallery.admin.gallery', ['id' => $galleryId]);
        } catch (\Exception $e) {
            exit(dump($e));
        }
    }

    /**
     * @Route("/gallery/{galleryId}/exif/{imageId}", name="b3gallery.admin.exif")
     */
    public function exifAction($galleryId, $imageId)
    {
        $image = $this->getDoctrine()->getRepository(Image::class)->find($imageId);
        $fullImagePath = $this->getParameter('gallery_directory') . '/' . $galleryId . '/1920/' . $image->getFilename();
        $exif = exif_read_data($fullImagePath, 'ANY_TAG');
        exit(dump($exif));
//        try {
//            $em->flush();
//            return $this->redirectToRoute('b3gallery.admin.gallery', ['id' => $galleryId]);
//        } catch (\Exception $e) {
//            exit(dump($e));
//        }
    }

    /**
     * @Route("/gallery/{galleryId}/image/delete/{imageId}", name="b3gallery.admin.image_delete")
     */
    public function imageDeleteAction($galleryId, $imageId)
    {
        $gallery = $this->getDoctrine()->getRepository(Gallery::class)->find($galleryId);
        $image = $this->getDoctrine()->getRepository(Image::class)->find($imageId);
        if ($gallery->isImageInGallery($image)) {
            $gallery->removeImage($image);
        }
        if ($gallery->getFrontImage() && $gallery->getFrontImage()->getId() === $image->getId()) {
            $gallery->setFrontImage(null);
        }
        $fullImagePath = $this->getParameter('gallery_directory') . '/' . $galleryId . '/' . $image->getFilename();
        $em = $this->getDoctrine()->getManager();
        try {
            $em->remove($image);
            $em->flush();
//            if ($this->getParameter('gallery_keep_original_uploads')) {
                @unlink($fullImagePath);
//            }
            $this->deleteThumbnails($image, $galleryId);
            return $this->redirectToRoute('b3gallery.admin.gallery', ['id' => $galleryId]);
        } catch (\Exception $e) {
            exit(dump($e));
        }
    }

    protected function deleteThumbnails(Image $image, $galleryId) {
        $sizes = [
            640,
            1280,
            1920,
        ];
        if ($image->getIsSpherical()) {
            $sizes[] = 'sphere';
        }
        try {
            foreach ($sizes as $size) {
                $imagePath = $this->getParameter('gallery_directory') . '/' . $galleryId . '/' . $size . '/' . $image->getFilename();
                @unlink($imagePath);
            }
            return true;
        } catch (\Exception $e) {
//            exit(dump($e->getMessage()));
            return false;
        }
    }
}
