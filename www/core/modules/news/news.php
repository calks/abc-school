<?php

	define ('NEWS_MODULE_ITEMS_PER_PAGE', 5);

    class NewsModule extends Module {
    	
    	protected $action;
    	protected $oject_id;
    	protected $object;
    	protected $object_list;
    	protected $pagenav_data;

        public function run($params=array()) {

			$static_dir = Application::getModuleUrl($this->getName()) . '/static';
			
			$page = Application::getPage();
			$page->AddStylesheet("$static_dir/css/news.css");
        	        	
        	$this->action = $params ? array_shift($params) : 'list';
        	
			$method_name = 'task' . ucfirst($this->action);
			if (!method_exists($this, $method_name)) return $this->terminate();
			
			return call_user_func(array($this, $method_name), $params);	

        }
        
        
        protected function taskList($params=array()) {
        	$this->pagenav_data['page'] = $params ? (int)array_shift($params) : 1;
        	if ($this->pagenav_data['page']<1) $this->pagenav_data['page']=1;
        	
        	$obj = Application::getObjectInstance('news');
        	
        	$load_params = array();
        	$load_params['mode'] = 'front';
        	
        	$total_items = $obj->count_list($load_params);
        	$this->pagenav_data['total'] = ceil($total_items/NEWS_MODULE_ITEMS_PER_PAGE);
        	        	
        	$load_params['limit'] = NEWS_MODULE_ITEMS_PER_PAGE;
        	$load_params['offset'] = NEWS_MODULE_ITEMS_PER_PAGE * ($this->pagenav_data['page']-1);
        	
        	$this->oject_list = $obj->load_list($load_params);
        	foreach($this->oject_list as $item) {
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
			$smarty->assign('news', $this->oject_list);
			$smarty->assign('pagenav_data', $this->pagenav_data);
			
			$template_path = $this->getTemplatePath($this->action);						
			return $smarty->fetch($template_path);   
        	
        }
        
        protected function taskDetail($params=array()) {
        	$this->object_id = @(int)array_shift($params);        	
        	if (!$this->object_id) return $this->terminate();
        	
        	$obj = Application::getObjectInstance('news');
        	$load_params = array('mode' => 'front');
        	
        	$this->object = $obj->load($this->object_id, $load_params);
        	if (!$this->object) return $this->terminate();
        	
        	pagePropertiesHelper::setTitleDescKeysFromObject($this->object);
        	        	
			$smarty = Application::getSmarty();
			$smarty->assign('news', $this->object);
			$smarty->assign('view_all_link', Application::getSeoUrl("/{$this->getName()}"));		
			
			
			$template_path = $this->getTemplatePath($this->action);						
			return $smarty->fetch($template_path);        	
        }
        

    }
