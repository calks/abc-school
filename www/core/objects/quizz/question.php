<?php

	class quizz_question extends DataObject {
		
		public $quizz_id;
		public $content;		
		public $seq;
		
		public function get_table_name() {
			return "quizz_question";
		}
		
	    function mandatory_fields() {
            return array(
            	"content" => "Текст вопроса"
            );
        }

        function order_by() {
            return " seq ";
        }		
		
		public function make_form(&$form) {
			$form->addField(new THiddenField("id"));
            $form->addField(new THiddenField("quizz_id"));
            $form->addField(new THiddenField("seq"));
            $form->addField(new TEditorField("content", ""));
            
            return $form;
		}
		
	}