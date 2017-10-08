<?php

	Application::loadLibrary('core/admin_module');

	class AdminGalleryModule extends AdminModule {
		
		protected function getObjectName() {
			return 'gallery';
		}		
		
		protected function getImageFields() {
			return array(
				'image' => array('thumb', 'huge')
			);
		}		
		
		protected function beforeListLoad(&$load_params) {
			$photo = Application::getObjectInstance('gallery/photo');
			$photo_table = $photo->get_table_name();
			$photo_alias = $photo->get_table_abr();
			
			$object = Application::getObjectInstance($this->getObjectName());
			$table = $object->get_table_name();
			$alias = $object->get_table_abr(); 
			
			$load_params['fields'][] = "COUNT($photo_alias.id) AS photos_count";
			$load_params['from'][] = "
				LEFT JOIN $photo_table $photo_alias
				ON $photo_alias.gallery_id = $alias.id
			";
			$load_params['group_by'][] = "$alias.id";
		}
		
		
		/*protected function saveImages() {
			parent::saveImages();
			$image = isset($this->objects[0]->image) ? $this->objects[0]->image : ''; 
			if (!$image) $this->errors[] = "Картинка - обязательное поле";
		}*/
				
		
	}