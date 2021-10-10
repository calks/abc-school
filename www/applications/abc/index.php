<?php

	Application::loadLibrary('olmi/request');
	Application::loadLibrary('core/module');
	Application::loadLibrary('core/block');
	Application::loadLibrary('core/router');

	Application::loadLibrary('olmi/object');
	Application::loadLibrary('olmi/class');
	
	$url = ltrim($_SERVER['REQUEST_URI'], '/');
	//if(!$url) $url = 'index';

	Router::route($url);
	
	pagePropertiesHelper::setTitleDescKeysFromDocument();

	$smarty = Application::getSmarty();

	$page = Application::getPage();

	$page->addMeta(array(
		'http-equiv' => 'Content-Type',
		'content' => 'text/html; charset=UTF-8'
	));
		
	$page->addStylesheet('style.css');
	$page->addStylesheet('text.css');
	
	
	$page->addScript('/js/jquery.js');
	$page->addScript(Application::getApplicationUrl() . '/static/js/func.js');
	$page->addScript(Application::getApplicationUrl() . '/static/js/init.js');
		
	
	$module_name = Router::getModuleName();
	$module_params = Router::getModuleParams();

	$module_found = Application::loadModule($module_name);
	if ($module_found) { 		
		$doc = pagePropertiesHelper::getDocument();				
		if (is_object($doc) && !$doc->active) { 
			$content = Application::runModule('page404');
		}
		else {					
			if (is_object($doc) && $doc->access == 'registered') {				
				$user_session = Application::getUserSession();
				if (!$user_session->userLogged()) {
					Redirector::redirect(Application::getSeoUrl("/login?redirect=" . rawurlencode($url)));
				}
			}
			
			$content = Application::runModule($module_name, $module_params);
		}
	} else {
		$content = Application::runModule('page404');
	}

	if (Request::get('content_only')) die($content);

	$smarty->assign('content', $content);
	
	$header = Application::getBlockContent('header');
	$smarty->assign('header', $header);
		

	$indexpics = Application::getBlockContent('indexpics');
	$smarty->assign('indexpics', $indexpics);
		
	$hotlinks = Application::getBlockContent('hotlinks');
	$smarty->assign('hotlinks', $hotlinks);
	
	$slideshow = Application::getBlockContent('slideshow');
	$smarty->assign('slideshow', $slideshow);
	
	$last_news = Application::getBlockContent('last_news');
	$smarty->assign('last_news', $last_news);
	
	$footer = Application::getBlockContent('footer');
	$smarty->assign('footer', $footer);
	
	$html_head = $page->getHtmlHead('1.1');
	$smarty->assign('html_head', $html_head);

	$smarty->display('index.tpl');

