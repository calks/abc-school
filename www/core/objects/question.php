<?php

	class question extends DataObject {

		public $active;
		public $created;
		public $author_name;
		public $author_email;
		public $question;
		public $answer;
		public $author_notified;
		
		public function get_table_name() {
			return 'question';
		}
		
		
		public function make_form(&$form) {
			$form = parent::make_form($form);
			
			$form->addField(new TCheckboxField('active', ''));
			$form->addField(new THiddenField('created'));
			$form->addField(new TEditField('author_name', '', 100, 255));
			$form->addField(new TEditField('author_email', '', 100, 100));
			$form->addField(new TTextField('question', '', 98, 4));
			$form->addField(new TTextField('answer', '', 98, 4));
			$form->addField(new THiddenField('author_notified'));
			
			return $form;			
		}
		
	}
	
	
	
	