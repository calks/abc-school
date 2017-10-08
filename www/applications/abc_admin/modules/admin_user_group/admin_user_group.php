<?php

	Application::loadLibrary('core/admin_module');
	
	class adminUserGroupModule extends AdminModule {
		
		protected $schedule_collection;
		
		protected function getObjectName() {
			return 'user_group';
		}

		protected function beforeListLoad(&$load_params) {
			
			$group = Application::getObjectInstance('user_group');
			
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
			
		}
		
		protected function taskEdit() {
			
			$schedule = Application::getEntityInstance('user_group_schedule');
			$smarty = Application::getSmarty();
			$smarty->assign('weekdays', $schedule->getWeekdays());
			
			$page = Application::getPage();
			$page->addScript('/applications/abc_admin/modules/admin_user_group/static/schedule.js');
			
			$this->schedule_collection = array();
			$schedule = Application::getEntityInstance('user_group_schedule');
			$object = $this->objects[0];

			$this->schedule_collection = $schedule->getEmptyCollection();
			
			if ($object->id) {
				$this->schedule_collection = $schedule->loadCollection($object->id);	
			}
			
			if (Request::isPostMethod()) {
				$schedule->updateCollectionFromPost($this->schedule_collection);
			}
			
			$smarty->assign('schedule', $this->schedule_collection);
						
			return parent::taskEdit();
		}
		
		
		protected function afterObjectSave() {
			$object = $this->objects[0];
			$schedule = Application::getEntityInstance('user_group_schedule');
			$schedule->saveCollection($this->schedule_collection, $object->id);
		}
	}