<?php

	class gallery extends DataObject {
		public $name;
		public $seq;
		public $active;
		public $image;
		
		public function get_table_name() {
			return "gallery";
		}
		
        function mandatory_fields() {
            return array(
            	"name" => "Название"
            );
        }
				
        function order_by() {
            return " seq ";
        }

        function make_form(&$form) {
            $form->addField(new THiddenField("id"));
            $form->addField(new TEditField("name", "", 85, 255));
            $form->addField(new THiddenField("seq"));
            $form->addField(new TCheckboxField("active", "0"));
            $form->addField(new TFileField("image", 100));
            return $form;
        }
		
        function getSelect($add_null_item) {
        	$db = Application::getDb();
        	$table = $this->get_table_name();

        	$sql = "
        		SELECT id, name 
        		FROM $table
        	";        	
        	$data = $db->executeSelectAllObjects($sql);
        	
        	$out = get_empty_select($add_null_item);
        	foreach($data as $item) $out[$item->id] = $item->name;
        	return $out;
        }
        
		
	}