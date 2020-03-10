<?php

namespace materialhelpers;

use PHPThumb\GD;

class GDSpecial extends GD
{
    /**
     * Current maximum width an image can be after resizing (in pixels)
     *
     * @var int
     */
    protected $currMaxWidth;

    /**
     * Current maximum height an image can be after resizing (in pixels)
     *
     * @var int
     */
    protected $currMaxHeight;

    /**
     * Special Resizes the Image
     *
     * This function attempts to get the image to as close to the provided dimensions as possible, and then creates image with set image and height.
     * Original image is putted into center of created image. You can also change image background color.
     *
     * @param int $maxWidth
     * @param int $maxHeight
     * @param array $color
     * @param string $position (top, bottom)
     * @param array $corners
     * @return \PHPThumb\GD
     */
    public function specialResize($maxWidth = 0, $maxHeight = 0, $color = array(), $position = null, $corners = array())
    {
        // make sure our arguments are valid
        if (!is_numeric($maxWidth)) {
            throw new \InvalidArgumentException('$maxWidth must be numeric');
        }

        if (!is_numeric($maxHeight)) {
            throw new \InvalidArgumentException('$maxHeight must be numeric');
        }

        $this->currMaxWidth = intval($maxWidth);
        $this->currMaxHeight = intval($maxHeight);

        // make sure we're not exceeding our image size if we're not supposed to
        if ($this->options['resizeUp'] === false) {
            $this->maxHeight = (intval($maxHeight) > $this->currentDimensions['height']) ? $this->currentDimensions['height'] : $maxHeight;
            $this->maxWidth = (intval($maxWidth) > $this->currentDimensions['width']) ? $this->currentDimensions['width'] : $maxWidth;
        } else {
            $this->maxHeight = intval($maxHeight);
            $this->maxWidth = intval($maxWidth);
        }

        // get the new dimensions...
        $this->calcImageSize($this->currentDimensions['width'], $this->currentDimensions['height']);

        // create the working image
        if (function_exists('imagecreatetruecolor')) {
            $this->workingImage = imagecreatetruecolor($this->currMaxWidth, $this->currMaxHeight);
        } else {
            $this->workingImage = imagecreate($this->currMaxWidth, $this->currMaxHeight);
        }

        // set background color and fill working image with this color
        if ($color == 'alpha') {
            $background = imagecolorallocatealpha($this->workingImage, 255, 255, 255, 127);
            imagefill($this->workingImage, 0, 0, $background);
            imagesavealpha($this->workingImage, true);
        } else {
            if ($color) {
                $background = imagecolorallocate($this->workingImage, $color[0], $color[1], $color[2]);
            } else {
                $background = imagecolorallocate($this->workingImage, 255, 255, 255);
            }

            imagefill($this->workingImage, 0, 0, $background);
        }

        // get x, y coordinates to put old image to new image center
        $cordX = ($this->currMaxWidth / 2) - ($this->newDimensions['newWidth'] / 2);

        if (!$position) {
            $cordY = ($this->currMaxHeight / 2) - ($this->newDimensions['newHeight'] / 2);
        } elseif ($position == 'top') {
            $cordY = 0;
        } elseif ($position == 'bottom') {
            $cordY = $this->currMaxHeight - $this->newDimensions['newHeight'];
        } else {
            $cordY = 0;
        }

        // and create the newly sized image
        imagecopyresampled
        (
            $this->workingImage, $this->oldImage, $cordX, $cordY, 0, 0, $this->newDimensions['newWidth'], $this->newDimensions['newHeight'], $this->currentDimensions['width'], $this->currentDimensions['height']
        );

        // update all the variables and resources to be correct
        $this->oldImage = $this->workingImage;
        $this->currentDimensions['width'] = $this->currMaxWidth;
        $this->currentDimensions['height'] = $this->currMaxHeight;

        if ($corners) {
            $this->createCorners($corners[0], (isset($corners[1])) ? $corners[1] : array());
        }

        return $this;
    }

    /**
     * Rotates image specified number of degrees
     *
     * @param  int          $degrees
     * @return \PHPThumb\GD
     */
    public function rotateImageNDegrees($degrees)
    {
        if (!is_numeric($degrees)) {
            throw new \InvalidArgumentException('$degrees must be numeric');
        }

        if (!function_exists('imagerotate')) {
            throw new \RuntimeException('Your version of GD does not support image rotation');
        }

        $this->workingImage = imagerotate($this->oldImage, $degrees, 0);

        if ($degrees == 180){
            $newWidth = $this->currentDimensions['width'];
            $newHeight = $this->currentDimensions['height'];
        } else {
            $newWidth = $this->currentDimensions['height'];
            $newHeight = $this->currentDimensions['width'];
        }
        $this->oldImage                    = $this->workingImage;
        $this->currentDimensions['width']  = $newWidth;
        $this->currentDimensions['height'] = $newHeight;

        return $this;
    }

    protected function createCorners($radius, $corners = array())
    {
        $w = $this->currentDimensions['width'];
        $h = $this->currentDimensions['height'];

        $q = 4;
        $radius *= $q;

        // find color to change to transparent
        do {
            $r = rand(0, 255);
            $g = rand(0, 255);
            $b = rand(0, 255);
        } while (imagecolorexact($this->oldImage, $r, $g, $b) < 0);

        $nw = $w * $q;
        $nh = $h * $q;

        $img = imagecreatetruecolor($nw, $nh);
        $alphacolor = imagecolorallocatealpha($img, $r, $g, $b, 127);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        imagefilledrectangle($img, 0, 0, $nw, $nh, $alphacolor);

        imagefill($img, 0, 0, $alphacolor);
        imagecopyresampled($img, $this->oldImage, 0, 0, 0, 0, $nw, $nh, $w, $h);

        if ($corners) {
            foreach ($corners as $value) {
                switch ($value) {
                    case 'tl': {
                        imagearc($img, $radius - 1, $radius - 1, $radius * 2, $radius * 2, 180, 270, $alphacolor);
                        imagefilltoborder($img, 0, 0, $alphacolor, $alphacolor);
                    }
                        break;
                    case 'tr': {
                        imagearc($img, $nw - $radius, $radius - 1, $radius * 2, $radius * 2, 270, 0, $alphacolor);
                        imagefilltoborder($img, $nw - 1, 0, $alphacolor, $alphacolor);
                    }
                        break;
                    case 'bl': {
                        imagearc($img, $radius - 1, $nh - $radius, $radius * 2, $radius * 2, 90, 180, $alphacolor);
                        imagefilltoborder($img, 0, $nh - 1, $alphacolor, $alphacolor);
                    }
                        break;
                    case 'br': {
                        imagearc($img, $nw - $radius, $nh - $radius, $radius * 2, $radius * 2, 0, 90, $alphacolor);
                        imagefilltoborder($img, $nw - 1, $nh - 1, $alphacolor, $alphacolor);
                    }
                        break;
                }
            }
        } else {
            imagearc($img, $radius - 1, $radius - 1, $radius * 2, $radius * 2, 180, 270, $alphacolor);
            imagefilltoborder($img, 0, 0, $alphacolor, $alphacolor);
            imagearc($img, $nw - $radius, $radius - 1, $radius * 2, $radius * 2, 270, 0, $alphacolor);
            imagefilltoborder($img, $nw - 1, 0, $alphacolor, $alphacolor);
            imagearc($img, $radius - 1, $nh - $radius, $radius * 2, $radius * 2, 90, 180, $alphacolor);
            imagefilltoborder($img, 0, $nh - 1, $alphacolor, $alphacolor);
            imagearc($img, $nw - $radius, $nh - $radius, $radius * 2, $radius * 2, 0, 90, $alphacolor);
            imagefilltoborder($img, $nw - 1, $nh - 1, $alphacolor, $alphacolor);
        }

        imagealphablending($img, true);
        imagecolortransparent($img, $alphacolor);

        $dest = imagecreatetruecolor($w, $h);
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        imagefilledrectangle($dest, 0, 0, $w, $h, $alphacolor);
        imagecopyresampled($dest, $img, 0, 0, 0, 0, $w, $h, $nw, $nh);

        imagedestroy($img);

        $this->oldImage = $dest;
    }
}
