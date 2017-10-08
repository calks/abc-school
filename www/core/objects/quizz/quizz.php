<?php

	class quizz extends DataObject {
		
		public $name;
		public $active;
		public $seq;
		public $shuffle_questions;
		
		public function get_table_name() {
			return "quizz";
		}
		
	    function mandatory_fields() {
            return array(
            	"name" => "Название"
            );
        }

        function order_by() {
            return " seq ";
        }		
		
		public function make_form(&$form) {
            $form->addField(new THiddenField("id"));
            $form->addField(new THiddenField("seq"));
            $form->addField(new TEditField("name", "", 85));
            $form->addField(new TCheckboxField("active", "0"));
            $form->addField(new TCheckboxField("shuffle_questions", "0"));
            
            return $form;
		}
		
		public function save() {
			$this->active = (int)$this->active;
			$this->shuffle_questions = (int)$this->shuffle_questions;
			
			return parent::save();
		}
		
	}