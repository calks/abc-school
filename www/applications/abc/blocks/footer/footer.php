<?php

	class FooterBlock extends Block {
		
		public function run($params=array()) {
			
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			
			$footer_menu = Application::getBlockContent('pagemenu', array(
				'type' => 'footer'
			));
			
			
			$smarty->assign('footer_menu', $footer_menu);
			
			return $smarty->fetch($template_path);
			
		}
		
	}