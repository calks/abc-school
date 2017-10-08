<?php

	class gallery_photo extends DataObject {		
		public $seq;
		public $comment;
		public $image;
		public $gallery_id;
		
		public function get_table_name() {
			return "gallery_photo";
		}		
				
        function order_by() {
            return " seq ";
        }

        function make_form(&$form) {
        	$form->addField(new THiddenField("id"));            
            $form->addField(new TEditField("comment", "", 85, 255));
            $form->addField(new THiddenField("seq"));            
            $form->addField(new TFileField("image", 100));
            
            $gallery = Application::getObjectInstance('gallery');
            $form->addField(new TSelectField('gallery_id', '', $gallery->getSelect('-- Не выбрана --')));
            
            return $form;
        }		
		
	}