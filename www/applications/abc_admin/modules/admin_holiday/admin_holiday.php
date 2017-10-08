<?php

	Application::loadLibrary('core/admin_module');
	
	class adminHolidayModule extends AdminModule {
		
				
		protected function getObjectName() {
			return 'holiday';
		}
		
		protected function taskEdit($params=array()) {
			if ($this->action == 'add') {
				$this->objects[0]->date = date('d.m.Y');
			}
			else {
				$this->objects[0]->date = preg_replace('/(\d+)\-(\d+)\-(\d+)/', '$3.$2.$1', $this->objects[0]->date);
			}
			return parent::taskEdit();
		}


	}