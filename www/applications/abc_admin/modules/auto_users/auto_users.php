<?php

	Application::loadLibrary('core/admin_module');
	
	class AutoUsersModule extends AdminModule {
		
		protected $credentials;
		
		protected function taskList() {
		
		}

		protected function getCredentials($prefix='s') {
			if (!$this->credentials) $this->credentials = array();
			$login = $prefix . rand(100000, 999999);
			while(array_key_exists($login, $this->credentials)) {
				$login = $prefix . rand(100000, 999999);	
			}
			
			return array(
				'login' => $login,
				'pass' => rand(100000, 999999)
			);
		}
		
		
		protected function getGroupId($group_title, $branch_title) {
			
			$db = Application::getDb();

			$branch_title = trim($branch_title);
			$sbranch_title = addslashes($branch_title);
			
			$branch = Application::getEntityInstance('user_group_branch');
			$branch_table = $branch->getTableName();
			
			$period_id = CURRENT_PERIOD_ID;
			
			$branch_id = $db->executeScalar("
				SELECT id 
				FROM $branch_table
				WHERE title='$sbranch_title' AND period_id=$period_id
			");
			if (!$branch_id){
				$branch->title = $branch_title;
				$branch_id = $branch->save();
			}			
			
			
			
			$group_title = trim($group_title);
			$sgroup_title = addslashes($group_title);

			
			$group = Application::getEntityInstance('user_group');
			$group_table = $group->getTableName();
			$existing_group_id = $db->executeScalar("
				SELECT id 
				FROM $group_table
				WHERE 
					title='$sgroup_title' AND
					branch_id=$branch_id
			");
			if ($existing_group_id) return $existing_group_id;
			
			$group->title = $group_title;
			$group->branch_id = $branch_id;
			return $group->save();
		}
		
		protected function taskCreate() {
			$db = Application::getDb();
			$period_id = CURRENT_PERIOD_ID;
			
			$db->execute("
				DELETE FROM user
				WHERE role='student' AND (period_id=$period_id OR period_id IS NULL)
			");
			$db->execute("
				DELETE FROM user_group WHERE period_id=$period_id
			");
			$db->execute("
				DELETE FROM user_group_branch WHERE period_id=$period_id
			");
			
			
			$static_dir = Application::getSitePath() . '/applications/abc_admin/modules/auto_users/static';
			$csv_in_dir = "$static_dir/in";
			$csv_out_dir = "$static_dir/out";
			
			
			$groups = array();
			
			$d = opendir($csv_in_dir);
			while($file=readdir($d)) {
				if (in_array($file, array('.', '..'))) continue;
				$in_path = $csv_in_dir . "/$file";
				$out_path = $csv_out_dir . "/$file";
			
				$in = fopen($in_path, 'r');
				$out = fopen($out_path, 'w');
				
				$values = array();
				$group_coupling = array();
				
				
				$is_filename_utf = preg_match('/[а-я]/isu', $file);				
				$file_utf = $is_filename_utf ? $file : iconv('cp1251', 'utf-8', $file);
				$branch_title = trim(str_replace('.csv', '', $file_utf));
				
				
				if (mb_strpos($file_utf, 'Менеджеры') !== false) $role = 'manager';
				elseif (mb_strpos($file_utf, 'Преподаватели') !== false) $role = 'teacher';
				else $role = 'student';
				
				$group_id = null;
				$prev_row_type = null; 
				while(($row = fgets($in, 1000)) && !feof($in)) {
					$row = iconv('cp1251', 'utf-8', $row);
					$is_empty = !trim(str_replace(';', '', $row));
					if ($is_empty) continue;
										
					$row = explode(';', $row);					
					$row[0] = trim($row[0]);
					if(!$row[0]) continue;
					
					
					if (strpos($row[0], 'Расписание') !== false) {
						$row_type = 'schedule';
						$row_type_str = 'расписание';
					}
					elseif(preg_match('/[0-9a-z]+/', $row[0])) {
						$row_type = 'group_name';
						$row_type_str = 'группа';
					}
					else {
						$row_type = 'student';
						$row_type_str = 'студент';
					}
					
					
					switch ($row_type) {
						case 'group_name':
							fputcsv($out, array(iconv('utf-8', 'cp1251', $row[0])), ';', '"');
							$group_name = $row[0];
							$group_name = preg_replace('/\s+/', ' ', $group_name);
							$group_name = preg_replace('/\s,/', ',', $group_name);						
							$group_id = $this->getGroupId($group_name, $branch_title);
							
							if (!isset($groups[$branch_title])) {
								$groups[$branch_title] = array();
							}							
							if(in_array($group_name, $groups[$branch_title])) {
								echo "<span style=\"color:red\">[$branch_title][$group_name] error: two groups with same name</span><br>"; die();								
							}
							
							$groups[$branch_title][] = $group_name;
								
							
							break;
						case 'schedule':												
							$schedule_data = $this->parseScheduleRow($row[0], $branch_title);
							foreach ($schedule_data as $day_number=>$starts_at) {
								if (!$day_number) continue;
								$schedule_entry = Application::getEntityInstance('user_group_schedule');
								$schedule_entry->user_group_id = $group_id;
								$schedule_entry->weekday = $day_number;
								$schedule_entry->starts_at = $starts_at;
								$schedule_entry->deleted=0;
								$schedule_entry->save();
							}
							
							//print_r($schedule);
							
							break;
						case 'student':
							$credentials = $this->getCredentials(substr($role, 0, 1));
							$login = $credentials['login'];
							if (!isset($group_coupling[$group_id])){
								$group_coupling[$group_id] = array();
							}
							$group_coupling[$group_id][] = "'" . addslashes($login) . "'";
							
							
							$pass = $credentials['pass'];
							$name = $row[0];
							
							$cell_phone = $row[1];							
							$cell_phone = preg_replace('/[^\d]/', '', $cell_phone);
							
							
							$name = explode(' ', $name);
							$lastname = addslashes(array_shift($name));
							$firstname = addslashes(implode(' ', $name));
							$period_id = CURRENT_PERIOD_ID;
							$values[] = "('$login', '$pass', '$firstname', '$lastname', '$role', '$cell_phone', '$period_id')";
							
							$out_row = array();
							$out_row[] = iconv('utf-8', 'cp1251', "$firstname $lastname");							
							$out_row[] = $login;
							$out_row[] = $pass;					
							fputcsv($out, $out_row, ';', '"');		
							break;							
						default:
							echo "<span style=\"color:red\">[$branch_title] error: unknown row type</span><br>".print_r($row,1); die();	
					}
					
					
					if($row_type == 'schedule') {						
						
					}
					
					
					if ($prev_row_type == 'group_name' && $row_type == 'group_name') {						
						echo "<span style=\"color:red\">[$branch_title] error: two groups one by one </span><br>".print_r($row,1); die();
					}
					
					$prev_row_type = $row_type;
					
					
				}
				
				fclose($in);
				fclose($out);
				
				//print_r($values); die();
				
				$chunks = array_chunk($values, 100);
				foreach ($chunks as $c) {
					$c = implode(',', $c);
					$sql = "
						INSERT INTO user (login, pass, firstname, lastname, role, cell_phone, period_id) VALUES $c
					";
					//echo $sql;
					$db->execute($sql);
				}

				$user_group = Application::getEntityInstance('user_group');
				$coupling_table = $user_group->getCouplingTableName(); 
				foreach ($group_coupling as $group_id=>$login_list) {
					$login_list = implode(',', $login_list);
					$sql = "
						INSERT INTO $coupling_table
						SELECT 
							id AS user_id,
							$group_id as group_id
						FROM user
						WHERE login IN($login_list)
					";
								
					$db->execute($sql);
				}
				
			}
			closedir($d);
			
			
			redirector::redirect('/admin/auto_users');
		
		} 
		
		
		protected function parseScheduleRow($schedule_row, $branch_title) {
			$schedule_row = mb_strtolower($schedule_row, 'utf-8');
			$schedule_row = str_replace('расписание', '', $schedule_row);						
			
			$day_names = array(
				1 => 'понедельник',
				2 => 'вторник',
				3 => 'среда',
				4 => 'четверг',
				5 => 'пятница',
				6 => 'суббота',
				7 => 'воскресенье'
			);
			
			$rday_names = array_flip($day_names);
			
			$day_regex = implode('|', $day_names);
			$time_regex = "\d{1,2}(:|\.)\d{2}";
			
			$schedule_row = str_replace(',', ' ', $schedule_row);
			
			$same_begin_time_matched = preg_match("/(?P<day1>$day_regex)\-(?P<day2>$day_regex)\s+(?P<begins1>$time_regex)/isuU", $schedule_row, $same_time_matches);
			$different_begin_time_matched = preg_match("/(?P<day1>$day_regex)\s+(?P<begins1>$time_regex)\s+(?P<day2>$day_regex)\s+(?P<begins2>$time_regex)/isuU", $schedule_row, $different_time_matches);
			$different_begin_end_time_matched = !$different_begin_time_matched && preg_match("/(?P<day1>$day_regex)\s+(?P<begins1>$time_regex)\s*\-\s*(?P<ends1>$time_regex)\s+(?P<day2>$day_regex)\s+(?P<begins2>$time_regex)\s*\-\s*(?P<ends2>$time_regex)/isuU", $schedule_row, $different_time_matches);
			
			$day1_name = isset($same_time_matches['day1']) ? $same_time_matches['day1'] : null;
			$day2_name = isset($same_time_matches['day2']) ? $same_time_matches['day2'] : null;
			
			$day1_number = $day1_name ? $rday_names[$day1_name] : null;
			$day2_number = $day2_name ? $rday_names[$day2_name] : null;
			
			$begins1 = isset($same_time_matches['begins1']) ? str_replace('.', ':', $same_time_matches['begins1']) : null;
			$begins2 = isset($same_time_matches['begins1']) ? str_replace('.', ':', $same_time_matches['begins2']) : null;
			
			$schedule = array();
			
			if ($same_begin_time_matched) {				
				$schedule[$day1_number] = $begins1;
				$schedule[$day2_number] = $begins1;
			}
			elseif($different_begin_time_matched || $different_begin_end_time_matched) {
				$schedule[$day1_number] = $begins1;
				$schedule[$day2_number] = $begins2;
			}
			else {
				echo "<span style=\"color:red\">[$branch_title] error: cant't parse schedule</span><br>".print_r($schedule_row,1);
				die();
			}
			
			return $schedule;						
		}
		
		protected function taskPrint() {
			$db = Application::getDb();
			$data = $db->executeSelectAllObjects("
				SELECT * FROM user
				WHERE email IS NULL OR email=''
			");

			$students = array_chunk($data, 4);
			
			$smarty = Application::getSmarty();
			$smarty->assign('students', $students);
			$template_path = $this->getTemplatePath('print');
			die($smarty->fetch($template_path));		
		}
		
		
	}