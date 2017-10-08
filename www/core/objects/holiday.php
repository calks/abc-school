<?php

	class holiday extends DataObject {
		
		public $date;
		public $title;
		public $visibility;
		public $repeat_annually;
		
		public function getTableName() {
			return 'holiday';
		}
		
		
		public function mandatory_fields() {
			return array(
				'date' => 'Дата',
				'title' => 'Название'
			);
		}
		
        public function make_form(&$form) {
        	$form = parent::make_form($form);
            $form->addField(new TJSCalendarField('date', ''));
            $form->addField(new TEditField("title", "", 100, 255));
            $form->addField(new TSelectField('visibility', '', $this->getVisibilityOptions()));
            $form->addField(new TCheckboxField('repeat_annually', ''));
            
            return $form;
        }
        
        
        public function load_list($params=array()) {
        	$list = parent::load_list($params);
        	$visibility_options = $this->getVisibilityOptions();
        	
        	foreach($list as $item) {
        		$item->visibility_str = isset($visibility_options[$item->visibility]) ? $visibility_options[$item->visibility] : ''; 
        	}
        	
        	return $list;
        }
        
        protected function getVisibilityOptions() {
        	return array(
        		'all' => 'всем',
	        	'teachers_only' => 'только преподавателям',
	        	'students_only' => 'только ученикам'
        	);
        }
        
        public function save() {
        	$this->capacity = (int)$this->capacity;
        	
			$date = $this->date;
			
			if ($this->date) {
				$this->date = preg_replace('/(\d+)\.(\d+)\.(\d+)/', '$3-$2-$1', $this->date);
			}
			
			$id = parent::save();
			
			$this->date = $date;
			
			return $id;
        	
        }
        
        
				
	}
	
	
	
	
	
	
	