<?php


    class FormManager {

        public function createForm($object_name, $field_set=null) {
            Application::loadObjectClass($object_name);
            return call_user_func(array($object_name, 'make_form'));
        }

    }
