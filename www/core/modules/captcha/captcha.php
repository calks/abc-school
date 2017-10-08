<?php

	class CaptchaModule extends Module {
		
		public function run($params=array()) {
			$id = array_shift($params);
			
			$str = strtoupper(@$_SESSION[$id]);
			$image = $this->getBaseImage();

			$X_var=ImageSX($image);
			$X_var=$X_var-130;
			$Y_var=ImageSY($image);
			$Y_var=$Y_var-10;

			$color = ImageColorAllocate($image, 210, 90, 21);
			$font = $this->getFontPath();

			if (function_exists('ImageTTFText')) {
  				ImageTTFText($image, 35, 0, $X_var, $Y_var, $color, $font, $str);
			}
			else {
  				imagestring($image, 5, 11, 0, $str, $black);
			}

			$black = ImageColorAllocate($image, 0, 0, 0);
			$white = ImageColorAllocate($image, 255, 255, 255);
			$grey = ImageColorAllocate($image, 125, 125, 125);
			$orange = ImageColorAllocate($image, 255, 145, 24);			
			
			for ($i=0;$i<3;$i++) $this->drawLine($image, $black);
			for ($i=0;$i<3;$i++) $this->drawLine($image, $grey);
			for ($i=0;$i<3;$i++) $this->drawLine($image, $orange);

			header("Content-Type: image/gif");
			ImageGif($image);
			ImageDestroy($image);
			die();			
		}
		
		protected function getBaseImage() {
			$ImageToLoad = dirname(Application::getModulePath($this->getName())) . '/static/imagecode.gif';			
			$image = @ImageCreateFromGIF($ImageToLoad);
			if($image == "") return false;
			else return $image;
		}
		
		protected function getFontPath() {
			return dirname(Application::getModulePath($this->getName())) . '/static/alger.ttf';
		}
		
		protected function drawLine($image, $color) {
		    $x1 = rand(0,188);
		    $y1 = rand(0,60);
		    $x2 = rand(0,188);
		    $y2 = rand(0,60);
		    return imageline($image, $x1, $y1, $x2, $y2, $color);			
		} 
	}
	
	
	
	