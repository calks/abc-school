<?php

	class HeaderBlock extends Block {
		
		public function run($params=array()) {
			
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			
			$top_menu = Application::getBlockContent('pagemenu', array(
				'type' => 'top'
			));
			$smarty->assign('top_menu', $top_menu);			
			$smarty->assign('img_base', '/applications/' . Application::getApplicationName() . '/static/img/header');
						
			return $smarty->fetch($template_path);			
		}
		
	}