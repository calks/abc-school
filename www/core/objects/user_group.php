<?php

	class user_group extends DataObject {
		
		public $title;
		public $capacity;
		public $description;
		public $opened_before;
		public $education_starts;
		public $branch_id;
		public $month_price;
		public $month_price_comment;
		public $is_hidden;
		
		protected static $internal_branch_full_list;
		
		public function getTableName() {
			return 'user_group';
		}
		
		protected function getBranchFullList() {
			if (!self::$internal_branch_full_list) {
				self::$internal_branch_full_list = array();
				$branch = Application::getEntityInstance('user_group_branch');
				foreach ($branch->load_list() as $b) {
					self::$internal_branch_full_list[$b->id] = $b->title;
				}
			}
			return self::$internal_branch_full_list;
		}
		
		
		public function getBranchSelect($null_item=false, $for_user_id=null) {
			$out = get_empty_select($null_item);
			
			$full_list = $this->getBranchFullList();
			
			$for_user_id = (int)$for_user_id;
			if ($for_user_id) {
				$db = Application::getDb();
				$coupling_table = $this->getCouplingTableName();
				$table = $this->getTableName();
				
				$branch_ids = $db->executeSelectColumn("
					SELECT DISTINCT 
						$table.branch_id
					FROM 
						$coupling_table 
						LEFT JOIN $table ON $table.id = $coupling_table.group_id
					WHERE $coupling_table.user_id=$for_user_id
				");
			}
			
			foreach ($full_list as $id=>$name) {
				if ($for_user_id && !in_array($id, $branch_ids)) continue;
				$out[$id] = $name;
			}
			
			
			return $out;
			
		}
		
		public function getCouplingTableName() {
			return 'user_group_coupling';
		}		
		
		public function mandatory_fields() {
			return array(
				'title' => 'Название'/*,
				'capacity' => 'Число мест',
				'opened_before' => 'Набор до',
				'education_starts' => 'Начало обучения'*/
			);
		}
		
        public function make_form(&$form) {
        	$form = parent::make_form($form);
            $form->addField(new TEditField("title", "", 100, 255));
            $form->addField(new TEditField("capacity", "", 10, 20));
            $form->addField(new TEditorField("description"));
            $form->addField(new TJSCalendarField('opened_before', ''));
            $form->addField(new TJSCalendarField('education_starts', ''));
            $form->addField(new TSelectField('branch_id', '', $this->getBranchSelect('-- Выберите --')));
            $form->addField(new TEditField("month_price", "", 10, 20));
            $form->addField(new TTextField("month_price_comment", "", 80, 4));
            return $form;
        }
        
        
        public function validate() {
        	$errors = parent::validate();
        	
        	if (!$this->branch_id) {
        		$errors[] = "Нужно выбрать филиал";
        	}
        	
        	return $errors;
        }
        
        public function getSelect($add_null_item=false, $branch_id=null, $teacher_id=null) {
        	$out = get_empty_select($add_null_item);
        	
        	
        	$params = array();
        	$alias = $this->getTableAlias($this->getTableName());
        	$branch_id = (int)$branch_id;
        	$teacher_id = (int)$teacher_id;
        	
        	if ($branch_id) {        		
        		$params['where'][] = "$alias.branch_id=$branch_id";        		
        	} 
        	
        	if ($teacher_id) {
        		$coupling_table = $this->getCouplingTableName();
        		$coupling_table_alias = $this->getTableAlias($coupling_table);
        		$params['from'][] = "
        			INNER JOIN $coupling_table $coupling_table_alias ON
        				$coupling_table_alias.group_id = $alias.id AND
        				$coupling_table_alias.user_id = $teacher_id
        		";
        		//$params['where'][] = "$alias.branch_id=$branch_id";
        	}
        	
        	
        	$list = $this->load_list($params);
        	foreach ($list as $item) {
        		$out[$item->id] = $item->title;
        	}
        	
        	return $out;
        }        
        
        public function load_list($params=array()) {
        	$list = parent::load_list($params);
        	
        	$branches = $this->getBranchSelect();
        	foreach($list as $item) {
        		$item->branch_name = isset($branches[$item->branch_id]) ? $branches[$item->branch_id] : '';        		
        		$item->month_price_str = (int)$item->month_price ? number_format($item->month_price, 2, ',', ' ') . ' руб.' : ''; 
        	}
        	
        	return $list;
        }
        
        public function save() {
        	$this->capacity = (int)$this->capacity;
        	$this->month_price = (float)$this->month_price;
        	
        	
			$opened_before = $this->opened_before;
			$education_starts = $this->education_starts;
			
			if ($this->opened_before) {
				$this->opened_before = preg_replace('/(\d+)\.(\d+)\.(\d+)/', '$3-$2-$1', $this->opened_before); 	
			}
			if ($this->education_starts) {
				$this->education_starts = preg_replace('/(\d+)\.(\d+)\.(\d+)/', '$3-$2-$1', $this->education_starts); 	
			}
			
			$group_id = parent::save();
			
			$this->opened_before = $opened_before;
			$this->education_starts = $education_starts;
			
			return $group_id;
        	
        	
        	
        	return parent::save();
        }

        
		public function saveCoupling($user_id, $group_id) {
			$user_id = (int)$user_id;
			
			if (!$user_id) return;
			$db = Application::getDb();
			$table = $this->getCouplingTableName();
			
			$db->execute("
				DELETE FROM $table
				WHERE user_id=$user_id
			");
			
			
			$values = array();
			$group_id = array_unique($group_id);
			foreach($group_id as $g) {
				$g = (int)$g;
				if(!$g) continue;
				$values[] = "($user_id, $g)";
			}
			
			if (!$values) return;
			$values = implode(',', $values);
			
			$db->execute("
				INSERT INTO $table (user_id, group_id)
				VALUES $values
			");
		}
		
		
		public function loadCoupling(&$user_list) {
			if (!$user_list) return;
			
			$mapping = array();
			
			foreach($user_list as $item) {				
				$item->group_id = array();
				$item->group_title = array();
				$mapping[$item->id] = $item;
			}
			
			$user_ids = array_keys($mapping);
			$user_ids = implode(',', $user_ids);
			
			$db = Application::getDb();
			$coupling_table = $this->getCouplingTableName();
			$table = $this->getTableName();
			
			$coupling_data = $db->executeSelectAllObjects("
				SELECT 
					$coupling_table.group_id,
					$coupling_table.user_id,
					$table.title AS group_title
				FROM $coupling_table LEFT JOIN $table ON $table.id = $coupling_table.group_id
				WHERE $coupling_table.user_id IN($user_ids)
			");
					
			foreach($coupling_data as $item) {
				$mapping[$item->user_id]->group_id[] = $item->group_id;
				$mapping[$item->user_id]->group_title[] = $item->group_title;
			}
			
		}
		
        
				
	}
	
	
	
	
	
	
	