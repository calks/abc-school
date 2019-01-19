<?php

	Application::loadLibrary('core/admin_module');

	class AdminQuestionsModule extends AdminModule {
		
		protected function getObjectName() {
			return 'question';
		}
		
		protected function beforeListLoad(&$load_params) {
			$load_params['order_by'] = 'author_notified';			
		}
		
		protected function validateObject($object) {
			$errors = array();
			if (!$object->author_name) $errors[] = "Нужно ввести имя автора";
			
			if (!$object->author_email) $errors[] = "Нужно ввести email автора";
			elseif (!email_valid($object->author_email)) $errors[] = "Неправильный формат email'а";
			
			if (!$object->question) $errors[] = "Нужно ввести текст вопроса";
			if (!$object->answer) $errors[] = "Нужно ввести ответ на вопрос";
			
			return $errors;
			
		}
		
		protected function getPreservedFields() {
			if ($this->original_objects[0]->author_notified) return array('author_notified');
			return array();			
		}
		
		protected function beforeObjectSave() {
			if ($this->objects[0]->author_notified) return;
						
			Application::loadLibrary('olmi/MailSender');
                        
            $smarty = Application::getSmarty();            
			$smarty->assign('question', $this->objects[0]);            			
            $template_path = $this->getTemplatePath('answer_notification');                        
            $message = $smarty->fetch($template_path);
            
            $msg = MailSender::createMessage();
            
            $msg->setSubject("Ответ на ваш вопрос от Лингвоцентра ABC");
            $msg->setFrom('no-reply@abc-school.ru', 'Лингвоцентр ABC');
            $msg->setReplyTo('no-reply@abc-school.ru', 'Лингвоцентр ABC');
            $msg->setBody($message, "text/html", "utf-8", "8bit");
            $msg->addTo($this->objects[0]->author_email);
            MailSender::send($msg);			
			
			$this->objects[0]->author_notified = 1;
		}
		
		
		
		
	}