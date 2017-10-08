<?php

	class quizz_question_answer extends DataObject {
		
		public $question_id;
		public $content;		
		public $seq;
		public $is_right;
		
		public function get_table_name() {
			return "quizz_question_answer";
		}
		
	    function mandatory_fields() {
            return array(
            	"content" => "Текст ответа"
            );
        }

        function order_by() {
            return " seq ";
        }		
		
		public function make_form(&$form) {
			$form->addField(new THiddenField("id"));			
            $form->addField(new THiddenField("question_id"));
            $form->addField(new THiddenField("seq"));
            $form->addField(new TEditorField("content", ""));
            $form->addField(new TCheckboxField('is_right', ''));
            
            return $form;
		}
		
		public function save() {
			$this->is_right = (int)$this->is_right;
			return parent::save();
		}
		
	}