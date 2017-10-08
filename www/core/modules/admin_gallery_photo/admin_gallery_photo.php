<?php

	Application::loadLibrary('core/admin_module');

	class AdminGalleryPhotoModule extends AdminModule {
		
		protected $layout;
		
		public function run($params=array()) {
			
			$this->layout = Request::get('layout');
			if (!$this->layout) $this->layout = 'standard';
			
			if ($this->url_addition) $this->url_addition .= '&'; 
			$this->url_addition .= "layout=$this->layout";
			
			$smarty = Application::getSmarty();
			$smarty->assign('layout', $this->layout);
			
			return parent::run($params);
		}
		
		protected function getObjectName() {
			return 'gallery/photo';
		}		
		
		protected function getImageFields() {
			return array(
				'image' => array('tiny', 'thumb', 'huge')
			);
		}		
		
		protected function saveImages() {
			parent::saveImages();
			$image = isset($this->objects[0]->image) ? $this->objects[0]->image : ''; 
			if (!$image) $this->errors[] = "Картинка - обязательное поле";
		}
		
		

		protected function beforeListLoad(&$load_params) {
			$filter = Application::getFilter('photo');
			$search_gallery = isset($_GET['search_gallery']) ? $_GET['search_gallery'] : null;
			if ($search_gallery) {
				$filter->setValue('search_gallery', $search_gallery);
				$filter->saveToSession(Application::getApplicationName());
			}
			$filter->set_params($load_params);
			$smarty = Application::getSmarty();
			$smarty->assign('allow_sorting', $filter->getValue('search_gallery') != 0);
			$smarty->assign('filter', $filter);
			
			$gallery = Application::getObjectInstance('gallery');
			$gallery_table = $gallery->get_table_name();
			$gallery_alias = $gallery->get_table_abr();
			
			$object = Application::getObjectInstance($this->getObjectName());
			$table = $object->get_table_name();
			$alias = $object->get_table_abr(); 
			
			$load_params['fields'][] = "$gallery_alias.name AS gallery_name";
			$load_params['from'][] = "
				LEFT JOIN $gallery_table $gallery_alias
				ON $gallery_alias.id = $alias.gallery_id
			";			
		}
		
		protected function taskList() {
			if ($this->layout == 'embeded') die();
			else return parent::taskList();
		}
		
		protected function taskEdit() {
			$smarty = Application::getSmarty();
			if ($this->action == 'add' && !Request::isPostMethod()) {
				$filter = Application::getFilter('photo');
				$gallery_id = $filter->getValue('search_gallery');
				if ($gallery_id) $this->objects[0]->gallery_id = $gallery_id;				
				$smarty->assign('gallery_id', $gallery_id);
				
				if ($this->layout != 'embeded') {					
					$page = Application::getPage();
					$page->addScript($this->getStaticResourceUrl('/static/add_photos.js'));
					$iframe_src = Application::getSeoUrl("/admin/{$this->getName()}?action=add&layout=embeded");
					$smarty->assign('add_photo_iframe_src', $iframe_src);					
				}
			}
			
			parent::taskEdit();
		}
		
		protected function neighbourExtraCondition() {
			$object = $this->objects[0];			
			return "gallery_id=$object->gallery_id";
		}
		
	}