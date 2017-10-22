<?php

namespace b3da\GalleryBundle\Controller;

use b3da\GalleryBundle\Entity\Gallery;
use b3da\GalleryBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


class FrontController extends Controller
{
    /**
     * @Route("/", name="b3gallery.front.index")
     */
    public function indexAction()
    {
        return $this->render('b3daGalleryBundle:Front:index.html.twig', [
            'galleries' => $this->getDoctrine()->getRepository(Gallery::class)->findBy([
                'isPublic' => true,
            ], [
                'position' => 'ASC',
            ]),
        ]);
    }

    /**
     * @Route("/gallery/{id}", name="b3gallery.front.gallery")
     */
    public function galleryAction($id)
    {
        $gallery = $this->getDoctrine()->getRepository(Gallery::class)->find($id);
        if (!$gallery || !$gallery->getIsPublic()) {
            return new JsonResponse(['error' => 'not found or denied'], 418);
        }
        if ($gallery->getPassword() > '') {
            $this->unlockGallery($gallery);
        }
        return $this->render('b3daGalleryBundle:Front:gallery.html.twig', ['gallery' => $gallery]);
    }

    /**
     * @Route("/gallery/{id}/json", name="b3gallery.front.gallery_json")
     */
    public function galleryJsonAction($id)
    {
        $gallery = $this->getDoctrine()->getRepository(Gallery::class)->find($id);
        if (!$this->getUser() && (!$gallery || !$gallery->getIsPublic())) {
            return new JsonResponse(['error' => 'not found or denied'], 418);
        }
        if ($gallery->getPassword() > '') {
            $this->unlockGallery($gallery);
        }
        $result = [];
        /** @var Image $image */
        foreach ($gallery->getImages() as $image) {
            $result[] = [
                'title' => $image->getTitle(),
                'description' => $image->getDescription(),
                'urlThumb' => $this->getParameter('twig_gallery_directory') . '/' . $id . '/640/' . $image->getFilename(),
                'url' => $this->getParameter('twig_gallery_directory') . '/' . $id . '/1920/' . $image->getFilename(),
                'width' => $image->getWidth(),
                'height' => $image->getHeight(),
                'mainColor' => $image->getMainColor() ? $image->getMainColor() : 'rgba(193, 193, 193, 0.65)',
            ];
        }
        return new JsonResponse($result);
    }

    protected function unlockGallery($gallery) {
        // todo: refactor && cleanup
        if (!isset($_SERVER['PHP_AUTH_PW'])) {
            header('WWW-Authenticate: Basic realm="b3gallery' . $gallery->getId() . $this->getParameter('gallery_http_realm') . '"');
            header('HTTP/1.0 401 Unauthorized');
            exit('{"error": "unauthorized"}');
        } else {
            // todo: bCrypt is better option here, maybe create PasswordEncoder service?
            $hash = hash('sha256', $this->getParameter('gallery_password_salt') . $_SERVER['PHP_AUTH_PW']);
            if ($gallery->getPassword() !== $hash) {
                header('WWW-Authenticate: Basic realm="b3gallery' . $gallery->getId() . $this->getParameter('gallery_http_realm') . '"');
                header('HTTP/1.0 401 Unauthorized');
                exit('{"error": "unauthorized"}');
            }
        }
    }
}
