<?php

	class IndexpicsBlock extends Block {
		
		public function run($params=array()) {
			
			$url = Router::getSourceUrl();
			if ($url) return $this->terminate();
			
			$static_dir = Application::getBlockUrl($this->getName()) . '/static';
			
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			
					
			$smarty->assign('static_dir', $static_dir);
			return $smarty->fetch($template_path);
			
		}
		
	}