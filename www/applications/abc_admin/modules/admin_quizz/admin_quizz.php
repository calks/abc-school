<?php

	Application::loadLibrary('core/admin_module');

	class AdminQuizzModule extends AdminModule {
		
		protected function getObjectName() {
			return 'quizz';
		}		
		
		protected function beforeListLoad(&$load_params) {			
			$object = Application::getObjectInstance($this->getObjectName());
			$table = $object->get_table_name();
			$alias = $object->get_table_abr();
			
			$question = Application::getObjectInstance('quizz/question');
			$q_table = $question->get_table_name();
			$q_alias = $question->get_table_abr();
			
			$load_params['fields'][] = "COUNT($q_alias.id) AS questions_count";
			$load_params['from'][] = "
				LEFT JOIN $q_table $q_alias
				ON $q_alias.quizz_id=$alias.id
			";
			$load_params['group_by'][] = "$alias.id";
			
		}
		
	}