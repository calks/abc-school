<?php

	class user_group_schedule extends DataObject {
		
		public $id;
		public $user_group_id;
		public $weekday;
		public $starts_at;
		public $deleted;
		
		
		public function getTableName() {
			return 'user_group_schedule';
		}
		
		public function order_by() {
			return 'starts_at';
		}
		
		
		public function loadCollection($user_group_id) {
			$user_group_id = (int)$user_group_id;
			$out = $this->getEmptyCollection();
			
			if ($user_group_id) {
				$load_params = array();
				$table = $this->getTableName();
				$alias = $this->getTableAlias($table);
				
				$load_params['where'][] = "$alias.user_group_id=$user_group_id";
				$load_params['where'][] = "$alias.deleted=0";
				
				$items = $this->load_list($load_params);
				foreach ($items as $i) {
					$out[$i->weekday][$i->starts_at] = $i;
				}
			
			}
			
			return $out;			
		}
		
		
		public function getEmptyCollection() {
			$out = array();
			foreach ($this->getWeekdays() as $day_number=>$day_name) {
				$out[$day_number] = array();
			}
			return $out;
		}
		
		public function updateCollectionFromPost(&$collection) {
			
			$post = isset($_POST['schedule']) ? $_POST['schedule'] : array();
			
			$new_collection = $this->getEmptyCollection();
			
			if ($post) {
				foreach($post as $day_number=>$entries) {					
					foreach($entries as $time) {
						$time = str_pad($time, 5, '0', STR_PAD_LEFT) . ':00';
						$item = isset($collection[$day_number][$time]) ? $collection[$day_number][$time] : Application::getEntityInstance($this->getName());						
						$item->weekday = $day_number;
						$item->starts_at = addslashes($time);
						$item->deleted = 0;
						$new_collection[$day_number][] = $item;
					}
				}
			} 
			
			$collection = $new_collection;
		}
		
		
		public function saveCollection($collection, $user_group_id) {
			$user_group_id = (int)$user_group_id;
			if (!$user_group_id) return;
			
			$db = Application::getDb();
			$table = $this->getTableName();
			$db->execute("
				UPDATE $table 
				SET deleted=1
				WHERE user_group_id=$user_group_id  
			");
			
			foreach($collection as $day_number=>$entries) {
				foreach ($entries as $item) {
					$item->user_group_id = $user_group_id;
					$item->save();
				}
			}
			
		}
		
		
		public function getWeekdays($null_item = false) {
			$out = get_empty_select($null_item);
			
			$out[1] = 'Пн';
			$out[2] = 'Вт';
			$out[3] = 'Ср';
			$out[4] = 'Чт';
			$out[5] = 'Пт';
			$out[6] = 'Сб';
			$out[7] = 'Вс';
			
			return $out; 
		}
		


	}
