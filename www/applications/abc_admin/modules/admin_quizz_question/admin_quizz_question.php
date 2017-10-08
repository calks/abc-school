<?php

	Application::loadLibrary('core/admin_module');

	class AdminQuizzQuestionModule extends AdminModule {
		
		protected $quizz_id;
		protected $quizz;
		
		public function run($params=array()) {
			$this->quizz_id = (int)Request::get('quizz');
			$quizz = Application::getObjectInstance('quizz');
			$this->quizz = $quizz->load($this->quizz_id);
			if(!$this->quizz) return $this->terminate();
			$this->setUrlAddition("quizz=$this->quizz_id");
			$smarty = Application::getSmarty();
			$smarty->assign('quizz', $this->quizz);
			return parent::run($params);
		}
		
		protected function getObjectName() {
			return 'quizz/question';
		}		
		
		protected function beforeListLoad(&$load_params) {			
			$object = Application::getObjectInstance($this->getObjectName());
			$table = $object->get_table_name();
			$alias = $object->get_table_abr();
			
			$answer = Application::getObjectInstance('quizz/question_answer');
			$a_table = $answer->get_table_name();
			$a_alias = $answer->get_table_abr();
			
			$load_params['where'][] = "$alias.quizz_id = $this->quizz_id";
			$load_params['fields'][] = "COUNT($a_alias.id) AS answers_count";
			$load_params['from'][] = "
				LEFT JOIN $a_table $a_alias
				ON $a_alias.question_id=$alias.id
			";
			$load_params['group_by'][] = "$alias.id";			
		}
		
		protected function taskEdit() {
			if ($this->action=='add') {
				$this->objects[0]->quizz_id = $this->quizz_id;
			}
			elseif($this->objects[0]->quizz_id != $this->quizz_id) {
				return $this->terminate();
			}
			return parent::taskEdit();
		}
		
		protected function neighbourExtraCondition() {
			return "quizz_id = $this->quizz_id";
		}
		
		
	}