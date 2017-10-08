<?php

	class homework extends DataObject {
		
		public $schedule_entry_id;
		public $schedule_entry_date;
		public $description;
		
		public function getTableName() {
			return 'homework';
		}
		
		public function order_by() {
			return 'created DESC';
		}
		
		public function loadForGroup($group_id) {
			$group_id = (int)$group_id;
			
			if (!$group_id) return array();
			$params = array();
			
			$schedule = Application::getEntityInstance('user_group_schedule');
			$schedule_table = $schedule->getTableName();
			$schedule_alias = $schedule->getTableAlias($schedule_table);
			$alias = $this->getTableAlias($this->getTableName());
			
			$params['from'][] = "
				LEFT JOIN $schedule_table $schedule_alias 
				ON $schedule_alias.id=$alias.schedule_entry_id
			";
			$params['fields'][] = "$schedule_alias.starts_at";
			$params['order_by'][] = "$alias.schedule_entry_date DESC, $schedule_alias.starts_at DESC";
			$params['where'][] = "$schedule_alias.user_group_id=$group_id";
			
			$list = $this->load_list($params);

			foreach ($list as $item) {
				$item->description_html = nl2br($item->description);	
			}
			
			return $list;

		}
		
		public function findId($schedule_entry_id, $schedule_entry_date) {
			$schedule_entry_id = (int)$schedule_entry_id;
			$schedule_entry_date = addslashes($schedule_entry_date);
			$table = $this->getTableName();
			$db = Application::getDb();

			$sql = "
				SELECT id
				FROM $table
				WHERE
					schedule_entry_id=$schedule_entry_id AND
					schedule_entry_date='$schedule_entry_date'
			";
			
			
			$id = $db->executeScalar($sql);
			
			return $id ? $id : null;
		}
		
		

	}
