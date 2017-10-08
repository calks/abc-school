<?php

	Application::loadLibrary('core/admin_module');

	class AdminSlideshowModule extends AdminModule {
		
		protected function getObjectName() {
			return 'slideshow_image';
		}		
		
		protected function getImageFields() {
			return array(
				'image' => array('small', 'slide')
			);
		}
		
		protected function saveImages() {
			parent::saveImages();
			$image = isset($this->objects[0]->image) ? $this->objects[0]->image : ''; 
			if (!$image) $this->errors[] = "Картинка - обязательное поле";
		}		
		
	}