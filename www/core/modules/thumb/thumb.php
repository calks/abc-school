<?php
	error_reporting(E_ALL); ini_set('display_errors',1);
	class ThumbModule extends Module {
		
		protected $entity_name;
		protected $entity_id;
		
		protected $image;
		protected $source_path;
		protected $destination_path;		
		protected $destination_url;
		protected $width;
		protected $height;
		protected $mode;
		protected $output_format;
		
		
		
		protected function parseImageParams($thumb_filename) {
			$info = explode('.', $thumb_filename);
			
			$this->output_format = $info[1];
			
			if (!in_array($this->output_format, array('jpeg', 'jpg', 'gif', 'png'))) return false;
			
			$info = $info[0];
			$info = explode('_', $info);
			$filename = $info[0]; 
			$this->mode = isset($info[2]) ? $info[2] : 'inscribe'; 
			$dimensions = isset($info[1]) ? $info[1] : null;
			
			if (!$dimensions) return false;
			
			$dimensions = explode('x', $dimensions);
			
			$this->width = isset($dimensions[0]) ? (int)$dimensions[0] : 0;
			$this->height = isset($dimensions[1]) ? (int)$dimensions[1] : 0;
			
			if (!$this->height || !$this->width) return false;

			$this->source_path = UPLOAD_PHOTOS . "/$this->entity_name/$this->entity_id/huge/$filename.$this->output_format";
			
			$destination_dir = "/temp/thumb/$this->entity_name/$this->entity_id";
			$destination_dir_abs = Application::getSitePath() . $destination_dir;
			if (!is_dir($destination_dir_abs)) {
				if (!@mkdir($destination_dir_abs, 0777, true)) {
					return false;
				}
			}
			
			$this->destination_url = "$destination_dir/$thumb_filename";
			
			//die($this->destination_url); 
			
			return true;		
		}
		
		
		
		public function run($params=array()) {			
			
			$this->entity_name = @array_shift($params);
			$this->entity_id = @array_shift($params);

			$thumb_filename = @array_shift($params);
			
			if (!$this->parseImageParams($thumb_filename)) return $this->terminate();
			
			$destination_path = Application::getSitePath() . $this->destination_url;			
			
			$info = getimagesize($this->source_path);
			if ($info[2] == IMAGETYPE_JPEG) {
				if (function_exists('imagecreatefromjpeg')) $img = imagecreatefromjpeg($this->source_path);
				else return $this->terminate();
			} elseif ($info[2] == IMAGETYPE_GIF) {
				if (function_exists('imagecreatefromgif')) $img = imagecreatefromgif($this->source_path);
				else return $this->terminate();	
			}
        	elseif ($info[2] == IMAGETYPE_PNG) {
        		if (function_exists('imagecreatefrompng')) $img = imagecreatefrompng($this->source_path);
        		else return $this->terminate();
        	}
        	else return $this->terminate();
        	
        	$src_width = $info[0];
        	$src_height = $info[1];

                
			if ($this->mode == 'crop') { 
		        $dims = $this->retainAspectRationCrop($src_width, $src_height, $this->width, $this->height);

		        $out_width = $this->width;
		        $out_height = $this->height;
		        $dest_width = $dims[0];
		        $dest_height = $dims[1];
	        
				$srcLeft = ceil(($dest_width-$this->width)/80);
	        
	        
		        if ($dest_height>$this->height) {
		        	$srcTop = ceil(($dest_height-$this->height));
		        	$destTop = 0;	
		        }
		        else {
		        	$srcTop = 0;
		        	$destTop = ceil(($this->height-$dest_height)/2);	        	
		        }
		        
		        if ($dest_width>$this->width) {		        	
		        	$srcLeft = 0;
		        	$destLeft = -1 * floor(($dest_width-$this->width)/2);	
		        }
		        else {
		        	$srcLeft = 0;
		        	$destLeft = ceil(($this->width-$dest_width)/2);	        	
		        }	        
	        }
    	    else {
		        if ($info[0] > $this->width || $info[1] > $this->height) $dims = $this->retainAspectRation($src_width, $src_height, $this->width, $this->height);
		        else $dims = array($info[0], $info[1]);
		        $srcLeft = 0;
		        $srcTop = 0;
		        $destLeft = 0;
		        $destTop = 0;
		        $out_width = $dims[0];
		        $out_height = $dims[1];
		        $dest_width = $dims[0];
		        $dest_height = $dims[1];
		        
	        }

	        $ne = $this->getEmptyImage($out_width, $out_height);

        	imagecopyresampled($ne, $img, $destLeft, $destTop, $srcLeft, $srcTop, $dest_width, $dest_height, $info[0], $info[1]);

//        if ($watermark) $ne = createWatermark($ne);

			switch ($this->output_format) {
				case 'jpg':
				case 'jpeg':
            	$func = 'imagejpeg';
            	$type = 'image/jpeg';
            	break;
        	case 'gif':
            	$func = 'imagegif';
            	$type = 'image/gif';
            	break;
        	case 'png':
            	$func = 'imagepng';
            	$type = 'image/png';
            	break;
        	}

        	//$this->addWatermark($ne);
        	
        	call_user_func($func, $ne, $destination_path);
        	
        	/*header("Content-type: $type\n");
        	imagejpeg($ne);
        	die();*/

        	if (is_file($destination_path)) Redirector::redirect($this->destination_url);
        	else return $this->terminate();        	
		}
		
		
		protected function addWatermark($image) {
			$watermark_file = coreResourceLibrary::getFirstFilePath(APP_RESOURCE_TYPE_MODULE, $this->getName(), "/static/watermark");
			if (!$watermark_file) return;
			
			$watermark_path = Application::getSitePath() . $watermark_file;
			
			$info = getimagesize($watermark_path);
			if ($info[2] == IMAGETYPE_JPEG) {
				if (function_exists('imagecreatefromjpeg')) $watermark = imagecreatefromjpeg($watermark_path);
				else return;
			} elseif ($info[2] == IMAGETYPE_GIF) {
				if (function_exists('imagecreatefromgif')) $watermark = imagecreatefromgif($watermark_path);
				else return;	
			}
        	elseif ($info[2] == IMAGETYPE_PNG) {
        		if (function_exists('imagecreatefrompng')) $watermark = imagecreatefrompng($watermark_path);
        		else return;
        	}
        	else return;
        	
        	$watermark_padding = 8;
        	
	        $watermark_width = imagesx($watermark);
	        $watermark_height = imagesy($watermark);
	        
	        $image_width = imagesx($image);
	        $image_height = imagesy($image);
	        
        	if ($image_width < $watermark_width*2) return; 
			
        	$watermark_left = $image_width - $watermark_width - $watermark_padding;
        	$watermark_top = $image_height - $watermark_height - $watermark_padding;
        	 
        	imagecopy($image, $watermark, $watermark_left, $watermark_top, 0, 0, $watermark_width, $watermark_height);
		}
		
		protected function getEmptyImage($width, $height, $fill_r=255, $fill_g=255, $fill_b=255) {
	        $ne = imagecreatetruecolor($width, $height);
	        if (!is_null($fill_r) && !is_null($fill_g) && !is_null($fill_b)) {
		        $fill = imagecolorallocate($ne, $fill_r, $fill_g, $fill_b);
		        imagefill($ne, 0, 0, $fill);	        	
	        }
	        return $ne;
		}
		
		
		protected function retainAspectRation($w, $h, $neww, $newh) {

			if ($w > $neww) $aspect = $w / $neww;
			else $aspect = $h / $newh;

			if ($h / $aspect > $newh) $aspect = $h / $newh;

			return array(round($w / $aspect), round($h / $aspect));
		}
    
    
		protected function retainAspectRationCrop($w, $h, $neww, $newh) {

			$aspect_h = $h / $newh;
			$aspect_w = $w / $neww;
    	   	
			$aspect = ($aspect_h<$aspect_w) ? $aspect_h : $aspect_w;
    	
			if ($aspect < 1) return array($w, $h);

			return array(round($w / $aspect), round($h / $aspect));
		}
		
		
		
		protected function terminate() {  
			header("HTTP/1.0 404 Not Found");
			die();
		}
		
		
	}
	
	
	

    



	