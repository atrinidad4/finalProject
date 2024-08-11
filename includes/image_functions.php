<?php
function resizeImage($file, $targetWidth, $targetHeight) {
    list($originalWidth, $originalHeight, $imageType) = getimagesize($file);

    $imageResized = imagecreatetruecolor($targetWidth, $targetHeight);

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $imageOriginal = imagecreatefromjpeg($file);
            break;
        case IMAGETYPE_PNG:
            $imageOriginal = imagecreatefrompng($file);
            break;
        case IMAGETYPE_GIF:
            $imageOriginal = imagecreatefromgif($file);
            break;
        default:
            return false;
    }

    imagecopyresampled($imageResized, $imageOriginal, 0, 0, 0, 0, $targetWidth, $targetHeight, $originalWidth, $originalHeight);

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($imageResized, $file);
            break;
        case IMAGETYPE_PNG:
            imagepng($imageResized, $file);
            break;
        case IMAGETYPE_GIF:
            imagegif($imageResized, $file);
            break;
    }

    imagedestroy($imageOriginal);
    imagedestroy($imageResized);

    return true;
}

?>