<?php

	define ('GALLERY_MODULE_ITEMS_PER_PAGE', 12);

    class GalleryModule extends Module {
    	
    	protected $action;
    	protected $gallery_id;
    	protected $gallery;
    	protected $photos;
    	protected $galleries;
    	protected $pagenav_data;

        public function run($params=array()) {

			$static_dir = Application::getModuleUrl($this->getName()) . '/static';
			
			$page = Application::getPage();
			$page->AddStylesheet("$static_dir/css/gallery.css");
        	        	
        	$this->action = $params ? array_shift($params) : 'list';
        	
			$method_name = 'task' . ucfirst($this->action);
			if (!method_exists($this, $method_name)) return $this->terminate();
			
			$page = pagePropertiesHelper::getDocument();
			$smarty = Application::getSmarty();
			$smarty->assign('page_heading', isset($page->meta_title) ? $page->meta_title : '');
			$smarty->assign('page_content', isset($page->content) ? $page->content : '');
			
			
			return call_user_func(array($this, $method_name), $params);	

        }
        
        
        protected function taskList($params=array()) {
        	$this->pagenav_data['page'] = $params ? (int)array_shift($params) : 1;
        	if ($this->pagenav_data['page']<1) $this->pagenav_data['page']=1;
        	
        	$obj = Application::getObjectInstance('gallery');
        	
        	$load_params = array();
        	$load_params['mode'] = 'front';
        	$load_params['where'][] = 'active=1';
        	
        	$total_items = $obj->count_list($load_params);
        	$this->pagenav_data['total'] = ceil($total_items/GALLERY_MODULE_ITEMS_PER_PAGE);
        	        	
        	$load_params['limit'] = GALLERY_MODULE_ITEMS_PER_PAGE;
        	$load_params['offset'] = GALLERY_MODULE_ITEMS_PER_PAGE * ($this->pagenav_data['page']-1);
        	
        	$this->galleries = $obj->load_list($load_params);
        	foreach($this->galleries as $item) {
        		$item->link = Application::getSeoUrl("/{$this->getName()}/detail/$item->id");	
        	} 
        	        	
        	$this->pagenav_data['links'] = array();
        	if ($this->pagenav_data['total']) {
        		for ($i=1; $i<=$this->pagenav_data['total']; $i++) {
        			$link = "/{$this->getName()}";
        			if ($i!=1) $link .= "/list/$i";
        			$this->pagenav_data['links'][$i] = Application::getSeoUrl($link);        			
        		}
        	}        	
        	
			$smarty = Application::getSmarty();
			$smarty->assign('galleries', $this->galleries);
			$smarty->assign('pagenav_data', $this->pagenav_data);
			
			$template_path = $this->getTemplatePath($this->action);						
			return $smarty->fetch($template_path);   
        	
        }
        
        protected function taskDetail($params=array()) {        	        	
        	$this->gallery_id = @(int)array_shift($params);        	
        	if (!$this->gallery_id) return $this->terminate();
        	
        	$this->pagenav_data['page'] = $params ? (int)array_shift($params) : 1;
        	if ($this->pagenav_data['page']<1) $this->pagenav_data['page']=1;
        	        	
        	$gallery = Application::getObjectInstance('gallery');
        	$gallery_params = array('mode' => 'front');
        	
        	$this->gallery = $gallery->load($this->gallery_id, $gallery_params);
        	if (!$this->gallery) return $this->terminate();
        	
			$page = Application::getPage();
			$static_dir = Application::getModuleUrl($this->getName()) . '/static';
			$page->AddStylesheet("$static_dir/css/jquery.lightbox-0.5.css");
			$page->AddScript("$static_dir/js/jquery.lightbox-0.5.pack.js");        	
        	
        	
        	$photo = Application::getObjectInstance('gallery/photo');
        	$photo_params = array('mode' => 'front');
        	$photo_params['where'][] = "gallery_id=$this->gallery_id";
        	
        	$total_items = $photo->count_list($photo_params);
        	$this->pagenav_data['total'] = ceil($total_items/GALLERY_MODULE_ITEMS_PER_PAGE);
        	        	
        	$photo_params['limit'] = GALLERY_MODULE_ITEMS_PER_PAGE;
        	$photo_params['offset'] = GALLERY_MODULE_ITEMS_PER_PAGE * ($this->pagenav_data['page']-1);
        	        	
        	$this->photos = $photo->load_list($photo_params);
        	
        	foreach($this->photos as $p) {
        		$p->thumb_url = PHOTOS_URL . "/gallery_photo/{$p->id}/thumb/$p->image";
        		list($filename, $extension) = explode('.', $p->image);
        		$p->fullsize_url = "/temp/thumb/gallery_photo/{$p->id}/{$filename}_1000x1000_inscribe.$extension";
        	}
        	
        	$this->pagenav_data['links'] = array();
        	if ($this->pagenav_data['total']) {
        		for ($i=1; $i<=$this->pagenav_data['total']; $i++) {
        			$link = "/{$this->getName()}/detail/$this->gallery_id";
        			if ($i!=1) $link .= "/$i";
        			$this->pagenav_data['links'][$i] = Application::getSeoUrl($link);        			
        		}
        	}  
        	        	
			$smarty = Application::getSmarty();
			$smarty->assign('photos', $this->photos);
			$smarty->assign('gallery', $this->gallery);
			$smarty->assign('pagenav_data', $this->pagenav_data);
			$smarty->assign('back_link', Application::getSeoUrl("/{$this->getName()}"));
			$smarty->assign('static_dir', $static_dir);
			
			
			$template_path = $this->getTemplatePath($this->action);						
			return $smarty->fetch($template_path);        	
        }
        

    }
