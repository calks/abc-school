<?php

	class EducationFormModule extends Module {
		
		protected $form;
		protected $form_type;		
		protected $errors;
		protected $captcha;
		
		public function run($params=array()) {
			$this->form = $this->getForm();
			$this->form_type = Request::get('form_type');
			if (!in_array($this->form_type, array('kids','adults','preschool'))) $this->form_type = 'kids';
			$this->errors = array();
			
			Application::loadLibrary('captcha');
			
			$this->captcha = new captcha($this->getName());
			
			$static_dir = Application::getModuleUrl($this->getName()) . '/static';			
			$page = Application::getPage();
			$page->AddStylesheet("$static_dir/css/education_form.css");
			$page->AddScript("$static_dir/js/form_switch.js");
			
			if (Request::isPostMethod()) {
				$this->form->LoadFromRequest($_REQUEST);
				if ($this->form_type == 'preschool') {
					$this->form->setValue('learned_earlier', $this->form->getValue('learned_earlier_yn'));
				}
				
				$this->validateForm();
				if (!$this->errors) {
					$this->sendForm();
					Redirector::redirect("/education_form-thanks");	
				}
			}
						
			
			$smarty = Application::getSmarty();
			$smarty->assign('form', $this->form);
			$smarty->assign('captcha', $this->captcha);
			$smarty->assign('form_action', Application::getSeoUrl("/{$this->getName()}"));
			$smarty->assign('form_type', $this->form_type);
			$smarty->assign('errors', $this->errors);
			$smarty->assign('warning_box_template', $this->getTemplatePath('warning_box'));
			
			$template_path = $this->getTemplatePath();
			return $smarty->fetch($template_path);			
		}
		
		
		protected function validateForm() {
			$phone = trim($this->form->getValue('phone'));
			if (!$phone) {
				$this->errors['phone'] = 'Введите, пожулуйста, контактный телефон';
			}

        	$email = $this->form->getValue('email');
			if ($email && !email_valid($email)) {
        		$this->errors['email'] = 'Email имеет неверный формат';        		
        	}
        	
        	$mandatory = array(
        		'address',        		
        		'age',
        	);
        	
        	if ($this->form_type != 'preschool') $mandatory[] = 'learned_earlier';

        	if (in_array($this->form_type, array('kids', 'preschool'))) {
				$kids_name = trim($this->form->getValue('kids_name'));
				if (!$kids_name) {
					$this->errors['kids_name'] = 'Нам нужно знать, как зовут нашего будущего ученика';
				}
				
				$parents_name = trim($this->form->getValue('parents_name'));
				if (!$parents_name) {
					$this->errors['parents_name'] = 'Пожалуйста, представтесь';
				}
				
				$mandatory[] = 'school';
				$mandatory[] = 'grade';
				
        	}
        	else {
				$name = trim($this->form->getValue('name'));
				if (!$name) {
					$this->errors['name'] = 'Нам нужно знать, как зовут нашего будущего ученика';
				}
        	}
			
        	$captcha_code = $this->form->getValue('captcha');
			if (!$this->captcha->code_valid($captcha_code)) {
        		$this->errors['captcha'] = 'Вы ввели неверный код';
        		$this->captcha->regenerate();
        	}
        	
        	foreach($mandatory as $mf) {
        		if (!trim($this->form->getValue($mf))) {
        			$this->errors[$mf] = 'Вы пропустили обязательное поле';
        		}        		
        	}
			
		}
		
		protected function getForm() {
			Application::loadLibrary('olmi/form');
			
			$form = new BaseForm();
			
			$form->addField(new TEditField('kids_name', '', 100, 255));
			$form->addField(new TEditField('name', '', 100, 255));
			$form->addField(new TEditField('age', '', 10, 20));
			$form->addField(new TEditField('birth_date', '', 30, 30));
			$form->addField(new TEditField('school', '', 100, 255));
			$form->addField(new TEditField('grade', '', 10, 20));
			$form->addField(new TSelectField('shift', '', $this->getShiftSelect()));
			$form->addField(new TCheckboxField('prolonged', '', false, 'class="checkbox"'));
			$form->addField(new TEditField('parents_name', '', 100, 255));			
			$form->addField(new TTextField('parents_job', '', 100, 2));
			$form->addField(new TEditField('phone', '', 100, 255));
			$form->addField(new TEditField('phone2', '', 100, 255));
			$form->addField(new TEditField('address', '', 100, 255));
			$form->addField(new TEditField('email', '', 100, 255));			
			$form->addField(new TEditField('learned_earlier', '', 100, 255));
			$form->addField(new TRadioField('learned_earlier_yn', '', array(
				'Да' => 'Да',
				'Нет' => 'Нет'
			)));
			$form->addField(new TEditField('kidergarden_end_time', '', 100, 255));
			$form->addField(new TTextField('learned_earlier_detail', '', 100, 2));
			$form->addField(new TTextField('comments', '', 100, 4));
			$form->addField(new TEditField('captcha', '', 10, 20));
			
			
			return $form;
		}
		
		protected function getShiftSelect() {
			return array(
				'первая (утро)' => 'первая (утро)',
				'вторая (день)' => 'вторая (день)'
			);
		}
		
		protected function sendForm() {
			Application::loadLibrary('olmi/MailSender');
            
            
            $smarty = Application::getSmarty();
            $template_path = $this->getTemplatePath('email');
            $smarty->assign('form', $this->form);
            $smarty->assign('form_type', $this->form_type);
            $smarty->assign('shift_select', $this->getShiftSelect());
            
            $message = $smarty->fetch($template_path);
            
            $msg = MailSender::createMessage();
            $types_str = array(
            	'kids' => 'дети',
            	'adults' => 'взрослые',
            	'preschool' => 'дошкольники'
            );
            $form_type = $types_str[$this->form_type];
            
            $msg->setSubject(encode_header_utf_8("abc-school.ru: заявка на обучение ($form_type)"));
            $msg->setFrom('no-reply@abc-school.ru', encode_header_utf_8('Лингвоцентр ABC'));
            $msg->setReplyTo('no-reply@abc-school.ru', encode_header_utf_8('Лингвоцентр ABC'));
            $msg->setBody($message, "text/html", "utf-8", "8bit");
            $msg->addTo(EMAIL_DESTINATION);
            MailSender::send($msg);
            
		
		}
		
	}