<?php

	

	class nalogModule extends Module {
		
		public function run($params=array()) {
			
			$document = Application::getEntityInstance('document');
			$document = $document->loadToUrl($this->getName());
			pagePropertiesHelper::setTitleDescKeysFromObject($document);
			
			$form = $this->getForm();
			$this->errors = array();
			
			Application::loadLibrary('captcha');
			
			$this->captcha = new captcha($this->getName());
			
			$static_dir = Application::getModuleUrl($this->getName()) . '/static';			
			$page = Application::getPage();			
			$page->AddScript("$static_dir/js/nalog.js");
			$page->AddStylesheet("$static_dir/css/nalog.css");
			
			
			$education_periods_options =  $this->getEducatiomPeriods();
			
			$education_periods = array(
				array(
					'start_year' => date('Y'),
					'comment' => ''
				)
			);
			
			
			$attachments = array(
				null				
			);
			
			
			if (Request::isPostMethod()) {
				$form->LoadFromRequest($_REQUEST);
				
				$child_name = trim($form->getValue('child_name'));
				if (!$child_name) {
					$this->errors['child_name'] = "Вы не ввели ФИО ребенка";
				}
				
				
				$parent_name = trim($form->getValue('parent_name'));
				if (!$parent_name) {
					$this->errors['parent_name'] = "Вы не ввели ФИО родителя";
				}
				
				$education_periods = array();
				$years_posted = isset($_POST['education_periods']['start_year']) ? $_POST['education_periods']['start_year'] : array(); 
				$comments_posted = isset($_POST['education_periods']['comment']) ? $_POST['education_periods']['comment'] : array();
				
				
				foreach ($years_posted as $idx => $se) {
					$comment = isset($comments_posted[$idx]) ? $comments_posted[$idx] : '';
					$period_valid = array_key_exists($se, $education_periods_options);
					if (!$period_valid) continue;
					
					$education_periods[] = array(
						'start_year' => $se,
						'comment' => $comment,
						'period_name' => $education_periods_options[$se]
					);					
				}
				
				if (!$education_periods) {
					$this->errors['education_period'] = 'Добавьте хотя бы один период обучения';
				}
				
				$attachments = $this->loadAttachments();
				
				
				if (!$this->errors) {
					
					Application::loadLibrary('olmi/MailSender');
		            
		            
		            $smarty = Application::getSmarty();
		            $template_path = $this->getTemplatePath('email');
		            $smarty->assign('form', $form);
		            $smarty->assign('education_periods', $education_periods);
		            
		            $smarty->assign('form_type', $form_type);
		            		            
		            $message = $smarty->fetch($template_path);
		            
		            
		            $msg = MailSender::createMessage();
		            
		            $msg->setSubject("abc-school.ru: заявка на оформление налогового вычета");
		            $msg->setFrom('no-reply@abc-school.ru', 'Лингвоцентр ABC');
		            $msg->setReplyTo('no-reply@abc-school.ru', 'Лингвоцентр ABC');
		            $msg->setBody($message, "text/html", "utf-8", "8bit");
		            
		            
		            foreach ($attachments as $a) {
		            	$msg->addAttachment($a);
		            }
		            
		            $msg->addTo(EMAIL_DESTINATION);
		            $msg->addTo('alexey@cyberly.ru');
		            
		            $sent = MailSender::send($msg);
		            
		            if ($sent) {
		            	Redirector::redirect("/{$this->getName()}?sent=1");
		            }
		            else {
		            	$this->errors['mail'] = "Произошла ошибка при отправке сообщения";
		            }
				}
				
				
				foreach ($attachments as $a) {
					unlink($a);
				}
			}
					
						
			$smarty = Application::getSmarty();
			$smarty->assign('form', $form);
			$smarty->assign('captcha', $this->captcha);
			$smarty->assign('form_action', Application::getSeoUrl("/{$this->getName()}"));
			$smarty->assign('errors', $this->errors);
			$smarty->assign('education_periods', $education_periods);
			$smarty->assign('education_periods_options', $education_periods_options);
			$smarty->assign('warning_box_template', $this->getTemplatePath('warning_box'));
			$smarty->assign('files_count', 1);			
			$smarty->assign('max_upload_size', $this->getUploadMaxSize());
			$smarty->assign('page', $document);
			
			$template_path = $this->getTemplatePath();
			return $smarty->fetch($template_path);			
			
		}
		
		
		protected function getForm() {
			Application::loadLibrary('olmi/form');
			
			$form = new BaseForm();
			
			$form->addField(new TEditField('child_name', '', 100, 255));
			$form->addField(new TEditField('child_birth_date', '', 30, 30));
			$form->addField(new TEditField('parent_name', '', 100, 255));
			$form->addField(new TEditField('parent_birth_date', '', 30, 30));
			
			$form->addField(new TRadioField('contracts_available_yn', '', array(
				'Да' => 'Да',
				'Нет' => 'Нет'
			)));
			
			return $form;
		}
		
		
		
			protected function getEducatiomPeriods() {
			$start_year = 2016;
			$end_year = date('j')>6 ? date('Y') : date('Y')-1;
			
			$out = array();
			for ($y1=$start_year; $y1<=$end_year; $y1++) {
				$y2 = $y1+1;
				$out[$y1] = "$y1 - $y2";
			}
			
			return $out;
		}
		
		
		
		protected function loadAttachments() {			
			
			$fieldname = 'attachment';
			
			$out = array();
						
				
			if(!isset($_FILES[$fieldname])) continue;
			$res = $_FILES[$fieldname];
			
			foreach ($_FILES[$fieldname]['error'] as $file_idx => $upload_result) {
				if ($upload_result != UPLOAD_ERR_OK) {
					switch($upload_result) {
						case UPLOAD_ERR_NO_FILE:
							break;
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							$this->errors['attachment'] = "Файл слишком большой (ограничение размера {$this->getUploadMaxSize()})";
							break;
						default:
							$this->errors['attachment'] = 'Ошибка при загрузке файла';					
					}
					continue;
				}
				
				
				$uploaded_file_path = @$res['tmp_name'][$file_idx];
								
				
				if (!is_uploaded_file($uploaded_file_path)) {
					$this->errors['attachment'] = 'Не найден загруженный файл';
					continue;
				}
				
				
				$acs = 0;
                $ext = strtolower(pathinfo($res['name'][$file_idx], PATHINFO_EXTENSION));
                if (!in_array($ext, array('pdf', 'png', 'jpeg', 'jpg'))) {
                	$this->errors['attachment'] = "Недопустимый формат файла: $ext";
                	continue;
                }								

				$new_name = md5(uniqid());
                $temp_dir = Application::getSitePath() . '/temp/attachment/';
                if (!is_dir($temp_dir)) {
                	if (!mkdir($temp_dir, 0777, true)) {
                		$this->errors['attachment'] = "Не могу создать директорию $temp_dir";
                		continue;
                	}
                }
                
                $temp_file_path = $temp_dir . $new_name . '.' . $ext;  
                
                if (!move_uploaded_file($uploaded_file_path, $temp_file_path)) {
                	$this->errors['attachment'] = "Не могу переместить загруженный файл";
                	continue;                	
                }
                
                $out[] = $temp_file_path;
			}
				
			return $out;
			
		}
		
		
		protected function getUploadMaxSize() {			
			$upload_max_filesize = ini_get('upload_max_filesize'); 
			$post_max_size = ini_get('post_max_size');
			
			$upload_max_filesize_int = (int)str_replace('M', '', $upload_max_filesize);
			$post_max_size_int = (int)str_replace('M', '', $post_max_size);
			
			$max_size = $upload_max_filesize_int < $post_max_size_int ? $upload_max_filesize_int : $post_max_size_int;			
			return self::getFileSizeStr($max_size*1024*1024);  
		}
		
		
		protected function getFileSizeStr($size) {
			
			if (!$size) {
				return '0';
			}
			elseif ($size < 1024) {				
				return number_format($size, 0, ',', ' ') . ' байт';
			}
			elseif ($size < 1024*1024) {
				return number_format($size/1024, 2, ',', ' ') . ' КБ';
			}
			else {				
				return number_format($size/1024/1024, 2, ',', ' ') . ' МБ';
			}						
		}
		
		
		
	}