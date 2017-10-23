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
