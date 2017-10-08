<?php

	class AdminModule extends Module {
		
		protected $action;
		protected $ids;
		protected $objects;
		protected $original_objects;
		protected $errors;
		protected $form;
		protected $url_addition='';
		
		public function run($params=array()) {
			
			$user_session = Application::getUserSession();
			
			$user_logged = $user_session->getUserAccount();
			if (!$user_logged) Redirector::redirect('/admin/admin_login');
			if($user_logged->role != 'admin') {
				$user_session->logout();
				Redirector::redirect('/admin/admin_login');	
			}
			
			
			$this->action = Request::get('action', 'list');
			$this->ids = Request::get('ids');
			$this->errors = array();
						
			if (!$this->ids) $this->ids = array();
			if ($id = (int)Request::get('id')) $this->ids[] = $id;
			
			foreach ($this->ids as &$id) $id = (int)$id;
			
			$method_name = 'task' . ucfirst($this->action);
			
			if (!method_exists($this, $method_name)) return $this->terminate();			
			if ($this->ids) {
				$obj = Application::getObjectInstance($this->getObjectName());
				$load_params['mode'] = 'admin';				
				$ids = implode(',', $this->ids);
				$alias = $obj->getTableAlias($obj->getTableName());
				$load_params['where'][] = "$alias.id IN($ids)";
				
				$this->objects = $obj->load_list($load_params);				
				$this->original_objects = array();
				foreach ($this->objects as $k=>$o) {					
					$this->original_objects[$k] = clone $o;	
				}
			}
			else {
				$this->objects = array();
				$this->original_objects = array();
			}			
			
			call_user_func(array($this, $method_name), $params);
			
			$smarty = Application::getSmarty();
			$smarty->assign('errors', $this->errors);
			$smarty->assign('action', $this->action);
			$smarty->assign('app_img_dir', Application::getApplicationUrl() . '/static/img');
			
			$template_path = $this->getTemplatePath($this->action);	

			$page = Application::getPage();
			$title = $this->getPageTitle();
			switch ($this->action) {				
				case 'add':
					$title .= '::Создание';					
					break;
				case 'edit':
					$title .= '::Редактирование';					
					break;					
			}
			$page->setTitle($title);
			
			
			$template_path = $this->getTemplatePath($this->action);						
			return $smarty->fetch($template_path);
		}
		
		protected function getObjectName() {
			return '';
		}
		
		protected function getObjectsPerPageCount() {
			return 0;
		}		
		
		protected function getMaxUploadSize() {
			$upload_max_filesize = ini_get('upload_max_filesize'); 
			$post_max_size = ini_get('post_max_size');
			
			$upload_max_filesize_int = (int)str_replace('M', '', $upload_max_filesize);
			$post_max_size_int = (int)str_replace('M', '', $post_max_size);
			
			$max_size = $upload_max_filesize_int < $post_max_size_int ? $upload_max_filesize : $post_max_size;
			return str_replace('M', 'МБ', $max_size);
		}
		
		
		
		protected function taskList() {
			
			$load_params['mode'] = 'admin';			
			$this->beforeListLoad($load_params);
			
			$obj = Application::getObjectInstance($this->getObjectName());
			
			$page = (int)Request::get('page');
			if ($page<1) $page = 1;
			$total = $obj->count_list($load_params);
			
			
			$limit = $this->getObjectsPerPageCount();
			$offset = $limit * ($page-1);

			if ($limit) {
				$load_params['limit'] = $limit;
				$load_params['offset'] = $offset;
			}
			
			$list = $obj->load_list($load_params);
						
			foreach ($list as $item) {
				$item->edit_link = "/admin/{$this->getName()}?action=edit&amp;ids[]=$item->id";
				if ($this->url_addition) $item->edit_link .= '&amp;' . str_replace('&', '&amp;', $this->url_addition);
				 
				$item->delete_link = "/admin/{$this->getName()}?action=delete&amp;ids[]=$item->id";
				if ($this->url_addition) $item->delete_link .= '&amp;' . str_replace('&', '&amp;', $this->url_addition);
				
				$item->moveup_link = "/admin/{$this->getName()}?action=moveup&amp;ids[]=$item->id";
				if ($this->url_addition) $item->moveup_link .= '&amp;' . str_replace('&', '&amp;', $this->url_addition);
				
				$item->movedown_link = "/admin/{$this->getName()}?action=movedown&amp;ids[]=$item->id";
				if ($this->url_addition) $item->movedown_link .= '&amp;' . str_replace('&', '&amp;', $this->url_addition);
			}
			
			$this->afterListLoad($list);
			
			$smarty = Application::getSmarty();
			
			if ($limit) {
				Application::loadLibrary('olmi/pagenav');
				$link = "/admin/{$this->getName()}";
				$pagenav = new TPageNavigation($link, $total, $this->getObjectsPerPageCount(), $page);
			}
			else {
				$pagenav = null;
			}			
			$smarty->assign('pagenav', $pagenav);
			
			$add_link = "/admin/{$this->getName()}?action=add";
			if ($this->url_addition) $add_link .= '&amp;' . str_replace('&', '&amp;', $this->url_addition);
			$smarty->assign('add_link', Application::getSeoUrl($add_link));
			$smarty->assign('objects', $list);
			
		}
		
		protected function taskAdd() {
			$this->objects = array(
				Application::getObjectInstance($this->getObjectName())
			);
			$smarty = Application::getSmarty();
			$smarty->assign('edit_template_path', $this->getTemplatePath('edit'));			
			$result = $this->taskEdit();
			$this->normalizeSeq();
			return $result;
		}
		
		protected function taskEdit() {
			Application::loadLibrary('olmi/form');	
			$this->form = new BaseForm();
			
			if (!isset($this->objects[0])) return $this->terminate();
			$object = $this->objects[0];
						
			$this->form = $object->make_form($this->form);
			$this->form->LoadFromObject($object);
			
			if(Request::isPostMethod()) {
				$this->form->LoadFromRequest($_REQUEST);
				$this->form->updateObject($object);

				if ($this->action=='edit') {
					foreach ($this->getPreservedFields() as $f) {
						$object->$f = $this->original_objects[0]->$f;
					}
				}
				
				$this->errors = $this->validateObject($object);
				if (!$this->errors) {
					$this->beforeObjectSave();
					$this->saveImages();
					if (!$this->errors) {												
						if (in_array('seq', $object->getFields())) {							
							if (!$object->seq) $object->seq = $this->getSeq();
						}						
						$object->save();
						$this->afterObjectSave();
						if ($this->action == 'add') $this->renameNewObjectImageDir($object->id);
						$redirect_url = "/admin/{$this->getName()}?action=list&message=" . urldecode('Объект сохранен');
						if ($this->url_addition) $redirect_url .= '&' . $this->url_addition;
						Redirector::redirect($redirect_url);
					}										
				}				
			}
			
			$smarty = Application::getSmarty();
			$smarty->assign('form_action', "/admin/{$this->getName()}");
			
			$back_link = "/admin/{$this->getName()}?action=list";
			if ($this->url_addition) $back_link .= '&amp;' . str_replace('&', '&amp;', $this->url_addition);			
			$smarty->assign('back_link', $back_link);

			$deleteimage_link = "/admin/{$this->getName()}?action=deleteimage&ids[]=$object->id";
			if ($this->url_addition) $deleteimage_link .= '&amp;' . str_replace('&', '&amp;', $this->url_addition);			
			$smarty->assign('deleteimage_link', $deleteimage_link);
			
			$smarty->assign('form', $this->form);
			$smarty->assign('object', $object);
		}
		
		protected function getSeq() {
			$object = $this->objects[0];
			$table = $object->get_table_name();
			$db = Application::getDb();
			return (int)$db->executeScalar("
				SELECT MAX(seq)+1 FROM $table
			");
		}
		
		protected function validateObject($object) {
			return $object->validate();			
		}
		
		protected function taskDeleteimage() {			
			$field = Request::get('image_field');
			if (!$field) return $this->terminate();
						
			if (!isset($this->objects[0]->$field)) return $this->terminate();			
			$object = $this->objects[0];			
								
			if (!$object->$field) return $this->terminate();
					
			$img_dir = UPLOAD_PHOTOS . '/' . $this->getObjectName() . '/' . (int)$object->id;
			$dir = opendir($img_dir);
			while($file = readdir($dir)) {
				if (in_array($file, array('.', '..'))) continue;
				$path = $img_dir . '/' . $file; 
				if (!is_dir($path)) continue;
				$path .= '/' . $object->$field;				
				if (is_file($path)) unlink($path);
			}						
			closedir($dir);
			
			$object->$field = '';
			$object->save();
			
			$redirect_url = "/admin/{$this->getName()}?action=edit&ids[]=$object->id&message=" . urldecode('Изображение удалено');
			if ($this->url_addition) $redirect_url .= '&' . $this->url_addition;
			Redirector::redirect($redirect_url);		
			
		}
		
		protected function taskDelete() {
			$this->beforeObjectDelete();
			foreach($this->objects as $obj) {
				$obj->delete();	
				$this->deleteObjectImageDir($obj->id);
			}
			$this->afterObjectDelete();
			$this->normalizeSeq();
			$redirect_url = "/admin/{$this->getName()}?action=list&message=" . urldecode('Объект удален');
			if ($this->url_addition) $redirect_url .= '&' . $this->url_addition;
			Redirector::redirect($redirect_url);			
		}		

		protected function getImageFields() {
			return array();
		}
		
		protected function deleteObjectImageDir($object_id, $relative_path = '') {
			$dir_path = UPLOAD_PHOTOS . '/' . $this->getObjectName() . '/' . (int)$object_id . $relative_path;
			if (!is_dir($dir_path)) return;
			$dir = opendir($dir_path);
			if (!$dir) {
				$this->errors[] = "Не могу читать содержимое $dir_path";
				return;
			}
			while($file = readdir($dir)) {
				if (in_array($file, array('.', '..'))) continue;
				$file_path = $dir_path . '/' . $file; 
				if (is_dir($file_path)) $this->deleteObjectImageDir($object_id, '/' . $file);
				else {
					if (!unlink($file_path)) {
						$this->errors[] = "Не могу удалить $file_path";
						return;
					}
				}
			}			
			closedir($dir);
			@rmdir($dir_path);
		}
		
		protected function renameNewObjectImageDir($new_object_id) {
			if (!$new_object_id) return;
			$old_path = UPLOAD_PHOTOS . '/' . str_replace('/', '_', $this->getObjectName()) . '/0';			
			if (!is_dir($old_path)) return;
			$new_path = UPLOAD_PHOTOS . '/' . str_replace('/', '_', $this->getObjectName()) . '/' . (int)$new_object_id;
			echo "$old_path $new_path";
			rename($old_path, $new_path);
		}
		
		protected function saveImages() {
			$image_fields = $this->getImageFields();
			if (!$image_fields) return;
			
			$object = $this->objects[0];
			$original_object = isset($this->original_objects[0]) ? $this->original_objects[0] : null;
			
			foreach($image_fields as $fieldname=>$sizes) {
				$object->$fieldname = isset($original_object->$fieldname) ? $original_object->$fieldname : ''; 
				
				if(!isset($_FILES[$fieldname])) continue;
				$res = $_FILES[$fieldname];
				
				if ($res['error'] != UPLOAD_ERR_OK) {
					switch($res['error']) {
						case UPLOAD_ERR_NO_FILE:
							break;
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							$this->errors[] = "Файл слишком большой (ограничение размера {$this->getMaxUploadSize()})";
							break;
						default:
							$this->errors[] = 'Ошибка при загрузке файла';					
					}
					continue;
				}
				if (!is_uploaded_file(@$res['tmp_name'])) {
					$this->errors[] = 'Не найден загруженный файл';
					continue;
				}
				
                $uploaded_file_path = $res['tmp_name'];
                $acs = 0;
                $ext = strtolower(substr($res['name'], 1 + strrpos($res['name'], '.')));
                if (in_array($ext, array('jpeg', 'jpg'))) $acs = 1;
                if ($ext == 'gif') $acs = 2;
                if ($ext == 'png') $acs = 3;
                
                if (!$acs) {
                	$this->errors[] = "Недопустимый формат файла: $ext";
                	continue;
                }
                
                $new_name = md5(uniqid());
                $temp_dir = UPLOAD_PHOTOS.'/tmp';
                if (!is_dir($temp_dir)) {
                	if (!mkdir($temp_dir, 0777, true)) {
                		$this->errors[] = "Не могу создать директорию $temp_dir";
                		continue;
                	}
                }
                
                $temp_file_path = $temp_dir . $new_name . '.' . $ext;  
                
                if (!move_uploaded_file($uploaded_file_path, $temp_file_path)) {
                	$this->errors[] = "Не могу переместить загруженный файл";
                	continue;                	
                }
                
                Application::loadLibrary('_resize');
                $img_size = Application::getObjectInstance('image_size');
                
                $this->deleteObjectImageDir(0);
                if ($this->errors) continue;
                
                foreach ($sizes as $size) {                	
                	$storage_dir = UPLOAD_PHOTOS . '/' . str_replace('/', '_', $this->getObjectName()) . '/' . (int)$object->id . '/' . $size;
                	if (!is_dir($storage_dir)) {
	                	if (!mkdir($storage_dir, 0777, true)) {
	                		$this->errors[] = "Не могу создать директорию $storage_dir";
	                		continue;
	                	}                		
                	}
                	
                	$height = $img_size->getHeight($this->getObjectName(), $size);
                	$width = $img_size->getWidth($this->getObjectName(), $size);

                	$new_file_path = $storage_dir . '/' . $new_name . '.' . $ext;
                	
                    $result = resize($acs, $temp_file_path, $new_file_path, $width, $height);
                    if (is_array($result)) {
                    	foreach ($result as $r) $errors[] = $r;
                    	continue;	
                    }
                                        
                	if (isset($original_object->$fieldname) && $original_object->$fieldname) {
                		$old_file_path = $storage_dir . '/' . $original_object->$fieldname;
                		if (is_file($old_file_path)) {
                			if (!unlink($old_file_path)) {
		                		$this->errors[] = "Не могу удалить старый файл $old_file_path";
		                		continue;                				
                			}
                		}
                	}
                    
                    $object->$fieldname = $new_name . '.' . $ext;                	
                }
                
                unlink($temp_file_path);				
			}
		}		
		
		protected function beforeListLoad(&$load_params) {
			
		}
		
		protected function afterListLoad(&$list) {
			
		}
		
		protected function beforeObjectSave() {
			
		}
		
		protected function afterObjectSave() {
			
		}
				
		protected function beforeObjectDelete() {
			
		}
		
		protected function afterObjectDelete() {
			
		}
		
		protected function taskMovedown($params) {
			return $this->taskMove($params, 'down');
		}
		
		protected function taskMoveup($params) {
			return $this->taskMove($params, 'up');
		}
		
		protected function taskMove($params, $direction) {
			$object = $this->objects[0];
			$table = $object->get_table_name();
			$db = Application::getDb();
			$extra_condition = $this->neighbourExtraCondition();
			if ($extra_condition) $extra_condition = " AND $extra_condition ";
			if($direction=='up') {
				$sql = "
					SELECT id, seq FROM $table					
					WHERE seq<$object->seq
					$extra_condition
					ORDER BY seq DESC
					LIMIT 1
				";
			}
			elseif($direction=='down') {
				$sql = "
					SELECT id, seq FROM $table					
					WHERE seq>$object->seq
					$extra_condition
					ORDER BY seq ASC
					LIMIT 1
				";				
			}
			else return $this->terminate();
			
			$neighbour = $db->executeSelectObject($sql);
			if ($neighbour) {
				$db->execute("
					UPDATE $table SET seq=$object->seq
					WHERE id=$neighbour->id
				");			
				$db->execute("
					UPDATE $table SET seq=$neighbour->seq
					WHERE id=$object->id
				");
				$this->normalizeSeq();
			}
			
			$message = 'Объект перемещен ';// . $direction=='up' ? 'выше' : 'ниже';
			$redirect_url = "/admin/{$this->getName()}?action=list&message=" . urldecode($message);
			if ($this->url_addition) $redirect_url .= '&' . $this->url_addition; 
			Redirector::redirect($redirect_url);
		}
		
		protected function neighbourExtraCondition() {
			
		}
		
		protected function normalizeSeq() {
			if (!isset($this->objects[0])) return;
			$object = $this->objects[0];			
			if (!in_array('seq', $object->getFields())) return;
			
			$table = $object->get_table_name();
			$db = Application::getDb();
			
			$db->execute("SET @num=0");
			$db->execute("
				UPDATE `$table`, (
				  SELECT `$table`.*, @num:=@num+1 AS new_seq FROM `$table`
				  ORDER BY seq
				) AS `{$table}_copy`
				SET `$table`.seq = `{$table}_copy`.new_seq
				WHERE `$table`.id = `{$table}_copy`.id
			");
		}
		
		protected function getPreservedFields() {
			return array();			
		}
		
		
		public function setUrlAddition($url_addition) {
			$this->url_addition = $url_addition;
		}
		
		protected function getPageTitle() {
			return ucfirst(str_replace('_', ' ', $this->getObjectName()));
		}
		
		
	}
	
	
	
	
	
	
	