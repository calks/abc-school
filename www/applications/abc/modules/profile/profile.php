<?php

	Application::loadLibrary('profile_helper');

	class profileModule extends Module {
		
		protected $user;
		protected $task;
		
		protected $branch_id;
		protected $teacher_id;
		protected $group_id;
		protected $attendance_from;
		protected $attendance_from_mysql;
		protected $attendance_to;		
		protected $attendance_to_mysql;
		
		
				
		public function run($params=array()) {
			
			$document = Application::getEntityInstance('document');
			$document = $document->loadToUrl($this->getName());
			pagePropertiesHelper::setTitleDescKeysFromObject($document);
			
			$this->task = @array_shift($params);

			$user_session = Application::getUserSession();
			//if (!$user_session->userLogged()) Redirector::redirect(Application::getSeoUrl("/login"));
			
			$this->user = $user_session->getUserAccount();		
			
			$static_dir = Application::getModuleUrl($this->getName()) . '/static';			
			$page = Application::getPage();
			$page->AddStylesheet("$static_dir/css/profile.css");
			$page->addScript('/applications/abc/static/js/jquery-ui/jquery-ui.min.js');
			$page->addStylesheet(Application::getSiteUrl() . '/applications/abc/static/js/jquery-ui/css/ui-lightness/jquery-ui.css');
			
			//if (profileHelperLibrary::canEditProfile()) {
				$page->AddScript("$static_dir/js/info_popup.js");	
			//}
				
			
			$this->attendance_from = Request::get('from');
			if (preg_match('/^(\d)+\.(\d)+\.(\d)+$/', $this->attendance_from)) {
				$this->attendance_from_mysql = preg_replace('/(\d+)\.(\d+)\.(\d+)/', '$3-$2-$1', $this->attendance_from);
			}
			else {
				$this->attendance_from = date('d.m.Y', strtotime('-1 month'));				
				$this->attendance_from_mysql = date('Y-m-d', strtotime('-1 month'));	
			}
			
			$this->attendance_to = Request::get('to');
			if (preg_match('/^(\d)+\.(\d)+\.(\d)+$/', $this->attendance_to)) {
				$this->attendance_to_mysql = preg_replace('/(\d+)\.(\d+)\.(\d+)/', '$3-$2-$1', $this->attendance_to);
			}
			else {
				$this->attendance_to = date('d.m.Y', strtotime('+1 month'));;
				$this->attendance_to_mysql = date('Y-m-d', strtotime('+1 month'));;	
			}
			
			$smarty = Application::getSmarty();
			
			
			if (!$this->task) $this->task = 'info';
			$method_name = 'task' . ucfirst($this->task);
			if (method_exists($this, $method_name)) {
				call_user_func(array($this, $method_name), $params);
			}
			else {
				return $this->terminate();
			}
			
			$page_template = $this->getTemplatePath($this->task);
			
			$smarty->assign('user', $this->user);
			$page_content = $smarty->fetch($page_template);
			
			$smarty->assign('document', $document);
			$smarty->assign('page_content', $page_content);			
			$smarty->assign('menu', $this->getMenu());
			
			
			$main_template = $this->getTemplatePath('wrap');
			return $smarty->fetch($main_template);
			
		}
		
		protected function getMenu() {
			
			if (!$this->user) return null;
			
			$items = array();
			
			
			$items['info'] = array(
				'name' => 'Карточка',
				'link' => Application::getSeoUrl("/{$this->getName()}/info")			
			);
			
			$items['attendance'] = array(
				'name' => 'Посещаемость',
				'link' => Application::getSeoUrl("/{$this->getName()}/attendance")			
			);
			
			$items['homework'] = array(
				'name' => 'Домашние задания',
				'link' => Application::getSeoUrl("/{$this->getName()}/homework")			
			);
			
			$items['payment'] = array(
				'name' => 'Оплата обучения',
				'link' => Application::getSeoUrl("/{$this->getName()}/payment")			
			);
			
			
			$items['logout'] = array(
				'name' => 'Выход',
				'link' => Application::getSeoUrl("/login/logout")
			);
			
			
			$smarty = Application::getSmarty();
			
			$smarty->assign('items', $items);
			$smarty->assign('task', $this->task);
			$template_path = $this->getTemplatePath('menu');
			return $smarty->fetch($template_path);
			
		}
		
		protected function getProfileForm($user_id) {			
			Application::loadLibrary('olmi/form');
			$profile_form = new BaseForm();
			
			$editable_fields = $this->getInfoEditableFields($user_id);
			
			
			$profile_form->addField(new THiddenField('user_id', $user_id));
			$profile_form->addField(new TEditField('firstname', '', 50, 100));
			$profile_form->addField(new TEditField('lastname', '', 50, 100));
			$profile_form->addField(new TEditField('email', '', 50, 100));
			$profile_form->addField(new TTextField('info', '', 50, 4));
			
			if (in_array('new_pass', $editable_fields)) {
				$profile_form->addField(new TPasswordField('new_pass', '', 50, 100));
				$profile_form->addField(new TPasswordField('new_pass_confirmation', '', 50, 100));
			}
			
			if (in_array('parents', $editable_fields)) {
				$profile_form->addField(new TEditField('parents', '', 30, 255));
			}

			if (in_array('phone', $editable_fields)) {
				$profile_form->addField(new TEditField('phone', '', 30, 255));
			}

			if (in_array('cell_phone', $editable_fields)) {
				$profile_form->addField(new TEditField('cell_phone', '', 30, 255));
			}
			
			
			return $profile_form;			
		}
		
		protected function getInfoEditableFields($user_id) {
			$user = Application::getEntityInstance('user');
			$user_id = (int)$user_id;
			$user = $user->load($user_id);
			if (!$user) return array();
			
			$user_role = $user->role;
			$editing_own_profile = $user_id == $this->user->id;
			
			$out = array(
				'firstname',
				'lastname',
				'email',
				'info'
			);
			
			if ($editing_own_profile) {
				$out[] = 'new_pass';
				$out[] = 'new_pass_confirmation';
			}
			
			if (profileHelperLibrary::canEditProfile()) {
				$out[] = 'cell_phone';
				if ($user_role == 'student') {
					$out[] = 'parents';
					$out[] = 'phone';
				}
			}
			
			return $out;
		}
		
		
		protected function emailIsUnique($email, $user_id) {
			$db = Application::getDb();
			$table = $this->user->getTableName();
			$id = (int)$user_id;
			if (!$id) return false;
			if (!$email) return true;
						
			$email = addslashes($email);

			$sql = "
				SELECT COUNT(*)
				FROM $table
				WHERE email='$email' AND id!=$id
			";
			
			return !(bool)$db->executeScalar($sql);
		}
		
		protected function validateProfileForm($form) {
			$errors = array();
			
			if (!$form->getValue('firstname')) {
				$errors['firstname'] = "Нужно заполнить поле &laquo;Имя&raquo;";
			}
			
			if (!$form->getValue('lastname')) {
				$errors['lastname'] = "Нужно заполнить поле &laquo;Фамилия&raquo;";
			}

			$email = $form->getValue('email');
			$user_id = $form->getValue('user_id');  
			
			$email_is_mandatory = $this->user->role != 'admin' || $this->user->id == $user_id;
			
			if ($email_is_mandatory && !$email) {
				$errors['email'] = "Нужно заполнить поле &laquo;Email&raquo;";
			}
			else {
				if (!email_valid($email)) {
					$errors['email'] = "Вы ввели неправильный Email";	
				}
				elseif (!$this->emailIsUnique($email, $user_id)) {
					$errors['email'] = "Введенный Email используется другим пользователем";
				}
			}
			
			$pass = $form->getValue('new_pass');
			$pass_confirmation = $form->getValue('new_pass_confirmation');
			
			if ($pass != $pass_confirmation) {
				$errors['new_pass_confirmation'] = "Подтверждение и пароль не совпадают";
			}
			
			return $errors;
		}
		
		
		protected function taskSchedule_entries($params=array()) {
			$out = array();
			$entry_date = preg_replace('/^(\d+)\.(\d+)\.(\d+)$/', '$3-$2-$1', Request::get('entry_date'));
			
			if ($entry_date) {
				$group_id = (int)request::get('group_id');
				$entry_date = addslashes($entry_date);
				$schedule = Application::getEntityInstance('user_group_schedule');
				$table = $schedule->getTableName();
				
				$object_name = Request::get('object');
				
				$object = Application::getEntityInstance($object_name == 'homework' ? 'homework' : 'user_attendance');
				$object_table = $object->getTableName(); 
				
				$sql = "
					SELECT 
						$table.id,
						DATE_FORMAT($table.starts_at, '%H:%i') AS caption,
						SUM(IF($object_table.schedule_entry_id IS NOT NULL, 1, 0)) AS records_count
					FROM 
						$table 
						LEFT JOIN $object_table ON 
							$object_table.schedule_entry_id=$table.id AND
							$object_table.schedule_entry_date='$entry_date' 
					WHERE 
						$table.user_group_id=$group_id AND
						$table.weekday=IF(DATE_FORMAT('$entry_date', '%w')=0, 7, DATE_FORMAT('$entry_date', '%w')) AND
						$table.deleted=0 
					GROUP BY $table.id
					HAVING records_count=0
					ORDER BY $table.starts_at
				";						
						
						
				$db = Application::getDb();
				$data = $db->executeSelectAllObjects($sql);
				foreach($data as $d) {
					$out[$d->id] = $d->caption;
				}
			}			
			
			die(json_encode($out));
		}
		
		
		protected function checkGroupRights($entry_id, $entry_date) {
			
			if (!profileHelperLibrary::canEditGroupData()) {
				die(json_encode(array(
					'error' => 'У вас нет прав для заполнения журнала'
				)));
			}
						
			if (!$entry_id || !$entry_date) {
				die(json_encode(array(
					'error' => 'Ошибка в запросе'
				)));
			}
			
			$db = Application::getDb();
			$schedule = Application::getEntityInstance('user_group_schedule');
			$entry = $schedule->load($entry_id);
			if (!$entry) {
				die(json_encode(array(
					'error' => 'Ошибка в запросе'
				)));				
			}
			elseif ($this->user->role=='teacher' && !in_array($entry->user_group_id, $this->user->group_id)) {
				die(json_encode(array(
					'error' => 'Вы пытаетесь править журнал чужой группы'
				)));
			}
		}
		
		
		protected function taskSave_attendance($params=array()) {
			$entry_id = (int)Request::get('entry_id');
			$entry_date = preg_replace('/^(\d+)\.(\d+)\.(\d+)$/', '$3-$2-$1', Request::get('entry_date'));
			
			$this->checkGroupRights($entry_id, $entry_date);
			
			$schedule = Application::getEntityInstance('user_group_schedule');
			$entry = $schedule->load($entry_id);			
			
			$user_data = isset($_POST['users']) && $_POST['users'] ? $_POST['users'] : array();
			//print_r($user_data);
			
			$user_ids = array();
			foreach($user_data as $item) {
				$uid = $item['id'];
				if (!$uid) continue;
				$user_ids[] = $uid;
			}
			
			$user_ids = array_unique($user_ids);
			$db = Application::getDb();
			
			if ($user_ids && $this->user->role == 'teacher') {
				$auser_ids = implode(',', $user_ids);
				
				$group = Application::getEntityInstance('user_group');
				$group_coupling_table = $group->getCouplingTableName();
				
				$sql = "
					SELECT COUNT(*)
					FROM $group_coupling_table
					WHERE user_id IN($auser_ids) AND group_id=$entry->user_group_id
				";
				
				if ($db->executeScalar($sql) != count($user_ids)) {
					die(json_encode(array(
						'error' => 'Вы пытаетесь отметить посещение для пользователя из чужой группы'
					)));
				}
			}
			
			$attendance = Application::getEntityInstance('user_attendance');
			$table = $attendance->getTableName();
			
			$db->execute("
				DELETE FROM $table
				WHERE schedule_entry_id=$entry_id AND schedule_entry_date='$entry_date'  
			");
			

			$values = array();
			foreach ($user_data as $item) {
				$uid = (int)$item['id'];
				if (!$uid) continue;
				if (!$item['attendance'] && !$item['comment']) continue;
				$comment = addslashes($item['comment']);
				$values[] = "($uid, $entry_id, '$entry_date', '$comment')";
			}
			
			if($values) {				
				$values = implode(',', $values);
				$db->execute("
					INSERT INTO $table (user_id, schedule_entry_id, schedule_entry_date, comment) VALUES $values
				");
			}
			
			die(json_encode(array(
				'message' => 'Данные сохранены',
				'chart' => $this->getChartHtml($entry->user_group_id)
			)));
			
		}
		
		protected function taskStudent_lookup($params=array()) {
			if (!profileHelperLibrary::canEditProfile()) die();
			$name = Request::get('name');
			$name = addslashes($name);
			
			$user = Application::getEntityInstance('user');
			$user_table = $user->getTableName();
			$user_alias = $user->getTableAlias($user_table);
			
			$load_params['where'][] = "$user_alias.active=1";
			$load_params['where'][] = "$user_alias.role='student'";
			$load_params['where'][] = "($user_alias.firstname LIKE '%$name%' OR $user_alias.lastname LIKE '%$name%')";
			$load_params['limit'] = 20;
			$load_params['order_by'][] = "user_name ASC";
			
			$found = $user->load_list($load_params);
			//print_r($found);
			
			$out = array();
			foreach($found as $f) {
				if (!$f->group_id) continue;
				$out[] = array(
					'id' => $f->id,
					'name' => $f->user_name,
					'group_id' => array_shift($f->group_id),
					'group_title' => $f->group_title
				);
			}
			
			
			die(json_encode($out));
			
		}
		
		protected function taskSave_payment($params=array()) {			
			$entry_date = preg_replace('/^(\d+)\.(\d+)\.(\d+)$/', '$3-$2-$1', Request::get('entry_date'));
			
			if (!profileHelperLibrary::canEditPayment()) {
				die(json_encode(array(
					'error' => 'У вас нет прав для заполнения журнала'
				)));
			}
			
			
			$user_data = isset($_POST['users']) && $_POST['users'] ? $_POST['users'] : array();
			
			$user_ids = array();
			foreach($user_data as $item) {
				$uid = $item['id'];
				if (!$uid) continue;
				$user_ids[] = $uid;
			}
			
			$user_ids = array_unique($user_ids);
			$db = Application::getDb();
			$user_ids = implode(',', $user_ids);
			
			$payment = Application::getEntityInstance('user_payment');
			$table = $payment->getTableName();
			
			$db->execute("
				DELETE FROM $table
				WHERE 
					payment_period_begin_date='$entry_date' AND
					user_id IN($user_ids)
			");
			

			$values = array();
			$now = date("Y-m-d H:i:s");
			$user_id = null;
			foreach ($user_data as $item) {
				$uid = (int)$item['id'];
				if (!$uid) continue;
				$user_id = $uid;
				if (!$item['payed'] && !$item['comment']) continue;
				$comment = addslashes($item['comment']);
				$values[] = "($uid, '$entry_date', '$now', '$comment')";
			}
			
			if($values) {				
				$values = implode(',', $values);
				$db->execute("
					INSERT INTO $table (user_id, payment_period_begin_date, created, comment) VALUES $values
				");
			}
			
			$user = Application::getEntityInstance('user');
			$user = $user->load($user_id);
			$group_id = $user->group_id[0];
			$entry_date_year = (int)substr($entry_date, 0, 4);
			$entry_date_month = (int)substr($entry_date, 5, 2); 
			$start_year = $entry_date_month >= 9 ? $entry_date_year : $entry_date_year-1; 
			
			die(json_encode(array(
				'message' => 'Данные сохранены',
				'chart' => $this->getPaymentChartHtml($group_id, null, $start_year)
			)));
			
		}
		

		
		protected function taskSave_user_notes($params=array()) {
			
			$notes_raw = isset($_POST['notes']) ? $_POST['notes'] : array();

			$notes = array();
			foreach ($notes_raw as $user_id=>$note) {
				$user_id = (int)$user_id;
				if ($user_id) {
					$notes[$user_id] = trim($note, " \r\n");
				}
			}
					
			
			if (!$notes) {
				die(json_encode(array(
					'error' => 'Ошибка в данных'
				)));
			}
						
			
			$user = Application::getEntityInstance('user');
			$user_table = $user->getTableName();
			$user_alias = $user->getTableAlias($user_table);
			$user_ids_str = implode(',', array_keys($notes));
			
			$user_load_params['where'][] = "$user_alias.id IN($user_ids_str)";
			$users = $user->load_list($user_load_params);
			
			
			$teacher_logged = $this->user->role == 'teacher';
			$admin_logged = in_array($this->user->role, array('manager', 'admin'));
			
			$edit_granted = $teacher_logged || $admin_logged;
			if ($teacher_logged) {
				foreach ($users as $u) {					
					$same_group = count(array_intersect($this->user->group_id, $u->group_id)) != 0;					
					$edit_granted = $edit_granted && $same_group;
				}
			}
			
			
			if (!$edit_granted) {
				die(json_encode(array(
					'error' => 'У вас нет прав на изменение пользователей'
				)));
			}
				
			
			
			foreach ($users as $u) {
				$u->notes = $notes[$u->id];
				$u->save();
			}
			
			
			$out = array(
				'message' => 'Данные сохранены'				
			); 
			
			$start_year = Request::get('start_year');
			$group_id = $users[0]->group_id[0];
			$chart_type = Request::get('chart_type');
			
			if ($chart_type == 'payment') {
				$out['chart'] = $this->getPaymentChartHtml($group_id, null, $start_year);
			}			
			elseif ($chart_type == 'attendance') {
				$out['chart'] = $this->getChartHtml($group_id);
			}
				
			
			die(json_encode($out));
				
		}
		
		

		protected function taskSave_homework($params=array()) {
			$entry_id = (int)Request::get('entry_id');
			$entry_date = preg_replace('/^(\d+)\.(\d+)\.(\d+)$/', '$3-$2-$1', Request::get('entry_date'));
			
			$this->checkGroupRights($entry_id, $entry_date);
			
			$homework = Application::getEntityInstance('homework');
			$homework->id = $homework->findId($entry_id, $entry_date);
			
			
			$task = trim(Request::get('task'));
			
			if (!$task && $homework->id) {
				$homework->delete();
			} 
			else {
				$homework->description = $task;
				$homework->schedule_entry_id = $entry_id;
				$homework->schedule_entry_date = $entry_date;
				$homework->save();
			}
			
			$schedule = Application::getEntityInstance('user_group_schedule');
			$entry = $schedule->load($entry_id);			
			
			die(json_encode(array(
				'message' => 'Данные сохранены',
				'chart' => $this->getHomeworkChartHtml($entry->user_group_id)
			)));
			
		}
		
		
		
		protected function groupLogic() {
			$smarty = Application::getSmarty();			
			
			
			if ($this->user->role=='student') {
				$this->group_id = $this->user->group_id[0];
			}
			else {				
				$group_id_from_student_id = null;
				$student_id = (int)Request::get('student_id');
				if ($student_id) {
					$student = Application::getEntityInstance('user');
					$student = $student->load($student_id);
					if ($student && $student->role == 'student' && $student->active==1 && $student->group_id) {
						$group_id_from_student_id = array_shift($student->group_id);
					}
				}
				
				if ($group_id_from_student_id) {
					$this->group_id = $group_id_from_student_id;	
				}
				else {
					$is_manager = in_array($this->user->role, array('admin', 'manager')); 
					if ($is_manager) {
						$this->teacher_id = (int)Request::get('teacher');
					}
					else {
						$this->teacher_id = $this->user->id;
					}
					$this->branch_id = (int)Request::get('branch');			
					$this->group_id = (int)Request::get('group');
				}				
			}
			
			
			if ($this->group_id) {
				
				$groups = $this->getGroups();
				//print_r($groups);
				$smarty->assign('teacher_names', isset($groups[$this->group_id]) ? $groups[$this->group_id]->teacher_names : '');
				$smarty->assign('group_schedule_html', $this->getGroupScheduleHtml($this->group_id));
				$smarty->assign('group_title', isset($groups[$this->group_id]) ? $groups[$this->group_id]->name : '');
				
				if (!in_array($this->user->role, array('admin', 'manager')) && !in_array($this->group_id, $this->user->group_id)) {
					$this->task = 'authority_error';
					$this->terminate();
					$this->group_id = null;
					return;	
				}
				
				if (!$this->branch_id) {
					$group = Application::getEntityInstance('user_group');
					$group = $group->load($this->group_id);				
					if (!$group) return $this->terminate();
					$this->branch_id = $group->branch_id;
				}
			}
			
			$branch_options = array();
			$teacher_options = array();
			$group_options = array();
			
			if ($this->user->role == 'teacher') {
				$groups = $this->getGroups($this->user->group_id, $this->branch_id);
				$group = Application::getEntityInstance('user_group');
				$group_options = get_empty_select('-- Выберите группу --');
				$branch_options = $group->getBranchSelect('-- Выберите филиал --', $this->user->id);
				
				foreach($groups as $group_id=>$group) {										
					if ($this->branch_id && $group->branch_id != $this->branch_id) continue;
					$group_options[$group_id] = $group->name;
				}
							
			}
			else {
				$group = Application::getEntityInstance('user_group');
				$group_options = $group->getSelect('-- Выберите группу --', $this->branch_id, $this->teacher_id);
				$branch_options = $group->getBranchSelect('-- Выберите филиал --'); 
				$teacher = Application::getEntityInstance('user');				
			}
			
			//print_r($group_options);
			Application::loadLibrary('olmi/field');
			$group_select = new TSelectField('group', $this->group_id, $group_options);
			$branch_select = new TSelectField('branch', $this->branch_id, $branch_options);
			$teacher_select = $this->getTeacherSelect();
			
		
			$smarty->assign('group_id', $this->group_id);
			$smarty->assign('branch_id', $this->branch_id);
			$smarty->assign('group_select', $group_select);
			$smarty->assign('branch_select', $branch_select);
			$smarty->assign('teacher_select', $teacher_select);
			
			$smarty->assign('common_heading', $this->getTemplatePath('common_heading'));
			
			$holidays_block = Application::getBlockContent('holidays');
			$smarty->assign('holidays', $holidays_block);
			
			$from_select = new TJSCalendarField('from', $this->attendance_from);
			$to_select = new TJSCalendarField('to', $this->attendance_to);
			
			
			if (in_array($this->task, array('attendance', 'payment'))) {
				 
			}

			$smarty->assign('from_select', $from_select);
			$smarty->assign('to_select', $to_select);
			
			
			$smarty = Application::getSmarty();
		}

		
		protected function getTeacherSelect() {
			$can_search_by_teacher = in_array($this->user->role, array('admin', 'manager'));
			if (!$can_search_by_teacher) return null;
			$teacher = Application::getEntityInstance('user');
			$alias = $teacher->getTableAlias($teacher->getTableName());
			$params['where'][] = "$alias.role='teacher'";
			
			if($this->branch_id) {
				$group = Application::getEntityInstance('user_group');
				$group_table = $group->getTableName();								
				$group_table_alias = $group->getTableAlias($group_table);
				
				$params['where'][] = "$group_table_alias.branch_id=$this->branch_id";
				$params['where'][] = "$alias.role='teacher'";
			}
			
			$options = array(
				null => '-- Выберите преподавателя --'
			);
			foreach ($teacher->load_list($params) as $t) {
				$options[$t->id] = trim("$t->firstname $t->lastname");
			}
			
			return new TSelectField('teacher', $this->teacher_id, $options);
		}
		
		
		protected function getGroupScheduleHtml($group_id, $in_chart=false) {
			$group_id = (int)$group_id;
			if (!$group_id) return '';
			$schedule = Application::getEntityInstance('user_group_schedule');
			$weeksdays = $schedule->getWeekdays();
			$schedule = $schedule->loadCollection($group_id);
			if (!$schedule) return '';
			
			$no_schedule = true;
			foreach($schedule as $day_schedule) {
				if ($day_schedule) $no_schedule = false;
			}
			
			if ($no_schedule) return '';

			$smarty = Application::getSmarty();
			$smarty->assign('group_schedule', $schedule);
			$smarty->assign('weeksdays', $weeksdays);
			$smarty->assign('in_chart', $in_chart);
			$template_path = $this->getTemplatePath('schedule');
			return $smarty->fetch($template_path);
		}
		

		protected function taskAttendance($params=array()) {
			$smarty = Application::getSmarty();			
			
			if (profileHelperLibrary::canEditGroupData()) {
				$page = Application::getPage();
				$page->addScript('/applications/abc/static/js/jquery.mCustomScrollbar.min.js');
				$page->addStylesheet(Application::getApplicationUrl() . '/static/css/jquery.mCustomScrollbar.css');
				$page->addScript('/applications/abc/modules/profile/static/js/attendance.js?v=1.1');
			}
			
			$this->groupLogic();
			
			if ($this->group_id || $this->branch_id || $this->teacher_id) {			
				$smarty->assign('chart', $this->getChartHtml($this->group_id, $this->branch_id, $this->teacher_id));
			}
		}
		
		
		protected function taskHomework($params=array()) {
			if (profileHelperLibrary::canEditGroupData()) {
				$page = Application::getPage();
				$page->addScript('/applications/abc/modules/profile/static/js/homework.js');
			}
			
			$this->groupLogic();
			
			$smarty = Application::getSmarty();
			
			if ($this->group_id) {				
				$smarty->assign('chart', $this->getHomeworkChartHtml($this->group_id));
			}
		}
		
		
		
		protected function taskPayment($params=array()) {
			if (profileHelperLibrary::canEditPayment() || profileHelperLibrary::canEditGroupData()) {
				$page = Application::getPage();
				$page->addScript('/applications/abc/static/js/jquery.mCustomScrollbar.min.js');
				$page->addStylesheet(Application::getApplicationUrl() . '/static/css/jquery.mCustomScrollbar.css');
				$page->addScript('/applications/abc/modules/profile/static/js/attendance.js?v=1.1');
			}
			
			
			$start_year = (int)Request::get('payment_start_year');
			if (!$start_year) {
				$current_year = (int)date('Y');
				$current_month = (int)date('m');
				$start_year = $current_month < 9 ? $current_year-1 : $current_year;				
			}
			$end_year = $start_year + 1; 
			$this->attendance_from = "01.09.$start_year";
			$this->attendance_from_mysql = "$start_year-09-01";
			$this->attendance_to = "31.08.$end_year";
			$this->attendance_to_mysql = "$end_year-08-31";
			
			$_REQUEST['from'] = $this->attendance_from;
			$_REQUEST['to'] = $this->attendance_to; 
			
			$this->groupLogic();

			$group_month_price = '';
			$group_month_price_comment = '';
			if ($this->group_id) {
				$group = Application::getEntityInstance('user_group');
				$group = $group->load($this->group_id);
				$group_month_price = $group->month_price_str;
				$group_month_price_comment = $group->month_price_comment;
			}
			
			Application::loadLibrary('olmi/field');
			$payment = Application::getEntityInstance('user_payment');
			$year_options = $payment->getPaymentYearOptions();
			$year_select = new TSelectField('payment_start_year', $start_year, $year_options);
			
			
			$year_period_select = new TSelectField('payment_start_year_half', Request::get('payment_start_year_half'), array(
				'both' => 'весь год',
				'first' => 'сентябрь-декабрь',
				'second' => 'январь-май'
			));  
			
			$smarty = Application::getSmarty();
			$smarty->assign('chart', $this->getPaymentChartHtml($this->group_id, $this->branch_id, $start_year, $this->teacher_id));
			
			/*if ($this->group_id || $this->branch_id) {				
				$smarty->assign('chart', $this->getChartHtml($this->group_id, $this->branch_id));
			}*/
			
			$smarty->assign('year_select', $year_select);
			$smarty->assign('year_period_select', $year_period_select);
			$smarty->assign('group_month_price', $group_month_price);
			$smarty->assign('group_month_price_comment', $group_month_price_comment);

		}
		
		
		protected function taskInfo($params=array()) {			
			$is_ajax = Request::get('ajax');
			
			$user_id = (int)@array_shift($params);
			
			if (!$user_id) $user_id = $this->user->id;
			
			
			$user = Application::getEntityInstance('user');
			$user = $user->load($user_id);
			
			if (!$user) {
				if ($is_ajax) {
					die(json_encode(array(
						'error' => 'Пользователь не найден'
					)));
				}
				else return $this->terminate();
			}
			
			
			$viewer_group_id = $this->user->group_id ? $this->user->group_id[0] : null;
			$viewing_teacher = $this->user->role=='student' && $user->role == 'teacher' && in_array($viewer_group_id, $user->group_id);	
			$viewing_own_profile = $this->user->id == $user_id;

			if (!profileHelperLibrary::canEditProfile() && !$viewing_own_profile && !$viewing_teacher || $viewing_teacher && isset($_REQUEST['submit'])) {
				if ($is_ajax) {
					die(json_encode(array(
						'error' => 'Вы не можете редактировать данные других пользователей'
					)));
				}
				else return $this->terminate();
			};
			

			if ($user->group_id) {
				$groups = $this->getGroups($user->group_id);
				$group_names = array();
				foreach($groups as $g) $group_names[] = $g->name;
				$group_name = $group_names ? implode('<br>', $group_names) : '';
			}
			else {
				$group_name = 'не назначена';
			}
			
			$smarty = Application::getSmarty();
			
			$errors = array();			
			$message = Request::get('profile_saved') ? 'Данные сохранены' : '';  
			
			$form = $this->getProfileForm($user_id);
			$form->loadFromObject($user);

			$form_action = "/{$this->getName()}/$this->task";
			if ($user_id != $this->user->id) $form_action .= "/$user_id";
			
			
			if (isset($_REQUEST['submit']) && (!$viewing_teacher || $viewing_own_profile)) {
				$form->LoadFromRequest($_REQUEST);
				$errors = $this->validateProfileForm($form);
				if (!$errors) {
					$form->UpdateObject($user);
					$new_pass = $form->getValue('new_pass');
					if ($new_pass) $this->user->pass = $new_pass;
					$user->save();
					if ($is_ajax) {
						$message = 'Данные сохранены';
					}
					else {
						Redirector::redirect(Application::getSeoUrl("$form_action?profile_saved=1"));
					}
				}
			}
			
			$smarty->assign('is_ajax', $is_ajax);
			$smarty->assign('info_user', $user);
			$smarty->assign('viewing_teacher', $viewing_teacher);
			$smarty->assign('group_name', $group_name);
			$smarty->assign('form', $form);
			$smarty->assign('errors', $errors);
			$smarty->assign('message', $message);		
			$smarty->assign('form_action', Application::getSeoUrl($form_action));
			
			if ($is_ajax) {
				die(json_encode(array(
					'content' => $smarty->fetch($this->getTemplatePath($this->task)),
					'error' => implode('<br>', $errors),
					'message' => $message
				)));
			}
		}
		
		
		protected function getHomeworkChartHtml($group_id) {
			$homework = Application::getEntityInstance('homework');
			$homework_data = $homework->loadForGroup($group_id);
						
			$smarty = Application::getSmarty();
			$smarty->assign('homework_data', $homework_data);
			//$smarty->assign('can_edit', $this->user->role == 'teacher');
			$smarty->assign('can_edit', profileHelperLibrary::canEditGroupData());
			
			return $smarty->fetch($this->getTemplatePath('homework_chart'));			
		}
		
		
		
		protected function getGroupsForChart($branch_id, $teacher_id) {
			$group = Application::getEntityInstance('user_group');
			$table = $group->getTableName();
			$alias = $group->getTableAlias($table);
			
			$db = Application::getDb();
			$branch_id = (int)$branch_id;
			$teacher_id = (int)$teacher_id;
			
			$params = array();
			
			$not_sorted_branches = array(
				1, // Гимназия №11, Гармония
				16 // Офис, Б.Богаткова
			); 
			
			if ($branch_id) {
				$params['where'][] = "$alias.branch_id=$branch_id";
			}
			
			if ($teacher_id) {
				
        		$coupling_table = $group->getCouplingTableName();
        		$coupling_table_alias = $group->getTableAlias($coupling_table);
        		$params['from'][] = "
        			INNER JOIN $coupling_table $coupling_table_alias ON
        				$coupling_table_alias.group_id = $alias.id AND
        				$coupling_table_alias.user_id = $teacher_id
        		";
        	
			}
			
			$sort_by_lesson_start = !in_array($branch_id, $not_sorted_branches);
			
			if ($sort_by_lesson_start) {
				$schedule = Application::getEntityInstance('user_group_schedule');
				$schedule_table = $schedule->getTableName(); 
				$schedule_alias = $schedule->getTableAlias($schedule_table);
				
				$params['from'][] = "
					LEFT JOIN $schedule_table $schedule_alias
					ON $schedule_alias.user_group_id = $alias.id
				";
					
				$params['fields'][] = "MIN(CONCAT($schedule_alias.weekday, $schedule_alias.starts_at)) AS first_lesson_day_time";
				
				$params['where'][] = "$schedule_alias.deleted=0";				
				$params['group_by'][] = "$alias.id";
				$params['order_by'][] = "first_lesson_day_time";					
			}
			
			
			if ($this->user->role == 'teacher') {
				$teacher_groups = implode(',', $this->user->group_id);
				$params['where'][] = "$alias.id IN($teacher_groups)";
			}
			
			$group_list = $group->load_list($params);
			return $group_list;
		
		}
		
		protected function getChartHtml($group_id, $branch_id=null, $teacher_id=null) {
			if (($branch_id || $teacher_id) && !$group_id) {
				
				$groups = $this->getGroupsForChart($branch_id, $teacher_id);								
				if (!$groups) return array();
				
				$out = array();
				$smarty = Application::getSmarty();
				foreach($groups as $g) {
					$chart = $this->getChartHtml($g->id);
					
					$out[$g->id] = array(
						'schedule' => $this->getGroupScheduleHtml($g->id, true),
						'group_name' => $g->title,
						'data' => $smarty->_tpl_vars['attendance_data'], 
						'chart' => $chart
					);
				}
				
				return $out;			
			}

			
			$attendance = Application::getEntityInstance('user_attendance');
			$attendance_data = $attendance->loadForGroup($group_id, $this->attendance_from_mysql, $this->attendance_to_mysql);
			profileHelperLibrary::addPopupInfoLinks($attendance_data);
			
			//print_r($attendance_data);
						
			$smarty = Application::getSmarty();
			$smarty->assign('attendance_data', $attendance_data);
			$smarty->assign('attendance_data_' . $group_id, $attendance_data);
			
			$column_keys = array();
			if ($attendance_data) {
				$user_ids = array_keys($attendance_data);
				foreach($attendance_data[$user_ids[0]]['attendance'] as $time=>$a) {
					$column_keys[$time] = $a['entry_id'];	
				}
								
			} 
			
			$smarty->assign('column_keys', $column_keys);
			$smarty->assign('columns_count', count($column_keys));
			$smarty->assign('can_edit', profileHelperLibrary::canEditGroupData());
			$smarty->assign('can_edit_user_notes', profileHelperLibrary::canEditGroupData());
			
			return $smarty->fetch($this->getTemplatePath('attendance_chart'));
		
			
		}
		
		
		protected function getPaymentChartHtml($group_id, $branch_id, $start_year, $teacher_id=null) {
			
			if (!$group_id && !$branch_id && !$teacher_id) return '';
			
			if (!$group_id && ($branch_id || $teacher_id)) {
				$groups = $this->getGroupsForChart($branch_id, $teacher_id);
				if (!$groups) return array();				
				$out = array();
				$smarty = Application::getSmarty();
				
				
				foreach($groups as $g) {
					$chart = $this->getPaymentChartHtml($g->id, null, $start_year);	
					if (!$chart) continue;				
					$data = isset($smarty->_tpl_vars['payment_data']) ? $smarty->_tpl_vars['payment_data'] : array();
					
					$out[$g->id] = array(
						'schedule' => $this->getGroupScheduleHtml($g->id, true),
						'month_price' => $g->month_price_str,
						'month_price_comment' => $g->month_price_comment,
						'group_name' => $g->title,
						'data' => $data, 
						'chart' => $chart
					);
				}
				
				return $out;
			}
			
			$smarty = Application::getSmarty();
			$payment = Application::getEntityInstance('user_payment');
			$payment_data = $payment->loadForGroup($group_id, $start_year, (bool)Request::get('debtors_only'));
			
			profileHelperLibrary::addPopupInfoLinks($payment_data);
						
			if (!$payment_data) return '';
			
			$column_keys = array();
			$user_ids = array_keys($payment_data);
			foreach($payment_data[$user_ids[0]]['payments'] as $period_start=>$data) {
				$column_keys[$period_start] = $data['caption'];	
			}
			
			//print_r($payment_data);die();
									 
			
			$smarty->assign('payment_data', $payment_data);
			$smarty->assign('column_keys', $column_keys);
			$smarty->assign('columns_count', count($column_keys));
			$smarty->assign('can_edit', profileHelperLibrary::canEditPayment());
			$smarty->assign('can_edit_user_notes', profileHelperLibrary::canEditGroupData());
			$smarty->assign('start_year', $start_year);
			
			
			$chart = $smarty->fetch($this->getTemplatePath('payment_chart'));			
			return $chart;
		
			
		}
		
		protected function getGroups($by_ids = null, $branch_id=null) {
			$group = Application::getEntityInstance('user_group');
			
			$table = $group->getTableName();
			$alias = $group->getTableAlias($table);
						
			$coupling_table = $group->getCouplingTableName();
			$coupling_alias = $group->getTableAlias($coupling_table);
			
			$load_params['from'][] = "
				LEFT JOIN $coupling_table $coupling_alias
				ON $coupling_alias.group_id = $alias.id			
			";
			
			$load_params['group_by'] = "$alias.id";
			$load_params['fields'][] = "COUNT($coupling_alias.user_id) AS user_count";
			
			
			if ($by_ids) {
				foreach ($by_ids as &$id) $id = (int)$id;
				$by_ids = implode(',', $by_ids);
				
				$load_params['where'][] = "$alias.id IN($by_ids)";
			}
			
			$branch_id = (int)$branch_id;
			if($branch_id) {
				$load_params['where'][] = "$alias.branch_id = '$branch_id'";
			}
			
			$groups = $group->load_list($load_params);
			
			if (!$groups) return array();
			
			$mapping = array();
			foreach($groups as $g) {
				$mapping[$g->id] = $g;
				$g->teachers = array();
			}
			
			$group_ids = array_keys($mapping);
			$group_ids = implode(',', $group_ids);
			
			$load_params = array();
			$user = Application::getEntityInstance('user');
			$user_table = $user->getTableName();
			$user_alias = $user->getTableAlias($user_table);
			
            $coupling_table = $group->getCouplingTableName();
            $coupling_alias = $group->getTableAlias($coupling_table);
			
			
			$load_params['where'][] = "$user_alias.active=1";
			$load_params['where'][] = "$user_alias.role IN ('teacher', 'manager')";
			$load_params['where'][] = "$coupling_alias.group_id IN($group_ids)";
			
			$users = $user->load_list($load_params);

			$roles = $user->getRoleSelect();
			foreach($users as $u) {
				$u->role_str = $roles[$u->role];

				foreach($u->group_id as $g) {
					$mapping[$g]->teachers[] = $u;
				}		
			}
			
			
			$out = array();
			
			foreach($groups as $g) {
				$entry = new stdClass();
				
				
				$entry->name = $g->title;
				//$entry->opened_before = coreFormatHelperLibrary::date($g->opened_before, false);				
				//$entry->education_starts = coreFormatHelperLibrary::date($g->education_starts, false);
				$entry->branch_id = $g->branch_id;
				$entry->description = $g->description; 
				$entry->teachers = array();
				$entry->teacher_names = '';
				
				$teacher_names = array(); 
				
				foreach ($g->teachers as $t) {
					$teacher_name = $t->firstname . ' ' . $t->lastname;
					//if (profileHelperLibrary::canEditProfile()) {
						$teacher_link =  Application::getSeoUrl("/profile/info/$t->id");
						$view_teacher = profileHelperLibrary::canEditProfile() ? '' : 'teacher';
						$teacher_names[] = "<a class=\"user_info $view_teacher\" href=\"$teacher_link\">$teacher_name</a>";
					/*}
					else {
						$teacher_names[] = $teacher_name;
					}*/
				}
				
				if ($teacher_names) {
					$entry->teacher_names = count($teacher_names)==1 ? '<b>Преподаватель</b><br />' : '<b>Преподаватели</b><br />';
					$entry->teacher_names .= implode(', ', $teacher_names);
				}
				
				
				$out[$g->id] = $entry;
				
			}
				
			
			return $out;
			
		}
		
		

		
		
	}
	
	
	
	
	