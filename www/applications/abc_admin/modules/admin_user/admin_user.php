<?php

	Application::loadLibrary('core/admin_module');

	class AdminUserModule extends AdminModule {
		
			
		protected function getImageFields() {
			return array(
				'avatar' => array('small', 'thumb', 'huge')
			);
		}		
		
		protected function taskList() {
			$page = Application::getPage();
			/*$page->addScript('/core/modules/messages/static/messages.js');
			$page->addStylesheet(Application::getSiteUrl() . '/core/modules/messages/static/messages.css');*/
			$page->addScript('/applications/abc_admin/modules/admin_user/static/user.js');
			
			Application::loadLibrary('olmi/form');
			$group_actions_form = new BaseForm();
			$group = Application::getEntityInstance('user_group');
			$group_actions_form->addField(new TSelectField('group_id', '', $group->getSelect('выберите...')));
			
			$smarty = Application::getSmarty();
			$smarty->assign('group_actions_form', $group_actions_form);
			$smarty->assign('url_addition', $this->url_addition);			
			$smarty->assign('csv_link', "/admin/{$this->getName()}?action=get_csv");
			
			return parent::taskList();
		}
		
		
		protected function taskAssign_group() {
			
			$new_group_id = (int)Request::get('new_group_id');
			
			foreach($this->objects as $object) {
				if ($object->role == 'student'){
					$object->group_id = array($new_group_id);
				}
				else {
					$object->group_id[] = $new_group_id;
				} 
				$object->group_id = array_unique($object->group_id);
				$object->save();
			}
			
			$message = 'Группа назначена';
			$redirect_url = "/admin/{$this->getName()}?action=list&message=" . urldecode($message);
			$url_addition = $this->url_addition;
			if ($this->page > 1) $url_addition .= $url_addition ? "&page=$this->page" : "page=$this->page";
			if ($url_addition) $redirect_url .= '&' . $url_addition;
			 
			Redirector::redirect($redirect_url);
			
		}
		
		protected function taskEdit() {
			$page = Application::getPage();
			$page->addScript('/applications/abc_admin/modules/admin_user/static/group_select.js');
			
			if ($this->action == 'add') {
				$this->objects[0]->role = 'student';
				$this->objects[0]->active = 1;
			}
			
			return parent::taskEdit();
		}		
		
		
		protected function getObjectName() {
			return 'user';			
		}
		
		protected function beforeListLoad(&$load_params) {
			
			$filter = Application::getFilter('user');

			$group_id = isset($_GET['group_id']) ? (int)$_GET['group_id'] : null;
			if ($group_id) {
				$filter->setValue('search_group', array($group_id));
				$filter->setValue('search_teacher', array());
				$filter->setValue('search_branch', array());
			} 
			
			$filter->set_params($load_params);			
			$smarty = Application::getSmarty();						
			$smarty->assign('filter', $filter);
			$smarty->assign('relation_map_json', json_encode($filter->getRelationMap()));
		}
		
		
		protected function afterListLoad(&$list) {
			$user = Application::getEntityInstance('user');
			$roles = $user->getRoleSelect();
			foreach($list as $item) {
				$item->role_str = $roles[$item->role];
			}
		}
		
		protected function getObjectsPerPageCount() {
			$filter = Application::getFilter('user');
			$limit = $filter->getValue('search_limit');
			if (!$limit) return 20;
			if ($limit=='all') return null;
			
			return $limit;
		}			
	
		
		
	}
	
	
	