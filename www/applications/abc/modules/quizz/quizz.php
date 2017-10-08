<?php

	define ('GALLERY_MODULE_ITEMS_PER_PAGE', 12);

    class QuizzModule extends Module {
    	
    	protected $action;
    	protected $quizz_id;
    	protected $quizzes;
    	protected $quizz;
    	protected $questions;
    	protected $current_question;
    	

        public function run($params=array()) {

			$static_dir = Application::getModuleUrl($this->getName()) . '/static';
			
			$page = Application::getPage();
			$page->AddStylesheet("$static_dir/css/gallery.css");
        	        	
        	$this->action = $params ? array_shift($params) : 'list';
        	
			$method_name = 'task' . ucfirst($this->action);
			if (!method_exists($this, $method_name)) return $this->terminate();
			
			$page = pagePropertiesHelper::getDocument();
			$smarty = Application::getSmarty();
			$smarty->assign('page_heading', isset($page->meta_title) ? $page->meta_title : '');
			$smarty->assign('page_content', isset($page->content) ? $page->content : '');
			
			
			return call_user_func(array($this, $method_name), $params);	

        }
        
        
        protected function taskList($params=array()) {        	
        	$obj = Application::getObjectInstance('quizz');
        	
        	$load_params = array();
        	$load_params['mode'] = 'front';
        	$load_params['where'][] = 'active=1';
        	
        	$this->quizzes = $obj->load_list($load_params);
        	foreach($this->quizzes as $item) {
        		$item->link = Application::getSeoUrl("/{$this->getName()}/detail/$item->id");	
        	} 
        	
			$smarty = Application::getSmarty();
			$smarty->assign('quizzes', $this->quizzes);
						
			$template_path = $this->getTemplatePath($this->action);						
			return $smarty->fetch($template_path);
        }
        
        
        protected function taskDetail($params=array()) {
        	
        	/*$this->restoreQuestions();
			$smarty = Application::getSmarty();			
			$smarty->assign('page_content', $this->generateResultPage());
			$template_path = $this->getTemplatePath($this->action);	

			$page = Application::getPage();
			$static_dir = Application::getModuleUrl($this->getName()) . '/static';
			$page->AddStylesheet("$static_dir/css/quizz.css");
			$page->AddScript("$static_dir/js/quizz.js");
						
			return $smarty->fetch($template_path);*/
        	
        	
        	$this->quizz_id = @(int)array_shift($params);        	
        	if (!$this->quizz_id) return $this->terminate();
        	        	
        	$quizz = Application::getObjectInstance('quizz');
        	$quizz_params = array('mode' => 'front');
        	$quizz_params['where'][] = 'active=1';
        	
        	$this->quizz = $quizz->load($this->quizz_id, $quizz_params);
        	if (!$this->quizz) return $this->terminate();
        	
			$page = Application::getPage();
			$static_dir = Application::getModuleUrl($this->getName()) . '/static';
			$page->AddStylesheet("$static_dir/css/quizz.css");
			$page->AddScript("$static_dir/js/quizz.js");
        	
        	$this->resetQuestions();
        	$this->saveQuestions();
        	
			$smarty = Application::getSmarty();
			$smarty->assign('quizz', $this->quizz);
			$smarty->assign('page_content', $this->generateQuestionPage());
			
			$template_path = $this->getTemplatePath($this->action);						
			return $smarty->fetch($template_path);        	
        }
        
        
        protected function taskAjax($params=array()) {
        	$this->restoreQuestions();
        	
        	if (!isset($this->questions[$this->current_question])) die($this->generateErrorPage());        	
        	        	
        	$answer_id = (int)Request::get('answer_id');
        	$this->questions[$this->current_question]->answer_id = $answer_id;
        	$this->current_question++;
        	
        	$this->saveQuestions();

        	if ($this->current_question >= count($this->questions)) die($this->generateResultPage());
        	
        	die($this->generateQuestionPage());
        	
        }
        
        
        protected function resetQuestions() {
        	$this->current_question = 0;
        	
        	$db = Application::getDb();
        	$question = Application::getObjectInstance('quizz/question');
        	$answer = Application::getObjectInstance('quizz/question_answer');
        	
        	$q_table = $question->get_table_name();
        	$q_alias = $question->get_table_abr();
        	$a_table = $answer->get_table_name();
        	$a_alias = $answer->get_table_abr();
        	
        	$q_params = array('mode' => 'front');
        	$q_params['fields'][] = "COUNT($a_alias.id) AS answers_count";
        	$q_params['fields'][] = "SUM($a_alias.is_right) AS right_answers_count";
        	$q_params['from'][] = "
        		LEFT JOIN $a_table $a_alias
        		ON $a_alias.question_id = $q_alias.id
        	";
        	$q_params['having'][] = "answers_count > 1";
        	$q_params['having'][] = "right_answers_count = 1";
        	$q_params['group_by'][] = "$q_alias.id";
        	        	
        	$q_params['where'][] = "$q_alias.quizz_id=$this->quizz_id";
        	        	
        	$this->questions = $question->load_list($q_params);
        	if ($this->quizz->shuffle_questions) shuffle($this->questions);
        	
        	if(!$this->questions) return;
        	
        	$question_ids = array();
        	$key_mapping = array();
        	foreach($this->questions as $key=>$q) {
        		$key_mapping[$q->id] = $key;
        		$question_ids[] = $q->id;        		
        	}
        	$question_ids = implode(',', $question_ids);
        	
        	$a_params = array('mode' => 'front');
        	$a_params['where'][] = "question_id IN($question_ids)";
        	$answers = $answer->load_list($a_params);
        	
        	foreach($answers as $a) {
        		$this->questions[$key_mapping[$a->question_id]]->answers[] = $a;
        	}

        }
        
        
        protected function saveQuestions() {
        	$session_name = $this->getName() . '_question_storage';
        	$_SESSION[$session_name] = array(
        		'current_question' => $this->current_question,
        		'questions' => serialize($this->questions)
        	);
        }
        
        
        protected function restoreQuestions() {
        	Application::loadObjectClass('quizz/question');
        	Application::loadObjectClass('quizz/question_answer');
        	$session_name = $this->getName() . '_question_storage';
        	if (!isset($_SESSION[$session_name])) {
        		$this->questions = array();
        		$this->current_question = 0;
        	}
        	else {
        		$this->questions = $_SESSION[$session_name]['questions'];
        		$this->current_question = $_SESSION[$session_name]['current_question'];
        		
        		if ($this->questions) $this->questions = unserialize($this->questions);
        	}
        }
        
        
        protected function generateQuestionPage() {
        	$smarty = Application::getSmarty();        	
        	$question = isset($this->questions[$this->current_question]) ? $this->questions[$this->current_question] : null;

        	if(!$question) return $this->generateErrorPage();
        	
        	$smarty->assign('question', $question);
        	$smarty->assign('back_link', Application::getSeoUrl("/{$this->getName()}"));
        	
        	$smarty->assign('question_number', $this->current_question+1);
        	$smarty->assign('questions_count', count($this->questions));
        	
        	$template_path = $this->getTemplatePath('question');
        	return $smarty->fetch($template_path);
        }
        
        
        protected function generateErrorPage() {
        	$smarty = Application::getSmarty();
        	$smarty->assign('back_link', Application::getSeoUrl("/{$this->getName()}"));
        	$template_path = $this->getTemplatePath('error');
        	return $smarty->fetch($template_path);        	
        }
        
        
        protected function generateResultPage() {
        	$right_answers = 0;
        	foreach($this->questions as $question) {
        		foreach($question->answers as $answer) {
        			if($answer->is_right && $answer->id == $question->answer_id) $right_answers++;
        		}
        	}
        	
        	$smarty = Application::getSmarty();
        	$smarty->assign('right_answers', $right_answers);
        	$smarty->assign('questions_total', count($this->questions));
        	$smarty->assign('back_link', Application::getSeoUrl("/{$this->getName()}"));
        	$smarty->assign('questions', $this->questions);
        	
        	$template_path = $this->getTemplatePath('result');
        	return $smarty->fetch($template_path);
        	        	
        }
        
        
        

    }
