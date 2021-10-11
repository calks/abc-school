<?php

	class user_group_branch extends DataObject {
		
		public $title;
		public $period_id;
		
		public function getTableName() {
			return 'user_group_branch';
		}
		
		public function order_by() {
			return 'id';
		}
		
		public function mandatory_fields() {
			return array(
				'title' => 'Название'
			);
		}
		
		public function load_list($params = array()) {
			
			$period_id = CURRENT_PERIOD_ID;
			$table = $this->getTableName();
			$table_alias = $this->getTableAlias($table);
			$params['where'][] = "`$table_alias`.`period_id` = $period_id";
			
			return parent::load_list($params);
		}
		
		public function save() {
			if (!$this->period_id) {
				$this->period_id = CURRENT_PERIOD_ID;				
			}			
			
			return parent::save();
		}
		
        public function make_form(&$form) {
        	$form = parent::make_form($form);
            $form->addField(new TEditField("title", "", 100, 255));
            return $form;
        }
		
		
	}