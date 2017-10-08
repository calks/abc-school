<?php


    Application::loadFilter('base');

    class UserFilter extends BaseFilter {
    	
    	var $period_start_mysql;
    	var $period_end_mysql;
    	
    	
    	protected static $field_relations;
    		
        function add_fields() {
        	$user = Application::getEntityInstance('user');
        	
        	$this->addField(new TSelectField('search_role', '', $user->getRoleSelect('Любая'))); 
        	$this->addField(new TEditField('search_keyword', ''));
        	$limit_options = array(        		
        		20 => '20',
        		50 => '50',
        		100 => '100',
        		'all' => 'все'
        	);
        	$this->addField(new TSelectField('search_limit', '', $limit_options));
        	
        	$active_options = $this->getActiveOptions();
        	$this->addField(new TSelectField('search_active', '', $active_options));
        	
        	
        	$branch_options = $this->getBranchOptions();
        	$this->addField(new CollectionCheckBoxField('search_branch', $branch_options, array(), 3));
        	
        	$teacher_options = $this->getTeacherOptions();
        	$this->addField(new CollectionCheckBoxField('search_teacher', $teacher_options, array(), 3));
        	
        	$group = Application::getEntityInstance('user_group');
        	$this->addField(new CollectionCheckBoxField('search_group', $group->getSelect('не назначена'), array(), 3));
        	
        	$this->addField(new TSelectField('search_debtors', '', $this->getDebtorOptions()));
        	
        }
        
        
        function getActiveOptions() {
        	return array(        		
        		'any' => 'всех',
        		'active_only' => 'только активных',
        		'inactive_only' => 'только неактивных'
        	);
        }
        
        
        function getDebtorOptions() {
        	$payment = Application::getEntityInstance('user_payment');
        	$out = array(
	        	null => 'Не показывать'
        	);
        	
        	foreach ($payment->getPaymentYearOptions() as $k=>$v) {
        		$out[$k] = "за $v год";
        	}
        	
        	return $out;
        }

        function set_params(&$params) {
        	
        	parent::set_params($params);
        	
            $db = Application::getDb();
            
            $user = Application::getEntityInstance('user');
            $roles = $user->getRoleSelect();
            $role = $this->getValue('search_role');
            
            $table = $user->getTableName();
            $alias = $user->getTableAlias($table);

            if (array_key_exists($role, $roles)) {
            	$params['where'][] = "$alias.role='$role'";
            }
            
            $keyword = trim($this->getValue('search_keyword'));
            if ($keyword) {
            	$keyword = preg_replace('/\s+/is', ' ', $keyword);
            	$condition = array();
            	$words = explode(' ', $keyword);
            	
            	foreach($words as $w) {
            		$w = addslashes($w);
            		$condition[] = "( 
	            		$alias.firstname LIKE '%$w%' OR
	            		$alias.lastname LIKE '%$w%' OR
	            	  	$alias.email LIKE '%$w%' 
					)";
            	}
            	  	
				$condition = implode(' OR ', $condition);
				
            	$params['where'][] = "($condition)";
            }
            
            
            $group_id = $this->getValue('search_group');
            if ($group_id) {
            	$include_unassigned = false;
            	$group_id_processed = array();
            	
            	foreach($group_id as $index => &$id) {            		
            		$id = (int)$id;	
            		if ($id) $group_id_processed[] = $id;
            		else $include_unassigned = true; 
            	}

            	$group = Application::getEntityInstance('user_group');
            	$coupling_table = $group->getCouplingTableName();
            	$coupling_alias = $group->getTableAlias($coupling_table);
            	
            	
            	$condition = array();
            	if ($group_id_processed) {
            		$group_id = implode(',', $group_id_processed);
            		$condition[] = "$coupling_alias.group_id IN($group_id)";
            	}
            	if ($include_unassigned) {
            		$condition[] = "$coupling_alias.group_id IS NULL";
            	}
            	
            	if ($condition) {
            		$condition = implode(' OR ', $condition);
            		$params['where'][] = "($condition)";
            	}
            }
            
            
            $branch_id = $this->getValue('search_branch');
            if ($branch_id) {
            	$branch_ids = array();
            	
            	foreach($branch_id as $bid) {            		
            		$bid = (int)$bid;	
            		if ($bid) $branch_ids[] = $bid;            		 
            	}

            	if ($branch_ids) {
            		
            		$branch_ids = implode(',', $branch_ids);
	            	$group = Application::getEntityInstance('user_group');
	            	$group_table = $group->getTableName();
	            	$group_alias = $group->getTableAlias($group_table);
	
	           		$params['where'][] = "$group_alias.branch_id IN($branch_ids)";	            		
            	}
            }
            
            
			$teacher_id = $this->getValue('search_teacher');
            if ($teacher_id) {
            	$teacher_ids = array();
            	
            	foreach($teacher_id as $tid) {            		
            		$tid = (int)$tid;	
            		if ($tid) $teacher_ids[] = $tid;            		 
            	}

            	if ($teacher_ids) {
            		
            		$teacher_ids = implode(',', $teacher_ids);
	            	$user = Application::getEntityInstance('user');
	            	$user_table = $user->getTableName();
	            	$user_alias = $user->getTableAlias($user_table);	            	
	            	
	            	$group = Application::getEntityInstance('user_group');
	            	$coupling_table = $group->getCouplingTableName();
	            	$coupling_alias = $group->getTableAlias($coupling_table);

	            	$params['from'][] = "
	            		INNER JOIN $coupling_table {$coupling_alias}_2 ON {$coupling_alias}_2.user_id = $user_alias.id
	            	";
	            	
	            	$params['from'][] = "
	            		LEFT JOIN $coupling_table {$coupling_alias}_3 ON {$coupling_alias}_3.group_id = {$coupling_alias}_2.group_id
	            	";
	            	
	            	
	            	$params['from'][] = "
	            		INNER JOIN $user_table teachers ON 
	            			teachers.id = {$coupling_alias}_3.user_id 
	            			AND teachers.role='teacher'
	            	";
	            	
	            	
	            	$params['where'][] = "teachers.id IN($teacher_ids)";
	
	           		    		
            	}
            }            
            
            $search_active = $this->getValue('search_active');
            $active_options = $this->getActiveOptions();
            if (!array_key_exists($search_active, $active_options)) {
            	$keys = array_keys($active_options);
            	$search_active = $keys[0];
            	$this->setValue('search_active', $search_active);
            	$this->saveToSession(Application::getApplicationName());
            }
            if ($search_active == 'active_only') {
            	$params['where'][] = "$alias.active=1";
            }
            elseif ($search_active == 'inactive_only') {
            	$params['where'][] = "$alias.active=0";
            }
            
            
            $search_debtors_year = (int)$this->getValue('search_debtors');
            if ($search_debtors_year) {
            	
            	$current_year = date("Y");
            	$current_month = date("m");
            	
            	$month_before_ny = 4;
            	$month_after_ny = 5;
            	
            	$start_year = $search_debtors_year;
            	$end_year = $search_debtors_year+1;
            	
            	$month_to_pay_count = 0;
            	if ($start_year < $current_year) {
            		$month_to_pay_count += $month_before_ny; 
            	}
            	elseif($start_year==$current_year && $current_month >= 9) {
            		$month_to_pay_count += ($current_month - 9);
            	}

            	if ($end_year < $current_year) {
            		$month_to_pay_count += $month_after_ny; 
            	}
            	elseif($end_year==$current_year) {            		
            		$to_add = ($current_month) > $month_after_ny ? $month_after_ny : ($current_month);
            		$month_to_pay_count += $to_add;
            	}
            	
            	
            	$period_start_date = "$search_debtors_year-09-01";
            	$period_end_date = ($search_debtors_year+1) . "-05-01";
            	
            	
            	$this->period_start_mysql = $period_start_date;
            	$this->period_end_mysql = $period_end_date;
            	 
            	
            	$payment = Application::getEntityInstance('user_payment');
            	$payment_table = $payment->getTableName();
            	$payment_alias = $user->getTableAlias($payment_table);
            	$params['from'][] = "
            		LEFT JOIN $payment_table $payment_alias ON 
            			$payment_alias.user_id = $alias.id AND
            			$payment_alias.payment_period_begin_date >= '$period_start_date' AND
            			$payment_alias.payment_period_begin_date <= '$period_end_date'
            	";
            	
            			
            	$params['fields'][] = "$month_to_pay_count AS month_to_pay_total";
            	$params['fields'][] = "
            		SUM(IF($payment_alias.user_id IS NOT NULL, 1, 0)) AS payed_month_count
            	";
            	
            	$current_period = date("Y-m-01");
            	$params['fields'][] = "
            		SUM(IF($payment_alias.payment_period_begin_date='$current_period', 1, 0)) AS current_month_payed
            	";
            	
            	
            	$params['having'][] = "($month_to_pay_count - payed_month_count) >=2 OR current_month_payed=0";
            	
            }
            
            
            
        }
        
        
		protected function getFilterRelations() {
			
			if (is_null(self::$field_relations)) {
				$group = Application::getEntityInstance('user_group');
				
				self::$field_relations = $group->load_list();
				$mapping = array();
				
				foreach (self::$field_relations as $item) {
					$item->teacher_id = null;
					$item->teacher_name = null;
					$mapping[$item->id] = $item;
				}
				
				if ($mapping) {
					$group_ids = implode(',', array_keys($mapping));
					$user = Application::getEntityInstance('user');
					$user_table = $user->getTableName();
					$group_coupling_table = $group->getCouplingTableName();
					$group_table = $group->getCouplingTableName();
					
					$db = Application::getDb();
					$teacher_data = $db->executeSelectAllObjects("
						SELECT 
							$user_table.id AS teacher_id,
							CONCAT ($user_table.firstname, ' ', $user_table.lastname) AS teacher_name,							
							$group_coupling_table.group_id
						FROM 
							$user_table
							LEFT JOIN $group_coupling_table ON $group_coupling_table.user_id = $user_table.id
						WHERE 
							$user_table.role='teacher' AND
							$group_coupling_table.group_id IN($group_ids)	
					");
							
					foreach ($teacher_data as $d) {
						$mapping[$d->group_id]->teacher_id = $d->teacher_id;
						$mapping[$d->group_id]->teacher_name = $d->teacher_name;
					}
				}
			}
			//print_r(self::$field_relations);
			return self::$field_relations;
		}
		
		
		protected function getBranchOptions() {
			$out = array();
			
			foreach ($this->getFilterRelations() as $fr) {
				$out[$fr->branch_id] = $fr->branch_name;
			}
			
			return $out;
		}
		
		protected function getTeacherOptions() {		
			$out = array();
			
			foreach ($this->getFilterRelations() as $fr) {
				if (!$fr->teacher_id) continue;				 
				$out[$fr->teacher_id] = $fr->teacher_name;
			}
			
			return $out;
		}
		
		protected function getBranchTeacherRelationsOptions() {
			$out = array();
			
			foreach ($this->getFilterRelations() as $fr) {
				if (!$fr->branch_id) continue;
				$out[$fr->branch_id] = $fr->branch_title;
			}
			
			return $out;
		}
		
		public function getRelationMap() {
			$out = array(
				'branch_teacher' => array(),
				'branch_group' => array(),
				'teacher_group' => array()			
			);
			
			foreach ($this->getFilterRelations() as $fr) {
				
				if (!isset($out['branch_teacher'][$fr->branch_id])) {
					$out['branch_teacher'][$fr->branch_id] = array();					
				}
				$out['branch_teacher'][$fr->branch_id][$fr->teacher_id] = $fr->teacher_id;
				
				if (!isset($out['branch_group'][$fr->branch_id])) {
					$out['branch_group'][$fr->branch_id] = array();					
				}
				$out['branch_group'][$fr->branch_id][$fr->id] = $fr->id;
				
				if (!isset($out['teacher_group'][$fr->teacher_id])) {
					$out['teacher_group'][$fr->teacher_id] = array();					
				}
				$out['teacher_group'][$fr->teacher_id][$fr->id] = $fr->id;
				
			}
			
			
			return $out;
		
		}
		
	}
	
	
	
	
	
	
	
	