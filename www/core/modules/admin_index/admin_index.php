<?php

	class AdminIndexModule extends Module {
		
		public function run($params=array()) {
			
			$user_session = Application::getUserSession();
			if (!$user_session->userLogged()) Redirector::redirect('/admin/admin_login');
						
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			return $smarty->fetch($template_path);
		}
		
	}
	
	