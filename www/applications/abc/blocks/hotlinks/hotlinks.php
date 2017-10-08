<?php

	class HotlinksBlock extends Block {
		
		public function run($params=array()) {
			
			$static_dir = Application::getBlockUrl($this->getName()) . '/static';
			
			$page = Application::getPage();
			$page->AddStylesheet("$static_dir/css/hotlinks.css");
			
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			
			$smarty->assign('static_dir', $static_dir);
			
			$smarty->assign('education_link', Application::getSeoUrl('/education_form'));
			$smarty->assign('career_link', Application::getSeoUrl('/career_form'));
			$smarty->assign('quizz_link', Application::getSeoUrl('/quizz'));
			
			return $smarty->fetch($template_path);
		}
		
	}