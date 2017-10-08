<?php

    class blocks extends CMSObject {
        var $id;
        var $name;
        var $block_name;
        var $html_code;

        function get_table_name() {
            return "blocks";
        }

        function mandatory_fields() {
            return array("name" => "Title", "block_name" => "Block Name");
        }

        function unique_fields() {
            return array("block_name" => "Block Name");
        }

        // ID's from blocks DB table, identifying records for email subject (signup forms,
        // contact forms, etc.) We need this list to supply text edit field instead of FCK
        // for such blocks.
        // TODO this does not work now because of DB changes. ids should be replaced with block names
        function email_subject_block_ids() {
            return array(16,17,18,19,20,21,22);
        }

        function order_by() {
            return " ORDER BY `id` ASC ";
        }

        function load($_id, $where="") {
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();
            if ($where != "") $where = " AND ".$where;
            $object = $dbEngine->LoadObject("SELECT * FROM {$table} WHERE ID={$_id} {$where}", get_class($this));
            if (!$object) return 0;
            $object->name = stripslashes($object->name);
            $object->html_code = stripslashes($object->html_code);
            return $object;
        }

        function get_count_records($param_select = ''){
            $db = Application::getDb();
            $table = $this->get_table_name();
            if($param_select != '') $param_select = 'WHERE '.$param_select;
            return $db->executeScalar("SELECT COUNT(*) FROM {$table} {$param_select}");
        }

        function load_list($page = 1, $param_select = '') {
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();
            if($param_select != '') $param_select = 'WHERE '.$param_select;
            if($page > 0){
                $limit = "LIMIT ".(($page-1)*COUNT_LIST_ADMIN).", ".COUNT_LIST_ADMIN;
            }else $limit = '';
            $query = "SELECT * FROM {$table} {$param_select} ".$this->order_by().$limit;
            $objects = $dbEngine->LoadQueryResults($query , get_class($this));
            return $objects;
        }

        function make_form(&$form) {
            global $email_subject_block_ids;

            $form->addField(new THiddenField("id"));
            $form->addField(new TEditField("name", "", 85, 100));
            $form->addField(new TEditField("block_name", "", 85, 100));
            if (in_array($this->id, $this->email_subject_block_ids())) {
                $form->addField(new TEditField("html_code", "", 85, 255));
            }
            else {
                //$form->addField(new TTextField("html_code", "", 82, 8));
                $form->addField(new TEditorField("html_code"));
            }

            return $form;
        }

        protected function createBlock($name) {
            $db = Application::getDb();
            $table = $this->get_table_name();
            $name = addslashes($name);

            $existing = $db->executeScalar("
                SELECT id FROM $table
                WHERE block_name='$name'
            ");

            if ($existing) return;
            $text_name = ucwords(str_replace('_', ' ', $name));

            $db->execute("
                INSERT INTO $table (name, block_name, html_code)
                VALUES ('$text_name', '$name', '')
            ");
        }

        function getBlockToSite($name, $create_if_not_exists=false) {
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();
            $query = "SELECT * FROM {$table} WHERE `block_name` LIKE '".addslashes($name)."'";
            $result = $dbEngine->LoadObject($query, get_class($this));
            if (!$result) {
                if ($create_if_not_exists) $this->createBlock($name);
                return "";
            }
            $result->html_code = stripslashes($result->html_code);
            return $result;
        }

        function getHtmlBlockToSite($name, $create_if_not_exists=false) {
            $block = $this->getBlockToSite($name, $create_if_not_exists);
            if (!$block) return "";
            return $block->html_code;
        }

        function getTextBlockToSite($name, $create_if_not_exists=false) {
            return strip_tags($this->getHtmlBlockToSite($name, $create_if_not_exists));
        }


    }
