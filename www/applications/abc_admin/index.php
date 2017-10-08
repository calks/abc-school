<?php

	Application::loadLibrary('olmi/request');
	Application::loadLibrary('core/module');
	Application::loadLibrary('core/block');
	Application::loadLibrary('core/router');

	Application::loadLibrary('olmi/object');
	Application::loadLibrary('olmi/class');
	
	Application::loadLibrary('olmi/editor');

	
	
	$url = ltrim($_SERVER['REQUEST_URI'], '/');
	
	Router::setDefaultModuleName('admin_index');
	
	Router::route($url);

	$page = Application::getPage();

	
	$page->addStylesheet('/styles.css');
	$page->addStylesheet('/dropdown/dropdown.css');
	$page->addStylesheet('/dropdown/themes/admin/default.css');
	
	$page = Application::getPage();
	$page->addScript('/js/jquery.js');
	$page->addScript('/js/func.js');
	$page->addScript('/js/jquery-ui/jquery-ui.min.js');
	$page->addStylesheet(Application::getSiteUrl() . '/applications/abc_admin/static/js/jquery-ui/css/ui-lightness/jquery-ui.css');
	
	
	$smarty = Application::getSmarty();

	$module_name = Router::getModuleName();
	$module_params = Router::getModuleParams();
	
	$user_session = Application::getUserSession();
	$user_logged = $user_session->getUserAccount();
	if ($module_name != 'admin_login') {
		if (!$user_logged) Redirector::redirect('/admin/admin_login');
		if($user_logged->role != 'admin') {
			$user_session->logout();
			Redirector::redirect('/admin/admin_login');	
		}			
	}
	

	$module_found = Application::loadModule($module_name);
	if ($module_found) {
		$content = Application::runModule($module_name, $module_params);
	} else {
		$content = Application::runModule('page404');
	}

	if (Request::get('content_only')) die($content);

	$smarty->assign('message', Request::get('message'));
	
	$smarty->assign('content', $content);
	
	$header = Application::getBlockContent('admin_header');
	$smarty->assign('header', $header);

	
	
	$page->addScript('/js/jquery.js');

	$html_head = $page->getHtmlHead();
	$smarty->assign('html_head', $html_head);

	$smarty->display('index.tpl');

