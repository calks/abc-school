<?php

	require_once Application::getApplicationDir() . '/libraries/profile_helper.php';

	class holidaysBlock extends Block {
		
		public function run($params=array()) {
			
			$from = date("Y-m-d", strtotime('-1 week'));
			$to = date("Y-m-d", strtotime('+1 week'));
									
			
			$holiday = Application::getEntityInstance('holiday');
			$table = $holiday->getTableName();
			$alias = $holiday->getTableAlias($table);
			
			$params['fields'][] = "DATE_FORMAT($alias.date, '%m') AS month";
			$params['fields'][] = "DATE_FORMAT($alias.date, '%d') AS day"; 			
			$params['where'][] = "(			
				$alias.date >= '$from' AND 
				$alias.date < '$to'
					OR
				$alias.repeat_annually AND 
				DATE_FORMAT($alias.date, '%m-%d') >= DATE_FORMAT('$from', '%m-%d') AND 
				DATE_FORMAT($alias.date, '%m-%d') < DATE_FORMAT('$to', '%m-%d')
			)";
			
			
			$user_session = Application::getUserSession();
			$user = $user_session->getUserAccount();
			
			
			if ($user->role == 'teacher') {
				$params['where'][] = "$alias.visibility IN('all', 'teachers_only')";
			}
			if ($user->role == 'student') {
				$params['where'][] = "$alias.visibility IN('all', 'students_only')";
			}
			
			$holidays = $holiday->load_list($params);
			
			//print_r($holidays); die();
			if (!$holidays) return '';
			
			$month_names = array(
				1 => 'января',
				2 => 'февраля',
				3 => 'марта',
				4 => 'апреля',
				5 => 'мая',
				6 => 'июня',
				7 => 'июля',
				8 => 'августа',
				9 => 'сентября',
				10 => 'октября',
				11 => 'ноября',
				12 => 'декабря'
			);
			
			foreach ($holidays as $h) {
				$h->date_str = (int)$h->day . ' ' . $month_names[(int)$h->month];
			}
			
			$smarty = Application::getSmarty();			
			$template_path = $this->getTemplatePath();
			$smarty->assign('holidays_list', $holidays);
			return $smarty->fetch($template_path);
			
		} 
		
		
	}