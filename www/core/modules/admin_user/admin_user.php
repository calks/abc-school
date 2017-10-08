<?php

	Application::loadLibrary('core/admin_module');

	class AdminUserModule extends AdminModule {
		
		protected function getObjectName() {
			return 'user';
		}
		
		protected function taskEdit() {
			if ($this->action == 'add') {
				if (!isset($this->objects[0])) return $this->terminate();
				$object = $this->objects[0];
				$object->role = 'student';				
			}
			
			return parent::taskEdit();
		}
		
	}