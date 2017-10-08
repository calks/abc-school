<?php

	class slideshow_image extends DataObject {
		
		public $image;
		public $seq;
		
		public function get_table_name() {
			return "slideshow_image";
		}
		
		public function order_by() {
			return "seq";
		}
				
		public function make_form(&$form) {
			$form = parent::make_form($form);
			
			$form->addField(new THiddenField('seq'));
			$form->addField(new TFileField('image'));

			return $form;			
		}
		
	}