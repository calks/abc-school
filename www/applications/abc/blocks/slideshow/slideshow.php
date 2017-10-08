<?php

	class SlideshowBlock extends Block {
		
		public function run($params=array()) {
			
			$url = Router::getSourceUrl();
			if ($url) return $this->terminate();

			$static_dir = Application::getBlockUrl($this->getName()) . '/static';
			
			$page = Application::getPage();
			$page->AddStylesheet("$static_dir/css/slideshow.css");
			$page->AddScript("$static_dir/js/cycle.js");
			
			$slideshow_image = Application::getObjectInstance('slideshow_image');
			$slideshow_image = $slideshow_image->load_list();
			
			if (!$slideshow_image) return $this->terminate();
			
			$images = array();
			foreach($slideshow_image as $i) {			
				$images[] = PHOTOS_URL . "/slideshow_image/$i->id/slide/$i->image"; 
			}
			
			
			$smarty = Application::getSmarty();
			$template_path = $this->getTemplatePath();
			
			$doc = Application::getObjectInstance('document');
			$page = $doc->loadToUrl('index');
			$smarty->assign('page_content', $page->content);
			
			$smarty->assign('static_dir', $static_dir);
			$smarty->assign('images', $images);
			return $smarty->fetch($template_path);
			
			
		}
		
	}