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

	Application::loadLibrary('olmi/request');
	Application::loadLibrary('core/module');
	Application::loadLibrary('core/block');
	Application::loadLibrary('core/router');

	Application::loadLibrary('olmi/object');
	Application::loadLibrary('olmi/class');
    
	
	$user = Application::getEntityInstance('user');
	$user_table = $user->getTableName();
	$user_alias = $user->getTableAlias($user_table);
	$user_filter = Application::getFilter('user');
	foreach($user_filter->fields as $fieldname=>$field) {
		$user_filter->setValue($fieldname, null);
	}
	
	$current_year = (int)date('Y');
	$current_month = (int)date('m');
	$period_start = $current_month >= 9 ? $current_year : $current_year-1;
	$period_end = $period_start+1;  
	
	$user_filter->setValue('search_debtors', $period_start);
	
	$load_params = array();
	$user_filter->set_params($load_params);
	$load_params['where'][] = "$user_alias.active = 1";
	$load_params['where'][] = "$user_alias.role = 'student'"; 
	$load_params['order_by'][] = "user_name";
	//$load_params['limit'] = 10;
	
	$debtors_raw = $user->load_list($load_params);
	
	$mapping = array();
	
	$debtors = array();
	foreach($debtors_raw as $dr) {
		if (!$dr->group_id) continue;
		$dr->payed_month_list = array();
		$dr->unpayed_month_list = array();
		$debtors[] = $dr;		
		$mapping[$dr->id] = $dr;
	}
	
	
	if (!$debtors) die();
	
	$payment = Application::getEntityInstance('user_payment');	
	$payment_table = $payment->getTableName();	
	$debtors_ids = implode(',', array_keys($mapping));	
	$period_start_date = $user_filter->period_start_mysql;
	$period_end_date = $user_filter->period_end_mysql;
	
	$db = Application::getDb(); 
	$payed_month_data = $db->executeSelectAllObjects("
		SELECT 
			user_id, 
			payment_period_begin_date
		FROM $payment_table
		WHERE 
			user_id IN($debtors_ids) AND
            payment_period_begin_date >= '$period_start_date' AND
            payment_period_begin_date <= '$period_end_date'
	");
	
	foreach($payed_month_data as $pmd) {
		$mapping[$pmd->user_id]->payed_month_list[] = $pmd->payment_period_begin_date;
	}

	$months_to_pay = $payment->getMonthsToPay($period_start);
	foreach($debtors as $d) {
		foreach ($months_to_pay as $date=>$name) {
			if (!in_array($date, $d->payed_month_list)) {
				$d->unpayed_month_list[] = $name;
			}
		}
	}
	
	
	$now = date('d.m.Y');
	$subject = "Список должников за $period_start-$period_end год \n по состоянию на $now";
	
	
	ob_start(); 
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $subject?></title>

<style type="text/css">
	body, div, p, td, th, span, h1, h2, h3 {
		margin: 0;
		padding: 0;
		font-family: Arial, sans-serif;
		font-size: 12px;
		font-weight: normal;
		border: none;
	}
	
	table {
		border: 1px solid black;
		border-collapse: collapse;
	}
	
	th, td {
		border: 1px solid black;
		padding: 3px 10px;
	}
	
	
	th {
		background: #ddd;
		font-weight: bold;
	}

	body {		
		width: 700px;
		padding-bottom: 20px;
	}
	
	
	#content {
		padding: 20px;		
	}
	
	#content h1 {
		font-size: 18px;
		margin-bottom: 20px;
	}
	
	#content h2 {		
		margin: 20px 0 10px;
	}
	
	#content h3 {		
		margin: 10px 0;
	}
	
	#content p {
		margin: 10px 0;
		line-height: 130%; 
	}
		
	
	#footer * {
		color: #828282;
		line-height: 130%;		
	}
	
	#footer p {
		margin-top: 20px;		 
	}

</style>

</head>
<body>
	<div id="content">
		<h1><?php echo nl2br($subject)?></h1>
			
		<table>
			<tr>
				<th>ФИО</th>
				<th>Группа</th>
				<th>Неоплаченные месяцы</th>				
			</tr>
			
			<?php foreach ($debtors as $d): ?>
				<tr>
					<td width="50%"><?php echo $d->user_name?></td>
					<td><?php echo $d->group_title?></td>
					<td align="center"><?php echo implode('<br>', $d->unpayed_month_list)?></td>				
				</tr>
			
			<?php endforeach; ?>
		
		</table>
	</div>

</body>
</html><?php

	$body = ob_get_clean();
	//die($body);
	//$emails = array('alexey@cyberly.ru');
	$emails = array(
		'irina-larchenko1@mail.ru',
		'elenbegun@hotmail.com', 
		'school.abc@mail.ru'
	);
	
	Application::loadLibrary('olmi/MailSender');
	
	foreach ($emails as $e) {
		$msg = MailSender::createMessage();            
		$msg->setSubject(encode_header_utf_8(strip_tags($subject)));
		$msg->setFrom('no-reply@abc-school.ru', encode_header_utf_8('Лингвоцентр ABC'));
		$msg->setReplyTo('no-reply@abc-school.ru', encode_header_utf_8('Лингвоцентр ABC'));
		$msg->setBody($body, "text/html", "utf-8", "8bit");
		$msg->addTo($e);
		MailSender::send($msg);		
	}
	
	//die($body);

	
	
	
	
	
	
