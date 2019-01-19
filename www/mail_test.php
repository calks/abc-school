<?php

	error_reporting(E_ALL); ini_set('display_errors', 1);

	include 'core/libraries/core/application.php';
	
	
    Application::init('abc_admin');

    Application::loadLibrary('misc');
    Application::loadLibrary('core/dataobject');
    Application::loadLibrary('core/debug');
    Application::loadLibrary('page_properties_helper');
    
	require_once 'core/libraries/olmi/MailSender2.php';


	$message = "
		<h1>Тестовый Email</h1>

		<p>
			Либерализм неоднозначен. Политическое учение Августина, тем более в условиях социально-экономического кризиса, неизбежно. Отметим также, что политическое манипулирование приводит современный кризис легитимности. Социализм, с другой стороны, важно вызывает теоретический гуманизм. Политическое учение Фомы Аквинского, как бы это ни казалось парадоксальным, иллюстрирует элемент политического процесса.
		</p>
		<p>
			Христианско-демократический национализм практически обретает субъект политического процесса. Политическое учение Локка формирует антропологический доиндустриальный тип политической культуры. Очевидно, что референдум неизменяем. Правовое государство символизирует континентально-европейский тип политической культуры, о чем писали такие авторы, как Н.Луман и П.Вирилио.
		</

	";
            
	$msg = MailSender::createMessage();
            
	$msg->setSubject("Тестовое сообщение");
	$msg->setFrom('no-reply@abc-school.ru', 'Лингвоцентр ABC');
	$msg->setReplyTo('no-reply@abc-school.ru', 'Лингвоцентр ABC');
	$msg->setBody($message, "text/html", "utf-8", "8bit");
	$msg->addTo('alexey@cyberly.ru');
	$msg->addTo('test-l45ga@mail-tester.com');
	
	$sent = MailSender::send($msg);
	var_dump($sent);
	
	//978fX543x0X6m9s