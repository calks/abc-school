<?php

    class news extends DataObject {
        var $id;        
        var $date;
        var $title;        
        var $story;        
        var $active;
        var $image;

        function get_table_name() {
            return "news";
        }

        function mandatory_fields() {
            return array(
            	"title" => "Заголовок", 
            	"date" => "Дата", 
            	"story" => "Текст новости"
            );
        }

        function order_by() {
            return " date DESC ";
        }

        function make_form(&$form) {
            $form->addField(new THiddenField("id"));
            $form->addField(new TEditField("title", "", 85));
            $form->addField(new TDateField("date", '', true, array("separator" => "", 'year.min' => '-5', 'year.max' => '+5')));
            $form->addField(new TEditorField("story", "", 800, 200));
            $form->addField(new TCheckboxField("active", "0"));
            $form->addField(new TFileField("image", 100));
            return $form;
        }
        
        function load_list($params) {
        	$mode = isset($params['mode']) ? $params['mode'] : '';

        	if ($mode=='front') {
        		$alias = $this->get_table_abr();
        		$params['where'][] = "$alias.active=1";
        	}
        	
        	$list = parent::load_list($params);
        	
        	return $list;
        	
        }
        
        function count_list($params) {
        	$mode = isset($params['mode']) ? $params['mode'] : '';
        	
        	if ($mode=='front') {
        		$alias = $this->get_table_abr();
        		$params['where'][] = "$alias.active=1";
        	}
        	
        	return parent::count_list($params);
        
        }


    }
