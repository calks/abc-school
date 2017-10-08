<?php

	class AdminMenuBlock extends Block {
		
		public function run($params=array()) {
			
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			return $smarty->fetch($template_path);
			
		}
		
	}