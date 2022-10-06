<?php

	class user extends DataObject {
		var $id;
		var $firstname;
		var $lastname;
		var $email;
		var $login;
		var $pass;
		var $active;
		var $role;
		var $parents;
		var $phone;
		var $cell_phone;
		var $info;
		var $is_hidden;
		var $notes;
		var $period_id;

		function __construct() {
			$this->group_id = array();
		}
		
		function get_table_name() {
			return 'user';
		}

		function mandatory_fields() {
			return array(
				//'email' => 'Email',
				'login' => 'Логин',
				'pass' => 'Пароль'
			);
		}

		function unique_fields() {
			return array(
				//'email' => 'Email',
				'login' => 'Login'
			);
		}

		function order_by() {
			return '`active` ASC, `id` DESC';
		}

		function make_form(&$form) {
			$form->addField(new THiddenField('id'));
			$form->addField(new TEditField('firstname', '', 30, 100));
			$form->addField(new TEditField('lastname', '', 30, 100));
			$form->addField(new TEditField('email', '', 30, 100));
			$form->addField(new TEditField('login', '', 30, 100));
			$form->addField(new TEditField('pass', '', 30, 100));
			$form->addField(new TCheckboxField('active', 1));
			$form->addField(new TSelectField('role', '', $this->getRoleSelect()));
			
			$group = Application::getEntityInstance('user_group');
			$group_options = $group->getSelect('-- Не назначена --');
			$form->addField(new TSelectField('group_select', '', $group->getSelect('-- Не назначена --')));
			$form->addField(new CollectionCheckBoxField('group_id', $group->getSelect(), array(), 3));
			
			$form->addField(new TEditField('parents', '', 30, 255));
			$form->addField(new TEditField('phone', '', 30, 255));
			$form->addField(new TEditField('cell_phone', '', 30, 255));
			$form->addField(new TTextField('info', '', 50, 4));
			
			return $form;
		}
		
		
		function validate() {			
			$errors = parent::validate();
			$email_error = $this->getEmailError();
			if ($email_error) $errors['email'] = $email_error;
			
			return $errors;
		}
		
		
		function emailIsUnique() {
			$id = (int)$this->id;
			$email = addslashes($this->email);
			
			$db = Application::getDb();
			$table = $this->getTableName();
						
			
			$period_id = CURRENT_PERIOD_ID;
			
			
			$found = (int)$db->executeScalar("
				SELECT COUNT(*)
				FROM $table
				WHERE 
					period_id = $period_id AND
					email = '$email' AND
					id != $id
			");

			return $found==0;			
			
		}
		
		function getEmailError() {
			if (!$this->email) return null;
			
			if (!email_valid($this->email)) {
				return 'Неправильный Email';
			}
						
			if (!$this->emailIsUnique()) {
				return 'Email уже назначен другому пользователю';
			}
		}
		
		function getRoleSelect($add_null_item=false) {
			$out = get_empty_select($add_null_item);
			
			$out['admin'] = 'Администратор';
			$out['director'] = 'Директор';
			$out['manager'] = 'Менеджер';
			$out['teacher'] = 'Преподаватель';
			$out['student'] = 'Ученик (родитель)';
			
			return $out;
		}
		
		
		function save() {
			$this->email = strtolower($this->email);
			
			if ($this->role == 'student') {
				$this->period_id = CURRENT_PERIOD_ID;
			}
			else {
				$this->period_id = null;
			}			
			
			$user_id = parent::save();
			
			if ($user_id) {
				$group = Application::getEntityInstance('user_group');
				$group->saveCoupling($user_id, $this->group_id);
			}
			
			return $user_id;
			
		}
		
		protected function set_load_params(&$params) {
			$table = $this->getTableName();
			$alias = $this->getTableAlias($table);
			
			$group = Application::getEntityInstance('user_group');
			$group_table = $group->getTableName();
			$group_alias = $group->getTableAlias($group_table);
			
			$group_coupling_table = $group->getCouplingTableName();
			$group_coupling_alias = $group->getTableAlias($group_coupling_table);
			
			
			$period_id = CURRENT_PERIOD_ID;
			$params['where'][] = "(`$alias`.`period_id` = $period_id OR `$alias`.`period_id` IS NULL)";       	
			
			
			$params['from'][] = "
				LEFT JOIN $group_coupling_table $group_coupling_alias ON $group_coupling_alias.user_id = $alias.id
			";
			
			$params['from'][] = "
				LEFT JOIN $group_table $group_alias ON $group_alias.id = $group_coupling_alias.group_id
			";
			
			$params['fields'][] = "CONCAT($alias.lastname, ' ', $alias.firstname) AS user_name";
						
			$params['group_by'] = "$alias.id";						
			
		}

		
		public function load_list($params=array()) {
			
			$this->set_load_params($params);
			
			$list = parent::load_list($params);			
			$group = Application::getEntityInstance('user_group');
			$group->loadCoupling($list);
			
			foreach($list as $item) {
				$item->group_title = $item->group_title ? implode('<br />', $item->group_title) : '';
				//$item->profile_edit_url = Application::getSeoUrl('/profile/') 
			}
			
			return $list;
		}
		
		
		public function count_list($params=array()) {
			$this->set_load_params($params);			
			return parent::count_list($params);
		}
		
		
		public function getIdByEmail($email) {
			$table = $this->getTableName();
			$db = Application::getDb();
			$email = addslashes($email);
			
			$period_id = CURRENT_PERIOD_ID; 
			
			return $db->executeScalar("
				SELECT id FROM $table
				WHERE email = '$email' AND period_id=$period_id
			");
			
		}
		
		

	}
