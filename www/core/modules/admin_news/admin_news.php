<?php

	Application::loadLibrary('core/admin_module');

	class AdminNewsModule extends AdminModule {
		
				
		protected function getObjectName() {
			return 'news';
		}
		
		protected function getImageFields() {
			return array(
				'image' => array('small', 'big')
			);
		}
		
		
	}