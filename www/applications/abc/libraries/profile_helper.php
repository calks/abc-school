<?php

	class profileHelperLibrary {
	
		public static function getFromDateMysql($fieldname='from') {
			$raw = trim(Request::get($fieldname));
			if (preg_match('/^(\d)+\.(\d)+\.(\d)+$/', $raw)) {
				return preg_replace('/(\d+)\.(\d+)\.(\d+)/', '$3-$2-$1', $raw);
			}
			else {				
				return date('Y-m-d', strtotime('-1 month'));	
			}			
		}
		
		
		public static function getToDateMysql($fieldname='to') {
			return self::getFromDateMysql($fieldname);			
		}
		
		public static function addPopupInfoLinks(&$data) {
			if (!$data) return;
			if (!self::canViewProfile()) return;
			
			foreach ($data as $user_id=>&$item) {
				$item['info_link'] = Application::getSeoUrl("/profile/info/$user_id");
			}
			
			//print_r($data);
			
		}
		
		
		protected static function getLoggedUserRole() {
			$user_session = Application::getUserSession();
			$user_logged = $user_session->getUserAccount();
			if (!$user_logged) return null;
			return $user_logged->role;
		} 

		
		public static function canOpenAttendanceTab() {
			$role = self::getLoggedUserRole();
			return in_array($role, array('admin', 'manager', 'teacher', 'director', 'student'));
		}
		
		public static function canOpenHomeworkTab() {
			$role = self::getLoggedUserRole();
			return in_array($role, array('admin', 'manager', 'teacher', 'director', 'student'));
		}
		
		public static function canOpenPaymentTab() {
			$role = self::getLoggedUserRole();
			return in_array($role, array('admin', 'teacher', 'director', 'student'));
		}
		
		public static function canOpenMarksTab() {
			$role = self::getLoggedUserRole();
			return in_array($role, array('admin', 'manager', 'teacher', 'director', 'student'));
		}
		
		
		public static function canEditAttendance() {
			$role = self::getLoggedUserRole();
			
			switch ($role) {
				case 'admin':				
				case 'director':
				case 'teacher':
					return true;	
				default: 
					return false;
			}
		}
		
		
		public static function canEditHomework() {
			$role = self::getLoggedUserRole();
			
			switch ($role) {
				case 'admin':
				case 'teacher':
					return true;	
				default: 
					return false;
			}
		}
		
		
		public static function canEditMarks() {
			$role = self::getLoggedUserRole();
			
			switch ($role) {
				case 'admin':
				case 'teacher':
					return true;	
				default: 
					return false;
			}
		}
		
		
		public static function canEditStudentNotes($group_id) {
			$role = self::getLoggedUserRole();
			
			switch ($role) {
				case 'admin':				
				case 'director':
				case 'teacher':
					return true;	
				default: 
					return false;
			}
		}
				

		public static function canViewProfile() {
			$role = self::getLoggedUserRole();
			return in_array($role, array('admin', 'manager', 'teacher', 'director'));
		}
		
		
		public static function canEditProfile() {
			$role = self::getLoggedUserRole();
			return in_array($role, array('admin'));
		}
		
		public static function canEditGroupData() {
			$role = self::getLoggedUserRole();
			return in_array($role, array('admin', 'manager', 'teacher', 'director'));
		}
		
		public static function canEditPayment() {
			$role = self::getLoggedUserRole();
			return in_array($role, array('admin', 'manager'));
		}
		
		public static function getGroupStudents($group_id, $filter_params=array()) {
			require_once Application::getSitePath() . '/applications/abc_admin/filters/user.php';
			$user_filter = new UserFilter();
			$group_id = (int)$group_id;
			
			$filter_params['search_group'] = array($group_id);
			
			foreach($user_filter->fields as $fieldname=>$field) {
				$user_filter->setValue($fieldname, isset($filter_params[$fieldname]) ? $filter_params[$fieldname] : null);
			}
			
			$user = Application::getEntityInstance('user');
			$user_table = $user->getTableName();
			$user_alias = $user->getTableAlias($user_table);
			
			$load_params = array();
			$user_filter->set_params($load_params);
			$load_params['fields'][] = "$user_alias.id AS user_id"; 
			$load_params['where'][] = "$user_alias.active = 1";
			$load_params['where'][] = "$user_alias.role = 'student'"; 
			$load_params['order_by'][] = "user_name";
			
			
			return $user->load_list($load_params);
			
		}
		
		
	}
	
	
	
	
	
	
	
	