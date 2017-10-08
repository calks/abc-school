<?php

	Application::loadLibrary('core/admin_module');

	class AdminQuizzQuestionAnswerModule extends AdminModule {
		
		protected $question_id;
		protected $question;
		
		public function run($params=array()) {
			$this->question_id = (int)Request::get('question');
			$question = Application::getObjectInstance('quizz/question');
			$this->question = $question->load($this->question_id);
			if(!$this->question) return $this->terminate();
			$this->setUrlAddition("question=$this->question_id");
			$smarty = Application::getSmarty();
			$smarty->assign('question', $this->question);
			return parent::run($params);
		}
		
		protected function getObjectName() {
			return 'quizz/question_answer';
		}		
		
		protected function beforeListLoad(&$load_params) {			
			$object = Application::getObjectInstance($this->getObjectName());			
			$alias = $object->get_table_abr();
			$load_params['where'][] = "$alias.question_id = $this->question_id";
		}
		
		protected function taskEdit() {
			if ($this->action=='add') {
				$this->objects[0]->question_id = $this->question_id;
			}
			elseif($this->objects[0]->question_id != $this->question_id) {
				return $this->terminate();
			}
			return parent::taskEdit();
		}
		
		protected function neighbourExtraCondition() {
			return "question_id = $this->question_id";
		}
		
		protected function afterObjectSave() {
			$this->setRightQuestion();
		}
		
		protected function afterObjectDelete() {
			$this->setRightQuestion();
		}
		
		protected function setRightQuestion() {			
			$db = Application::getDb();
			$question = Application::getObjectInstance('quizz/question_answer');
			$table = $question->get_table_name(); 
			$sql = "
				SELECT id FROM $table
				WHERE question_id=$this->question_id
				AND is_right=1
			";
						
			$ids = $db->executeSelectColumn($sql);
			if (count($ids) == 1) return;
			
			if ($this->action!='delete' && 
				isset($this->objects[0]->is_right) && 
				$this->objects[0]->is_right == 1) {
				$right_id = $this->objects[0]->id;
			}
			else {
				$sql = "
					SELECT id FROM $table
					WHERE question_id=$this->question_id
					ORDER BY is_right
					LIMIT 1
				";				
				$right_id = $db->executeScalar($sql);					
			}
			
			if (!$right_id) return;
			
			$sql = "
				UPDATE $table
				SET is_right = IF(id=$right_id, 1, 0)
				WHERE question_id=$this->question_id
			";
			
			$db->execute($sql);	

		}
		
		
	}
	
	