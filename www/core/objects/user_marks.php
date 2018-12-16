<?php

	class user_marks {
		
		
		public function getTableName() {
			return 'user_marks';
		}
		
		
		
		public function loadForGroup($group_id, $from=null, $to=null) {
			$group_id = (int)$group_id;
			
			$user = Application::getEntityInstance('user');
			$user_group = Application::getEntityInstance('user_group');
			
			$user_table = $user->getTableName();
			$user_coupling_table = $user_group->getCouplingTableName();
			
						
			$db = Application::getDb();
			$sql = "
				SELECT 
					$user_table.id AS user_id,
					CONCAT($user_table.lastname, ' ', $user_table.firstname) AS user_name,
					notes AS user_notes
				FROM
					$user_coupling_table 
					LEFT JOIN $user_table ON $user_table.id = $user_coupling_table.user_id
				WHERE 
					$user_coupling_table.group_id=$group_id AND
					$user_table.role='student' AND
					$user_table.active=1 
				ORDER BY user_name	
			";
					
			$data = $db->executeSelectAllObjects($sql);
			
			$out = array();
			foreach ($data as $d) {
				$out[$d->user_id] = array(
					'user_name' => $d->user_name,
					'user_notes' => $d->user_notes,
					'marks' => array(),
					'missed_two' => false
				);
			}
			
			if (!$out) return $out;
			
			$user_ids = array_keys($out);			
			$user_ids = implode(',', $user_ids);
			$table = $this->getTableName();
			$schedule = Application::getEntityInstance('user_group_schedule');
			$schedule_table = $schedule->getTableName();  
			
			$from_where = $from ? "AND $table.schedule_entry_date >= '$from'" : "";
			$to_where = $from ? "AND $table.schedule_entry_date <= '$to'" : ""; 
			
			$sql = "
				SELECT 
					$table.user_id,
					$table.schedule_entry_id,
					$table.mark,
					$table.comment,
					CONCAT($table.schedule_entry_date, ' ', $schedule_table.starts_at) AS time					
				FROM
					$table
					LEFT JOIN $schedule_table ON $schedule_table.id = $table.schedule_entry_id
				WHERE
					$table.user_id IN($user_ids) AND
					$schedule_table.user_group_id=$group_id 
					$from_where $to_where
				ORDER BY $table.schedule_entry_date, $schedule_table.starts_at
			";

					
			$data = $db->executeSelectAllObjects($sql);
			
			
			foreach ($data as $d) {
				foreach ($out as $user_id=>&$marks_info) {
					if (!isset($marks_info['marks'][$d->time])) {
						$marks_info['marks'][$d->time] = array(
							'entry_id' => $d->schedule_entry_id,
							'marks' => false,
							'missed_two' => false,
							'comment' => ''
						);
					}
					if ($user_id == $d->user_id) {
						$marks_info['marks'][$d->time]['marks'] = $d->mark;
						$marks_info['marks'][$d->time]['comment'] = $d->comment;
					}
				}
			}
			
			
			foreach($out as $user_id=>&$data) {
				$prev_datetime = null;
				$prev_marks = true;
				foreach($data['marks'] as $datetime => &$entry) {
					if (!$entry['marks'] && !$prev_marks) {
						$entry['missed_two'] = true;
						$data['missed_two'] = true;
						if ($prev_datetime) {
							$data['marks'][$prev_datetime]['missed_two'] = true;
						}
					} 
					$prev_datetime = $datetime;
					$prev_marks = $entry['marks'];
				}	
			}
			
			
			return $out;

		}
		
		

	}
