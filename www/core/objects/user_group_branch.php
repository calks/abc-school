<?php

	class user_group_branch extends DataObject {
		
		public $title;
		
		public function getTableName() {
			return 'user_group_branch';
		}
		
		public function order_by() {
			return 'id';
		}
		
		public function mandatory_fields() {
			return array(
				'title' => 'Название'
			);
		}
		
        public function make_form(&$form) {
        	$form = parent::make_form($form);
            $form->addField(new TEditField("title", "", 100, 255));
            return $form;
        }
		
		
	}