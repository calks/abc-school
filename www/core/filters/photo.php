<?php

    Application::loadFilter('base');

    class PhotoFilter extends BaseFilter {

        function add_fields() {
        	$gallery = Application::getObjectInstance('gallery');
        	$this->addField(new TSelectField('search_gallery', '', $gallery->getSelect('-- Любая --'))); 
        }


        function set_params(&$params) {
            $db = Application::getDb();
            
            $gallery_id = (int)$this->getValue('search_gallery');
            
            if($gallery_id) {
            	$object = Application::getObjectInstance('gallery/photo');            	
            	$alias = $object->get_table_abr();
            	$params['where'][] = "$alias.gallery_id=$gallery_id";
            }
        }
    }

