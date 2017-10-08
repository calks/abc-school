<?php


	class image_size extends CMSObject {
		
		public $object_type;
		public $size_name;
		public $height;
		public $width;
		
		protected static $data;
		
		public function getDimensions($object_type, $size_name) {
			$size_data = $this->getSizeData($object_type, $size_name);
			if (!isset($size_data[$object_type][$size_name])) {
				$this->createSizeRecord($object_type, $size_name);
				$size_data = $this->getSizeData($object_type, $size_name);
			}

			return $size_data[$object_type][$size_name];
		}
		
		public function getHeight($object_type, $size_name) {
			$dimensions = $this->getDimensions($object_type, $size_name);
			return $dimensions['height'];
		}
		
		public function getWidth($object_type, $size_name) {
			$dimensions = $this->getDimensions($object_type, $size_name);
			return $dimensions['width'];
		}		
		
		public function get_table_name() {
			return 'image_size';
		}
		
		protected function getSizeData($object_type, $size_name) {
			if (is_null(self::$data)) {
				self::$data = array();
				$db = Application::getDb();
				$table = $this->get_table_name();
				$data_raw = $db->executeSelectAllObjects("
					SELECT * FROM $table
				");
				
				foreach ($data_raw as $item) {
					if (!isset(self::$data[$item->object_type])) {
						self::$data[$item->object_type] = array();	
					}
					self::$data[$item->object_type][$item->size_name] = array(
						'height' => $item->height,
						'width' => $item->width
					);
				}				
			}
			return self::$data;
		}
				
				
		protected function getDefaultDimensions($size_name) {
			$default_sizes['tiny'] = array(
				'width' => 60,
				'height' => 45
			);			
			$default_sizes['small'] = array(
				'width' => 120,
				'height' => 90
			);
			$default_sizes['medium'] = array(
				'width' => 240,
				'height' => 180
			);			
			$default_sizes['big'] = array(
				'width' => 640,
				'height' => 480
			);
			$default_sizes['huge'] = array(
				'width' => 800,
				'height' => 600
			);
			
			return isset($default_sizes[$size_name]) ? $default_sizes[$size_name] : array(
				'width' => 400,
				'height' => 300			
			);
			
		}
		
		protected function createSizeRecord($object_type, $size_name) {
			$db = Application::getDb();
			$table = $this->get_table_name();
			$object_type = addslashes($object_type);
			$size_name = addslashes($size_name);
			
			$dimensions = $this->getDefaultDimensions($size_name);
			
			$db->execute("
				REPLACE INTO $table (
					`object_type`,
					`size_name`,
					`height`,
					`width`
				)
				VALUES (
					'$object_type',
					'$size_name',
					{$dimensions['height']},
					{$dimensions['width']}					
				)
			");	
					
			if (!isset(self::$data[$object_type])) self::$data[$object_type] = array();			
			self::$data[$object_type][$size_name] = $dimensions;			
		}
	
	}
	

