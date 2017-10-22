<?php

namespace b3da\GalleryBundle\Util;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ImageResizer {
    public function resizeImageExternally($fullImagePath, $newFullImagePath, $widthOrSize, $height = 0, $squareCrop = false, $autoRotate = true) {
        $cmd = 'vipsthumbnail ' . $fullImagePath . ' -o ' . $newFullImagePath . '[Q=69] ';
//        $cmd = 'vipsthumbnail ' . $fullImagePath . ' -o ' . $newFullImagePath . ' '; // todo: non jpeg?
        if ($widthOrSize > 0 && $height > 0) {
            $cmd .= '-s ' . $widthOrSize . 'x' . $height . ' ';
        } elseif ($widthOrSize > 0 && $height === 0) {
            $cmd .= '-s ' . $widthOrSize . ' ';
        }
        if ($squareCrop) {
            $cmd .= '-c ';
        }
        if ($autoRotate) {
            $cmd .= '-t ';
        }

        $filesystem = new Filesystem();
        $resizedDir = explode('/', $newFullImagePath);
        array_pop($resizedDir);
        $resizedDir = join('/', $resizedDir);
        $filesystem->mkdir($resizedDir, 0777);

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }

    public function getMainColorForImage($fullImagePath) {
        $img = $this->imageCreateFromAll($fullImagePath);
        $thumb = imagecreatetruecolor(1,1);
        imagecopyresampled($thumb, $img, 0, 0, 0, 0, 1, 1, imagesx($img), imagesy($img));
//        $mainColor = strtoupper(dechex(imagecolorat($thumb, 0, 0)));
        $rgb = imagecolorat($img, 0, 0);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        return "rgb($r, $g, $b)";
    }

    /**
     * Resize an image
     *
     * @param string $image (The full image path with filename and extension)
     * @param string $newPath (The new path to where the image needs to be stored)
     * @param int $height (The new height to resize the image to)
     * @param int $width (The new width to resize the image to)
     * @param int $jpegQuality (The new jpeg quality)
     * @return string (The new path to the reized image)
     */
    public function resizeImage($image, $newPath, $height = 0, $width = 0, $jpegQuality = 75) {

        // Get current dimensions
        $ImageDetails = $this->getImageDetails($image);
        $name = $ImageDetails->name;
        $height_orig = $ImageDetails->height;
        $width_orig = $ImageDetails->width;
        $fileExtention = $ImageDetails->extension;
        $ratio = $ImageDetails->ratio;

        //Resize dimensions are bigger than original image, stop processing
        if ($width > $width_orig && $height > $height_orig) {
            return false;
        }

        if ($height > 0) {
            $width = $height * $ratio;
        } else if ($width > 0) {
            $height = $width / $ratio;
        }
        $width = round($width);
        $height = round($height);

        $gd_image_dest = imagecreatetruecolor($width, $height);
        $gd_image_src = null;
        switch($fileExtention) {
            case 'png' :
                $gd_image_src = imagecreatefrompng($image);
                imagealphablending( $gd_image_dest, false );
                imagesavealpha( $gd_image_dest, true );
                break;
            case 'jpeg': case 'jpg': $gd_image_src = imagecreatefromjpeg($image); // fails silently on Nikon D600 images, even with 4GB memory_limit..
            break;
            case 'gif' : $gd_image_src = imagecreatefromgif ($image);
                break;
            default: break;
        }

        if (!$gd_image_src) {
            return 'error-imagecreatefrom* ' . $gd_image_src;
        }
        imagecopyresampled($gd_image_dest, $gd_image_src, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

        $filesystem = new Filesystem();
        $filesystem->mkdir($newPath, 0777);
        $newFileName = $newPath . $name . "." . $fileExtention;

        switch($fileExtention) {
            case 'png' : imagepng($gd_image_dest, $newFileName); break;
            case 'jpeg' : imagejpeg($gd_image_dest, $newFileName, $jpegQuality); break;
            case 'jpg' : imagejpeg($gd_image_dest, $newFileName, $jpegQuality); break;
            case 'gif' : imagegif ($gd_image_dest, $newFileName); break;
            default: break;
        }

        return $newPath;
    }

    /**
     *
     * Gets image details such as the extension, sizes and filename and returns them as a standard object.
     *
     * @param $imageWithPath
     * @return \stdClass
     */
    private function getImageDetails($imageWithPath) {
        $size = getimagesize($imageWithPath);

        $imgParts = explode("/",$imageWithPath);
        $lastPart = $imgParts[count($imgParts)-1];

        if (stristr("?",$lastPart)) {
            $lastPart = substr($lastPart,0,stripos("?",$lastPart));
        }
        if (stristr("#",$lastPart)) {
            $lastPart = substr($lastPart,0,stripos("#",$lastPart));
        }

        $dotPos     = stripos($lastPart,".");
        $name         = substr($lastPart,0,$dotPos);
        $extension     = substr($lastPart,$dotPos+1);

        $Details = new \stdClass();
        $Details->height    = $size[1];
        $Details->width        = $size[0];
        $Details->ratio        = $size[0] / $size[1];
        $Details->extension = $extension;
        $Details->name         = $name;

        return $Details;
    }

    protected function imageCreateFromAll($fullImagePath) {
        $size = getimagesize($fullImagePath);
        switch($size['mime']){
            case 'image/jpeg':
                $img = imagecreatefromjpeg($fullImagePath);
                break;
            case 'image/gif':
                $img = imagecreatefromgif($fullImagePath);
                break;
            case 'image/png':
                $img = imagecreatefrompng($fullImagePath);
                break;
            default:
                $img = null;
                break;
        }
        return $img;
    }
}
