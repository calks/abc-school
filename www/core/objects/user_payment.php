<?php

	class user_payment {
		
		
		public function getTableName() {
			return 'user_payment';
		}
		
		
		public function getMonthsToPay($start_year) {
			$month_list = array(
				9, 10, 11, 12, 1, 2, 3, 4, 5
			);
			$month_names = array(
				1 => 'январь',
				2 => 'февраль',
				3 => 'март',
				4 => 'апрель',
				5 => 'май',
				6 => 'июнь',
				7 => 'июль',
				8 => 'август',
				9 => 'сентябрь',
				10 => 'октябрь',
				11 => 'ноябрь',
				12 => 'декабрь'
			);
			
			$current_year = (int)date('Y');
			$current_month = (int)date('m');
			
			$out = array();
			
			foreach ($month_list as $m) {
				$y = $m>=9 ? $start_year : $start_year + 1;				
				if ($y > $current_year) continue;
				if ($y==$current_year && $m>$current_month) continue;
				$m2 = str_pad($m, 2, '0', STR_PAD_LEFT);
				$out["$y-$m2-01"] = $month_names[$m] . ' ' . $y;
			}

			return $out;
					
		}
		
		
		public function loadForGroup($group_id, $start_year=null, $debtors_only=false) {
			$group_id = (int)$group_id;
			
			$user = Application::getEntityInstance('user');
			$user_group = Application::getEntityInstance('user_group');
			
			$user_table = $user->getTableName();
			$user_coupling_table = $user_group->getCouplingTableName();

			$filter_params = array();
			if ($start_year && $debtors_only) {
				$filter_params['search_debtors'] = $start_year; 
			}

			$data = profileHelperLibrary::getGroupStudents($group_id, $filter_params);
			
			if (!$data) return array();
			
			
			$end_year = $start_year + 1;
			
			$month_list = array(
				9, 10, 11, 12, 1, 2, 3, 4, 5
			);
			$month_names = array(
				1 => 'Январь',
				2 => 'Февраль',
				3 => 'Март',
				4 => 'Апрель',
				5 => 'Май',
				6 => 'Июнь',
				7 => 'Июль',
				8 => 'Август',
				9 => 'Сентябрь',
				10 => 'Октябрь',
				11 => 'Ноябрь',
				12 => 'Декабрь'
			);
			
			$current_year = (int)date('Y');
			$current_month = (int)date('m');
			
			$out = array();
			foreach ($data as $d) {
				$item = array(
					'user_name' => $d->user_name,
					'payments' => array(),
					'is_debtor' => false
				);
				
				foreach ($month_list as $m) {
					$y = $m < 9 ? $end_year : $start_year; 
					$m2 = str_pad($m, 2, '0', STR_PAD_LEFT);
					//echo "$m <= $current_month || $y < $current_year<br>";
					$item['payments']["$y-$m2-01"] = array(
						'caption' => $month_names[$m] . '<br>' . $y,
						'payed' => false,
						'expired' => $y < $current_year || ($m <= $current_month && $y == $current_year),
						'comment' => '',
						'mark_unpayed' => false
					);
				}
				
				$out[$d->user_id] = $item; 
			}
			
			$db = Application::getDb();
			$user_ids = array_keys($out);			
			$user_ids = implode(',', $user_ids);
			$table = $this->getTableName();
						
			$sql = "
				SELECT 
					$table.user_id,
					$table.payment_period_begin_date,
					$table.comment,
					DATE_FORMAT($table.payment_period_begin_date, '%Y') AS year,					
					DATE_FORMAT($table.payment_period_begin_date, '%m') AS month
				FROM
					$table					
				WHERE
					$table.user_id IN($user_ids) AND
					$table.payment_period_begin_date >= '$start_year-09-01' AND
					$table.payment_period_begin_date <= '$end_year-05-01'
				ORDER BY $table.payment_period_begin_date
			";

					
			$data = $db->executeSelectAllObjects($sql);
			
			foreach ($data as $d) {
				$out[$d->user_id]['payments'][$d->payment_period_begin_date]['payed'] = true;
				$out[$d->user_id]['payments'][$d->payment_period_begin_date]['comment'] = $d->comment;
				
			}
			
			
			$current_period = date("Y-m-01");
			foreach($out as $user_id=>&$data) {				
				$prev_unpayed_period_start = null;
				foreach($data['payments'] as $period_start => &$entry) {
					//print_r($entry);
					if (!$entry['expired']) continue;					
					if (!$entry['payed']) {						
						$is_current_period = $period_start==$current_period;
						if ($prev_unpayed_period_start || $is_current_period) {
							$entry['mark_unpayed'] = true;
							$data['is_debtor'] = true;
							if ($prev_unpayed_period_start) $data['payments'][$prev_unpayed_period_start]['mark_unpayed'] = true;
						}
						$prev_unpayed_period_start = $period_start;
					}
				}	
			}
			
			return $out;

		}
		
		
		public function getPaymentYearOptions() {
			$current_year = date("Y");			
			$payment_table = $this->getTableName();
			$db = Application::getDb();
			$min_year = (int)$db->executeScalar("
				SELECT DATE_FORMAT(payment_period_begin_date, '%Y') 
				FROM $payment_table
				ORDER BY payment_period_begin_date ASC
				LIMIT 1
			");
			
			$min_year = $min_year ? $min_year-1 : $current_year-1;
			$out = array();
			
			for($y=$min_year; $y<=$current_year; $y++) {
				$out[$y] = $y . '-' . ($y+1);
			}
			
			return $out;
		}
		
		

	}
