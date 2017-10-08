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
			$group_title = trim($group_title);
			$sgroup_title = addslashes($group_title);

			$db = Application::getDb();
			$group = Application::getEntityInstance('user_group');
			$group_table = $group->getTableName();
			$existing_group_id = $db->executeScalar("
				SELECT id 
				FROM $group_table
				WHERE title='$sgroup_title'
			");
			if ($existing_group_id) return $existing_group_id;
			
			$branch_title = trim($branch_title);
			$sbranch_title = addslashes($branch_title);
			
			
			$branch = Application::getEntityInstance('user_group_branch');
			$branch_table = $branch->getTableName();
			$branch_id = $db->executeScalar("
				SELECT id 
				FROM $branch_table
				WHERE title='$sbranch_title'
			");
			if (!$branch_id){
				$branch->title = $branch_title;
				$branch_id = $branch->save();
			}			
			
			$group->title = $group_title;
			$group->branch_id = $branch_id;
			return $group->save();
		}
		
		protected function taskCreate() {
			$db = Application::getDb();
			/*$db->execute("
				DELETE FROM user
				WHERE email IS NULL OR email=''
			");*/
			
			
			$static_dir = Application::getSitePath() . '/applications/abc_admin/modules/auto_users/static';
			$csv_in_dir = "$static_dir/in";
			$csv_out_dir = "$static_dir/out";
			
			$d = opendir($csv_in_dir);
			while($file=readdir($d)) {
				if (in_array($file, array('.', '..'))) continue;
				$in_path = $csv_in_dir . "/$file";
				$out_path = $csv_out_dir . "/$file";
				
				$in = fopen($in_path, 'r');
				$out = fopen($out_path, 'w');
				
				$values = array();
				$group_coupling = array();
				
				$file_utf = iconv('cp1251', 'utf-8', $file);
				$branch_title = trim(str_replace('.csv', '', $file_utf)); 
				if (mb_strpos($file_utf, 'Менеджеры') !== false) $role = 'manager';
				elseif (mb_strpos($file_utf, 'Преподаватели') !== false) $role = 'teacher';
				else $role = 'student';
				
				$group_id = null;
				
				while(($row = fgets($in, 1000)) && !feof($in)) {
					$row = array($row);					
					$row[0] = trim($row[0]);
					if(!$row[0]) continue;
					
					//$row[0] = 
					
					$is_group_name = preg_match('/[0-9a-z]+/', $row[0]);
					if ($is_group_name) {
						fputcsv($out, array($row[0]), ';', '"');
						$group_name = iconv('cp1251', 'utf-8', $row[0]);
						$group_name = preg_replace('/\s+/', ' ', $group_name);
						$group_name = preg_replace('/\s,/', ',', $group_name);						
						$group_id = $this->getGroupId($group_name, $branch_title);
						continue;
					}
					
					
					$credentials = $this->getCredentials(substr($role, 0, 1));
					$login = $credentials['login'];
					if (!isset($group_coupling[$group_id])){
						$group_coupling[$group_id] = array();
					}
					$group_coupling[$group_id][] = "'" . addslashes($login) . "'";
					
					
					$pass = $credentials['pass'];
					$name = iconv('cp1251', 'utf-8', $row[0]);					
					$name = explode(' ', $name);
					$lastname = addslashes(array_shift($name));
					$firstname = addslashes(implode(' ', $name));
					$values[] = "('$login', '$pass', '$firstname', '$lastname', '$role')";										
					$row[1] = $login;
					$row[2] = $pass;					
					fputcsv($out, $row, ';', '"');
					
				}
				
				fclose($in);
				fclose($out);
				
				//print_r($values); die();
				
				$chunks = array_chunk($values, 100);
				foreach ($chunks as $c) {
					$c = implode(',', $c);
					$sql = "
						INSERT INTO user (login, pass, firstname, lastname, role) VALUES $c
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