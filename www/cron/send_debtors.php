<?php

	date_default_timezone_set('Asia/Novosibirsk');
    /*error_reporting(E_ALL & ~E_STRICT);
    ini_set('display_errors', 1);*/
	error_reporting(0);
	
    include '../core/libraries/core/application.php';

   	$application_name = 'abc_admin';
   
    Application::init($application_name);

    Application::loadLibrary('misc');
    Application::loadLibrary('core/dataobject');
    Application::loadLibrary('core/debug');
    Application::loadLibrary('page_properties_helper');
    Application::loadLibrary('debtors_helper');

	Application::loadLibrary('olmi/request');
	Application::loadLibrary('core/module');
	Application::loadLibrary('core/block');
	Application::loadLibrary('core/router');

	Application::loadLibrary('olmi/object');
	Application::loadLibrary('olmi/class');
    
	
	$period_start = debtorsHelper::getCurrentPeriodStart();
	$period_end = debtorsHelper::getCurrentPeriodEnd();  
	
	
	$debtors = debtorsHelper::getDebtors($period_start, $period_end);
	if (!$debtors) die();
	$body = debtorsHelper::getHtml($debtors, $period_start, $period_end);
	

	//$emails = array('alexey@cyberly.ru');
	$emails = array(
		'irina-larchenko1@mail.ru',
		'elenbegun@hotmail.com', 
		'school.abc@mail.ru'
	);
	
	Application::loadLibrary('olmi/MailSender');
	
	foreach ($emails as $e) {
		$msg = MailSender::createMessage();            
		$msg->setSubject(strip_tags($subject));
		$msg->setFrom('no-reply@abc-school.ru', 'Лингвоцентр ABC');
		$msg->setReplyTo('no-reply@abc-school.ru', 'Лингвоцентр ABC');
		$msg->setBody($body, "text/html", "utf-8", "8bit");
		$msg->addTo($e);
		MailSender::send($msg);		
	}
	
	//die($body);

	
	
	
	
	
	
