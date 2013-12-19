<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Simplepie Class
 *
 * Use simplepie in your CodeIgniter application
 *
 * @package CodeIgniter
 * @subpackage Libraries
 * @category Libraries
 * @author Peter Nijssen <peter@peternijssen.nl>
 * @copyright Copyright (c) 2012, Peter Nijssen
 * @license MIT
 * @link https://github.com/PTish/Codeigniter-sparks-simplepie
 * @since 1.0
 * @version 1.0
 */

        class Circlecrop
        {

            private $src_img;
            private $src_w;
            private $src_h;
            private $dst_img;
            private $dst_w;
            private $dst_h;

            public function __construct($params)
            {
				$img = $params['img'];
				$dstWidth = $params['dstWidth'];
				$dstHeight = $params['dstHeight'];
                
				$this->src_img = $img;
                $this->src_w = imagesx($img);
                $this->src_h = imagesy($img);
                $this->dst_w = $dstWidth;
                $this->dst_h = $dstHeight;
            }

            public function __destruct()
            {
                if (is_resource($this->dst_img))
                {
                    imagedestroy($this->dst_img);
                }
            }

            public function display($dst_file)
            {
                imagepng($this->dst_img,$dst_file);
                return $this;
            }

            public function reset()
            {
                if (is_resource(($this->dst_img)))
                {
                    imagedestroy($this->dst_img);
                }
                $this->dst_img = imagecreatetruecolor($this->dst_w, $this->dst_h);
                imagecopy($this->dst_img, $this->src_img, 0, 0, 0, 0, $this->dst_w, $this->dst_h);
                return $this;
            }

            public function size($dstWidth, $dstHeight)
            {
                $this->dst_w = $dstWidth;
                $this->dst_h = $dstHeight;
                return $this->reset();
            }

            public function crop()
            {
                $this->reset();

                       $mask = imagecreatetruecolor($this->dst_w, $this->dst_h);
                $maskTransparent = imagecolorallocate($mask, 255, 0, 255);
                imagecolortransparent($mask, $maskTransparent);
                imagefilledellipse($mask, $this->dst_w / 2, $this->dst_h / 2, $this->dst_w, $this->dst_h, $maskTransparent);
                
                imagecopymerge($this->dst_img, $mask, 0, 0, 0, 0, $this->dst_w, $this->dst_h, 100);

                $dstTransparent = imagecolorallocate($this->dst_img, 255, 0, 255);
                imagefill($this->dst_img, 0, 0, $dstTransparent);
                imagefill($this->dst_img, $this->dst_w - 1, 0, $dstTransparent);
                imagefill($this->dst_img, 0, $this->dst_h - 1, $dstTransparent);
                imagefill($this->dst_img, $this->dst_w - 1, $this->dst_h - 1, $dstTransparent);
                imagecolortransparent($this->dst_img, $dstTransparent);

                return $this;
            }

        }


?>