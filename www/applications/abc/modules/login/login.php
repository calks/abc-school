<?php

	class loginModule extends Module {
		
		protected $action;
		protected $user_session;
		protected $login_form;
		protected $errors;
		protected $redirect;
		
		public function run($params=array()) {		
			$this->redirect = Request::get('redirect', 'profile');
			$this->action = @array_shift($params);
			if (!$this->action) $this->action = 'login';
			$this->user_session = Application::getUserSession();
			$this->errors = array();
			
			$method_name = 'task' . ucfirst($this->action);
			if (!method_exists($this, $method_name)) return $this->terminate();

			$smarty = Application::getSmarty();
			
			$smarty->assign('redirect', $this->redirect);
							
			$document = Application::getEntityInstance('document');
			$document = $document->loadToUrl($this->getName());

			$static_dir = Application::getModuleUrl('education_form') . '/static';			
			$page = Application::getPage();
			$page->AddStylesheet("$static_dir/css/education_form.css");
			
			
			$smarty->assign('document', $document);
						
			call_user_func(array($this, $method_name), $params);
			
			$smarty->assign('errors', $this->errors);
			
			$smarty->assign('login_link', $this->getLink('login'));
			$smarty->assign('forgot_link', $this->getLink('forgot'));
			$smarty->assign('request_link', $this->getLink('request'));
			
			
			$template_path = $this->getTemplatePath($this->action);					
		
			return $smarty->fetch($template_path);			
		}
		
		protected function taskLogin() {
			if ($this->user_session->userLogged()) $this->onSuccessLogin();
			
			$this->login_form = array(
				'login' => isset($_POST['login_form']['login']) ? $_POST['login_form']['login'] : '',
				'pass' => isset($_POST['login_form']['pass']) ? $_POST['login_form']['pass'] : '' 
			);
			
						
			if (Request::isPostMethod()) {
				$login = $this->login_form['login']; 
				$pass = $this->login_form['pass'];
				if($this->user_session->auth($login, $pass)) {
					$this->onSuccessLogin();
				}
				else $this->errors['login'] = "Неправильная комбинация логина и пароля"; 
			}
			
			$smarty = Application::getSmarty();
			$smarty->assign('login_form', $this->login_form);
			$smarty->assign('form_action', Application::getSeoUrl("/{$this->getName()}/login"));
			
		}
		
		protected function taskLogout() {
			$this->user_session->logout();
			Redirector::redirect(Application::getSeoUrl("/"));
		}		
		
		protected function taskForgot() {
			
			if ($this->user_session->userLogged()) $this->onSuccessLogin();
			
			$email = trim(Request::get('email'));
			$password_sent = false;
			
			if (Request::isPostMethod()) {
				if (!$email) {
					$this->errors['email'] = "Вы не ввели Email";	
				}
				elseif (!email_valid($email)) {
					$this->errors['email'] = "Вы ввели неправильный Email";
				}
				else {					
					$user = Application::getEntityInstance('user');
					$user_id = $user->getIdByEmail($email);
					
					if (!$user_id) {
						$this->errors['email'] = "Пользователя с таким адресом нет";
					}
					else {
						
						Application::loadLibrary('olmi/MailSender');
						
						$user = Application::getEntityInstance('user');
						$user = $user->load($user_id);
			            $smarty = Application::getSmarty();
			            
			            $template_path = $this->getTemplatePath('forgot_email');
			            $smarty->assign('user', $user);			            
			            $message = $smarty->fetch($template_path);
			            
			            $msg = MailSender::createMessage();
			            
			            $msg->setSubject("abc-school.ru: напоминание пароля");
			            $msg->setFrom('no-reply@abc-school.ru', 'Лингвоцентр ABC');
			            $msg->setReplyTo('no-reply@abc-school.ru', 'Лингвоцентр ABC');
			            $msg->setBody($message, "text/html", "utf-8", "8bit");
			            $msg->addTo($email);
			            MailSender::send($msg);
						
						$password_sent = true;
						$email = '';
					}
				}
			}
			
			
			$document = Application::getEntityInstance('document');
			$document = $document->loadToUrl($this->action);
						
			$smarty = Application::getSmarty();
			$smarty->assign('email', $email);
			$smarty->assign('password_sent', $password_sent);						
			$smarty->assign('document', $document);
		}
		
		
		protected function getRequestForm() {
			Application::loadLibrary('olmi/form');
			$form = new BaseForm();

			$form->addField(new TEditField('firstname', '', 60, 255));
			$form->addField(new TEditField('lastname', '', 60, 255));
			$form->addField(new TEditField('email', '', 60, 255));
			$form->addField(new TEditField('cell_phone', '', 60, 255));
			
			$branch = Application::getEntityInstance('user_group_branch');
			$branch_table = $branch->getTableName();
			$branch_params['order_by'] = array('title');
			$branch_options = array(
				null => '-- Выберите --'
			);
			foreach ($branch->load_list($branch_params) as $b){
				$branch_options[$b->id] = $b->title;
			}

			$form->addField(new TSelectField('branch_id', '', $branch_options));
			
			return $form;
		}
		
		
		protected function findStudent($request_form) {
			$firstname = mb_strtolower(trim($request_form->getValue('firstname')), 'utf-8');
			$firstname_safe = addslashes($firstname);
			$lastname = mb_strtolower(trim($request_form->getValue('lastname')), 'utf-8');
			$lastname_safe = addslashes($lastname);
			$branch_id = (int)$request_form->getValue('branch_id');
			
			$user = Application::getEntityInstance('user');
			$user_table = $user->getTableName();
			$user_alias = $user->getTableAlias($user_table);
			
			$group = Application::getEntityInstance('user_group');
			$group_table = $group->getTableName();
			$group_alias = $group->getTableAlias($group_table);
			
			$load_params['where'][] = "$user_alias.role = 'student'";
			$load_params['where'][] = "$group_alias.branch_id = $branch_id";
			$load_params['where'][] = "TRIM(LOWER($user_alias.firstname)) = '$firstname_safe'";
			$load_params['where'][] = "TRIM(LOWER($user_alias.lastname)) = '$lastname_safe'";
			$load_params['limit'] = 1;
			
			$result = $user->load_list($load_params);
			
			return $result ? array_shift($result) : null;
			
		}
		
		
		protected function taskRequest() {
			
			if ($this->user_session->userLogged()) $this->onSuccessLogin();
			
			$form = $this->getRequestForm();
			
			$captions = array(
				'firstname' => 'Имя (ребенка)',
				'lastname' => 'Фамилия (ребенка)',
				'email' => 'Email',
				'cell_phone' => 'Сотовый телефон',
				'branch_id' => 'Филиал'
			);
			
			$password_sent = false;			
			if (Request::isPostMethod()) {
				
				$form->LoadFromRequest($_REQUEST);
				foreach ($captions as $fieldname=>$cap) {
					$value = trim($form->getValue($fieldname));
					if (!$value) {
						$this->errors[$fieldname] = "Вы не заполнили обязательное поле";
					}
					elseif ($fieldname=='email' && !email_valid($value)) {
						$this->errors[$fieldname] = "Вы ввели неправильный адрес";
					}
				}
				
				if (!$this->errors) {
					$student = $this->findStudent($form);
					$email = trim($form->getValue('email'));
					$cell_phone = trim($form->getValue('cell_phone'));
					
					if (!$student) {
						$this->errors['general'] = "Не удалось найти вас в журнале. 
						Проверьте, пожалуйста, правильно ли ошибок в имени и фамилии, и правильно ли указан филиал.";
					}
					elseif ($student->email && $student->email!=$email) {
						$contact_link = Application::getSeoUrl("/contact");
						$this->errors['general'] = "
							Аккаунт зарегистрирован на другой адрес Email. <br>
							Если вы считаете, что кто-то зарегистрировался на сайте под вашими именем и фамилией,
							обратитесь к <a href=\"$contact_link\">администрации</a>.
						";											}
					else {
						$student->email = $email;
						if (!$student->emailIsUnique()) {
							$forgot_link = Application::getSeoUrl("/{$this->getName()}/forgot");
							$this->errors['general'] = "
								Адрес $email уже используется другим пользователем. <br>
								Если вы забыли пароль, можно восстановить его 
								<a href=\"$forgot_link\">здесь</a>
							";
						}
					}					
					
					if (!$this->errors) {
						$student->cell_phone = $cell_phone;

						Application::loadLibrary('olmi/MailSender');
							
			            $smarty = Application::getSmarty();
			            
			            $template_path = $this->getTemplatePath('request_email');
			            $smarty->assign('student', $student);			            
			            $message = $smarty->fetch($template_path);
			            
			            $msg = MailSender::createMessage();
			            
			            $msg->setSubject("abc-school.ru: напоминание пароля");
			            $msg->setFrom('no-reply@abc-school.ru', 'Лингвоцентр ABC');
			            $msg->setReplyTo('no-reply@abc-school.ru', 'Лингвоцентр ABC');
			            $msg->setBody($message, "text/html", "utf-8", "8bit");
			            $msg->addTo($email);
			            $password_sent = MailSender::send($msg);
			            
			            if ($password_sent) {
			            	$student->save();
			            	foreach ($captions as $fieldname=>$cap) {
			            		$form->setValue($fieldname, null);
			            	}
			            }
						else {
							$this->errors['general'] = "
								Не удалось выслать логин и пароль.
							";											
						}
					}
				}
			}
			
			
			$document = Application::getEntityInstance('document');
			$document = $document->loadToUrl($this->action);
						
			$smarty = Application::getSmarty();
			$smarty->assign('request_form', $form);
			$smarty->assign('captions', $captions);
			$smarty->assign('password_sent', $password_sent);						
			$smarty->assign('document', $document);
		}		
		
		
		protected function onSuccessLogin() {
			$redirect_url = trim($this->redirect, ' /');
			Redirector::redirect(Application::getSeoUrl("/$redirect_url"));
		}
		
		
		protected function getLink($task) {
			$url = "/{$this->getName()}";
			if ($task != 'login') $url .= "/$task";
			if ($this->redirect != 'profile') {
				$url .= "?redirect=" . rawurlencode($this->redirect);
			}
			return Application::getSeoUrl($url);
		}
		
		
	}