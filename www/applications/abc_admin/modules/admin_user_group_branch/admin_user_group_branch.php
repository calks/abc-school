<?php

	Application::loadLibrary('core/admin_module');
	
	class adminUserGroupBranchModule extends AdminModule {
		
		
		protected function getObjectName() {
			return 'user_group_branch';
		}

	}