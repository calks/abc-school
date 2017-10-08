<?php

	define('QUESTIONS_MODULE_ITEMS_PER_PAGE', 10);
	Application::loadLibrary('captcha');

	class QuestionsModule extends Module {
		
		protected $action;
    	protected $object;
    	protected $object_list;
    	protected $pagenav_data;
    	protected $errors;
    	protected $form;
    	protected $captcha;
		

        public function run($params=array()) {

        	$this->action = $params ? array_shift($params) : 'list';
        	
			$method_name = 'task' . ucfirst($this->action);
			if (!method_exists($this, $method_name)) return $this->terminate();
			
			$this->captcha = new captcha($this->getName());
			
			$static_dir = Application::getModuleUrl($this->getName()) . '/static';
			
			$page = Application::getPage();
			$page->AddStylesheet("$static_dir/css/questions.css");			
			
			return call_user_func(array($this, $method_name), $params);	

        }

        
        protected function getForm() {
        	Application::loadLibrary('olmi/form');
        	$object = Application::getObjectInstance('question');
        	$form = new BaseForm();
        	$form = $object->make_form($form);
        	$form->addField(new TEditField('captcha', '', 10,20));
        	
        	return $form;
        }
        
        
        protected function validateForm($form) {
        	$errors = array();
        	
        	if (!$form->getValue('author_name')) {
        		$errors['author_name'] = 'Нам нужно знать, как к вам обращаться';
        	}
        	if (!$form->getValue('author_email')) {
        		$errors['author_email'] = 'Нам нужeн ваш Email, чтобы прислать вам ответ на вопрос';
        	}
        	elseif (!email_valid($form->getValue('author_email'))) {
        		$errors['author_email'] = 'Вы, вероятно, опечатались. Проверьте правильность ввода адреса.';
        	}
        	if (!$form->getValue('question')) {
        		$errors['question'] = 'Вы забыли ввести вопрос';
        	}
        	
        	
        	if (!$this->captcha->code_valid($form->getValue('captcha'))) {
        		$errors['captcha'] = 'Вы ввели неверный код';
        		$this->captcha->regenerate();
        	}
        	
        	return $errors;
        }
       
        
        protected function taskAdd($params=array()) {

        	$this->object = Application::getObjectInstance('question');
        	$this->form = $this->getForm();
        	
        	if(Request::isPostMethod()) {
        		$this->form->LoadFromRequest($_REQUEST);
        		$this->errors = $this->validateForm($this->form);
        		$this->form->UpdateObject($this->object);

        		if (!$this->errors) {
        			$this->object->id = null;
        			$this->object->created = date("Y-m-d H:i:s");
        			$this->object->active = false;
        			$this->object->author_notified = false;
        			$this->object->save();        			
        			$this->captcha->regenerate();
        			
        			$redirect_url = "/questions-thanks";
        			Redirector::redirect($redirect_url);
        		}        	
        	}
        	else {
        		$this->captcha->regenerate();
        	}
        	
        	$smarty = Application::getSmarty();
        	$template_path = $this->getTemplatePath($this->action);
        	$smarty->assign('errors', $this->errors);
        	$smarty->assign('form', $this->form);
        	$smarty->assign('captcha', $this->captcha);
        	$smarty->assign('form_action', Application::getSeoUrl("/{$this->getName()}/add"));
        	
        	return $smarty->fetch($template_path);
        	
        }
        
        protected function taskList($params=array()) {
        	$this->pagenav_data['page'] = $params ? (int)array_shift($params) : 1;
        	if ($this->pagenav_data['page']<1) $this->pagenav_data['page']=1;
        	
        	
        	$obj = Application::getObjectInstance('question');
        	
        	$load_params = array();
        	$load_params['mode'] = 'front';
        	$load_params['where'][] = 'active = 1';
        	$load_params['order_by'][] = 'created DESC';
        	
        	$total_items = $obj->count_list($load_params);
        	$this->pagenav_data['total'] = ceil($total_items/QUESTIONS_MODULE_ITEMS_PER_PAGE);
        	        	
        	$load_params['limit'] = QUESTIONS_MODULE_ITEMS_PER_PAGE;
        	$load_params['offset'] = QUESTIONS_MODULE_ITEMS_PER_PAGE * ($this->pagenav_data['page']-1);
        	
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
			$smarty->assign('questions', $this->oject_list);
			$page = pagePropertiesHelper::getDocument();
			$smarty->assign('page_heading', isset($page->meta_title) ? $page->meta_title : '');
			$smarty->assign('page_content', isset($page->content) ? $page->content : '');
			$smarty->assign('pagenav_data', $this->pagenav_data);
			
			$template_path = $this->getTemplatePath($this->action);						
			return $smarty->fetch($template_path);   
        	
        }
		
		
	}