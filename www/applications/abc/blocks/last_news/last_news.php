<?php

	class LastNewsBlock extends Block {
		
		public function run($params=array()) {
			
			$url = Router::getSourceUrl();
			if ($url) return $this->terminate();
						
			$news = Application::getObjectInstance('news');
			
			$load_params = array();
			$load_params['mode'] = 'front';
			$load_params['limit'] = 3;
			$load_params['where'][] = 'active=1';
			$load_params['order'] = 'date DESC';
			
			$news = $news->load_list($load_params);
			if (!$news) return $this->terminate();
			
			foreach($news as $item) {
				$item->link = Application::getSeoUrl("/news/detail/$item->id");
			}

			$static_dir = Application::getBlockUrl($this->getName()) . '/static';
			
			$page = Application::getPage();
			$page->AddStylesheet("$static_dir/css/last_news.css");
			
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			
			$smarty->assign('static_dir', $static_dir);
			$smarty->assign('news', $news);
			return $smarty->fetch($template_path);			
			
		}
		
	}