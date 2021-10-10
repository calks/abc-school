<?php

	class CareerFormModule extends Module {
		
		protected $form;				
		protected $errors;
		protected $captcha;
		
		public function run($params=array()) {
			$this->form = $this->getForm();
			$this->errors = array();
			
			Application::loadLibrary('captcha');
			
			$this->captcha = new captcha($this->getName());
			
			$static_dir = Application::getModuleUrl($this->getName()) . '/static';			
			$page = Application::getPage();
			$page->AddStylesheet("$static_dir/css/career_form.css");
			
			
			if (Request::isPostMethod()) {
				$this->form->LoadFromRequest($_REQUEST);
				$this->validateForm();
				if (!$this->errors) {
					$sent = $this->sendForm();
					if (!$sent) {
						$this->errors['send'] = "Не удалось отправить заявку";
					}
					else {
						Redirector::redirect("/career_form-thanks");
					}	
				}
			}
						
			
			$smarty = Application::getSmarty();
			$smarty->assign('form', $this->form);
			$smarty->assign('captcha', $this->captcha);
			$smarty->assign('form_action', Application::getSeoUrl("/{$this->getName()}"));			
			$smarty->assign('errors', $this->errors);
			
			$template_path = $this->getTemplatePath();
			return $smarty->fetch($template_path);			
		}
		
		
		protected function validateForm() {
			$phone = trim($this->form->getValue('phone'));
			if (!$phone) {
				$this->errors['phone'] = 'Введите, пожулуйста, контактный телефон';
			}

        	$email = $this->form->getValue('email');
			if (!trim($email)) {
				$this->errors['email'] = 'Введите, пожалуйста, Email';
			}
        	elseif(!email_valid($email)) {
        		$this->errors['email'] = 'Email имеет неверный формат';        		
        	}
        	
			$name = trim($this->form->getValue('name'));
			if (!$name) {
				$this->errors['name'] = 'Представьтесь, пожалуйста';
			}
			
			$mandatory = array(
				'birth_date_n_place',
				'family_type',
				'degree',
				'experience',
				'foreign_languages',
				'address'			
			);
			
			foreach ($mandatory as $mf) {
				$val = trim($this->form->getValue($mf));
				if (!$val) $this->errors[$mf] = "Вы пропустили обязательное поле";
			}

			
        	$captcha_code = $this->form->getValue('captcha');
			if (!$this->captcha->code_valid($captcha_code)) {
        		$this->errors['captcha'] = 'Вы ввели неверный код';
        		$this->captcha->regenerate();
        	}
			
		}
		
		protected function getForm() {
			Application::loadLibrary('olmi/form');
			
			$form = new BaseForm();
			
			$form->addField(new TEditField('name', '', 100, 255));			
			$form->addField(new TTextField('birth_date_n_place', '', 100, 2));
			$form->addField(new TEditField('family_type', '', 100, 255));			
			$form->addField(new TTextField('degree', '', 100, 4));
			$form->addField(new TTextField('experience', '', 100, 4));
			$form->addField(new TEditField('foreign_languages', '', 100, 255));
			$form->addField(new TTextField('skills', '', 100, 4));
			$form->addField(new TTextField('personality', '', 100, 4));
			$form->addField(new TEditField('address', '', 100, 255));
			$form->addField(new TEditField('phone', '', 100, 255));
			$form->addField(new TEditField('email', '', 100, 255));			
			$form->addField(new TEditField('captcha', '', 10, 20));
			
			return $form;
		}
		
		
		protected function sendForm() {
			Application::loadLibrary('olmi/MailSender');
                        
            $smarty = Application::getSmarty();
            $template_path = $this->getTemplatePath('email');
            $smarty->assign('form', $this->form);
            
            $message = $smarty->fetch($template_path);
            
            $msg = MailSender::createMessage();
            
            $msg->setSubject("abc-school.ru: заявка на вакансию");
            $msg->setFrom('no-reply@abc-school.ru', 'Лингвоцентр ABC');
            $msg->setReplyTo('no-reply@abc-school.ru', 'Лингвоцентр ABC');
            $msg->setBody($message, "text/html", "utf-8", "8bit");
            $msg->addTo(EMAIL_DESTINATION);
            return MailSender::send($msg);
            
		
		}
		
	}