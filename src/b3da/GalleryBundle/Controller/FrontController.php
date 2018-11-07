<?php

namespace b3da\GalleryBundle\Controller;

use b3da\GalleryBundle\Entity\About;
use b3da\GalleryBundle\Entity\Gallery;
use b3da\GalleryBundle\Entity\Image;
use b3da\GalleryBundle\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class FrontController extends Controller
{
    /**
     * @Route("/", name="b3gallery.front.index")
     */
    public function indexAction(Request $request)
    {
        $this->logVisit($request, null, null);
        $about = $this->getDoctrine()->getRepository(About::class)->find(1);
        return $this->render('b3daGalleryBundle:Front:index.html.twig', [
            'galleries' => $this->getDoctrine()->getRepository(Gallery::class)->findBy([
                'isPublic' => true,
            ], [
                'position' => 'ASC',
            ]),
            'about' => $about,
        ]);
    }

    /**
     * @Route("/gallery/{id}", name="b3gallery.front.gallery")
     */
    public function galleryAction(Request $request, $id)
    {
        $gallery = $this->getDoctrine()->getRepository(Gallery::class)->find($id);
        $this->logVisit($request, $gallery, null);
        if (!$gallery || !$gallery->getIsPublic()) {
            return new JsonResponse(['error' => 'not found or denied'], 418);
        }
        if ($gallery->getPassword() > '') {
            $this->unlockGallery($gallery);
        }
        $isSpherical = false;
        /** @var Image $image */
        foreach ($gallery->getImages() as $image) {
            if ($image->getIsSpherical()) {
                $isSpherical = true;
            }
        }
        return $this->render('b3daGalleryBundle:Front:gallery.html.twig', [
            'gallery' => $gallery,
            'isSpherical' => $isSpherical,
        ]);
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
                'urlThumb' => $this->generateUrl('b3gallery.front.image', [
                    'galleryId' => $gallery->getId(),
                    'imageId' => $image->getId(),
                    'size' => '640',
                ]),
                'url' => $this->generateUrl('b3gallery.front.image', [
                    'galleryId' => $gallery->getId(),
                    'imageId' => $image->getId(),
                    'size' => $image->getIsSpherical() ? 'sphere' : '1920',
                ]),
//                'urlThumb' => $this->getParameter('twig_gallery_directory') . '/' . $id . '/640/' . $image->getFilename(),
//                'url' => $this->getParameter('twig_gallery_directory') . '/' . $id . '/1920/' . $image->getFilename(),
                'width' => $image->getWidth(),
                'height' => $image->getHeight(),
                'mainColor' => $image->getMainColor() ? $image->getMainColor() : 'rgba(193, 193, 193, 0.65)',
                'isSpherical' => $image->getIsSpherical(),
            ];
        }
        return new JsonResponse($result);
    }

    /**
     * @Route("/gallery/{galleryId}/image/{imageId}/{size}", name="b3gallery.front.image")
     */
    public function imageAction(Request $request, $galleryId, $imageId, $size)
    {
        $image = $this->getDoctrine()->getRepository(Image::class)->find($imageId);
        if ($size > 1200) {
            $this->logVisit($request, null, $image);
        }
        $gallery = $this->getDoctrine()->getRepository(Gallery::class)->find($galleryId);
        if ($gallery->getPassword() > '') {
            $this->unlockGallery($gallery);
        }
        // todo: serve all images from this route
        if (!$image) {
            return new JsonResponse(['error' => 'not found or denied'], 418);
        }

        $fullImagePath = $this->getParameter('gallery_directory') . '/' . $galleryId . '/' . $size . '/' . $image->getFilename();
        $response = new BinaryFileResponse($fullImagePath);

        if ($request->query->get('d')) {
            $extension = explode('.', $image->getFilename());
            $extension = strtolower($extension[count($extension) - 1]);
            $mimeTypeGuesser = new FileinfoMimeTypeGuesser();
            if ($mimeTypeGuesser->isSupported()){
                $response->headers->set('Content-Type', $mimeTypeGuesser->guess($fullImagePath));
            } else {
                switch ($extension) {
                    case 'png':
                        $response->headers->set('Content-Type', 'image/png');
                        break;
                    case 'gif':
                        $response->headers->set('Content-Type', 'image/gif');
                        break;
                    case 'jpg':
                    case 'jpeg':
                        $response->headers->set('Content-Type', 'image/jpeg');
                        break;
                }
            }
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $this->getParameter('gallery_name') . '-gallery-' . $galleryId . '-image-' . $image->getId() . '.' . $extension
            );
        }
        return $response;
    }

    /**
     * @Route("/about", name="b3gallery.front.about")
     */
    public function aboutAction(Request $request)
    {
        $this->logVisit($request, null, null);
        $about = $this->getDoctrine()->getRepository(About::class)->find(1);
        if (!$about || !$about->getIsActive()) {
            return new JsonResponse(['error' => 'not found or denied'], 418);
        }
        return $this->render(
            $about->getHasSmallImage()
                ? 'b3daGalleryBundle:Front:aboutSmallImg.html.twig'
                : 'b3daGalleryBundle:Front:about.html.twig', [
                    'about' => $about,
            ]);
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

    protected function logVisit(Request $request, $gallery = null, $image = null) {
        $env = $this->container->get('kernel')->getEnvironment();
        if ($env !== 'prod') {
            return;
        }
        $visit = new Visit();
        $visit->setDate(new \DateTime());
        $visit->setIp($request->getClientIp());
        $visit->setRoute($request->getRequestUri());
        $ua = $this->getBrowser();
        $visit->setBrowser($ua['name']);
        $visit->setBrowserVersion($ua['version']);
        $visit->setOs($ua['platform']);
        $visit->setUserAgent($ua['userAgent']);
        if ($gallery) {
            $gallery->addVisit($visit);
        }
        if ($image) {
            $image->addVisit($visit);
        }
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($visit);
            $em->flush();
        } catch (\Exception $e) {
        }
    }

    /**
     * From: http://php.net/manual/en/function.get-browser.php#101125
     *
     * @return array
     */
    protected function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= '';

        if (preg_match('/linux/i', $u_agent)) { $platform = 'linux'; }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) { $platform = 'mac'; }
        elseif (preg_match('/windows|win32/i', $u_agent)) { $platform = 'windows'; }

        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        $i = count($matches['browser']);
        if ($i != 1) {
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){ $version= $matches['version'][0]; }
            else { $version= $matches['version'][1]; }
        }
        else { $version= $matches['version'][0]; }

        if ($version==null || $version=="") {$version="?";}

        return [
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern,
        ];
    }
}
