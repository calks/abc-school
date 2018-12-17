<?php

	Application::loadLibrary('core/admin_module');
	
	class adminDebtorsModule extends AdminModule {
		
				
		
		protected function taskList() {
			Application::loadLibrary('debtors_helper');
			$period_start = debtorsHelper::getCurrentPeriodStart();
			$period_end = debtorsHelper::getCurrentPeriodEnd();
			$debtors = debtorsHelper::getDebtors($period_start, $period_end);			
			$html = debtorsHelper::getHtml($debtors, $period_start, $period_end);
			
			header("Content-Type: text/html");
			header("Content-Length: ".strlen($html));              
			$filename = rawurlencode("Должники на " . date('d.m.Y'));
			header("Content-Disposition: attachment; filename*=UTF-8''$filename");
			
			
			die($html);
		}


	}