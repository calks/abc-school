<?php

    class DataStorage {

        protected static $table_aliases;

        protected function getFieldList($object_name) {
            Application::loadObjectClass($object_name);
            $fields = get_class_vars($object_name);

            $out = array();
            foreach($fields as $f) {
                $f = strtolower($f);
                if (substr($f, 0, 9) == 'internal_') continue;
                $out[] = $f;
            }
            return $out;
        }


        protected function getSQLFields($object_name, $params=array()) {
            $fields = isset($params['fields']) ? $params['fields'] : array();
            $merge_mode = isset($params['fields_merge_mode']) ? $params['fields_merge_mode'] : 'extend';
            switch($merge_mode) {
                case 'extend':
                    $fields = array_merge($fields, self::getFieldList($object_name));
                    break;
            }

            $out = array();
            foreach($fields as $f) $out[] = "`$f`";
        }


        protected function getSQLFrom($object_name, $params=array()) {
            $from = array();
            $object_table = call_user_func(array($object_name, 'get_table_name'));
            $from[] = "`$object_table`";
        }


        protected function getSQLWhere($object_name, $params=array()) {
            $where = isset($params['where']) ? $params['where'] : array();
            if (!$where) return '';
            if (!is_array($where)) $where = array($where);
            $where = implode(' AND ', $where);
            return " WHERE $where ";
        }


        protected function getLoadListQuery($object_name, $params=array()) {
            $fields = self::getSQLFields($object_name, $params);
            $from = self::getSQLFrom($object_name, $params);
            $where = self::getSQLWhere($object_name, $params);

            $sql = " SELECT $fields FROM $from $where ";
            return $sql;
        }


        public function loadObjectList($object_name, $params=array()) {
            Application::loadObjectClass($object_name);

            $db = Application::getDB();
            $sql = self::getLoadListQuery($object_name, $params);
            $list = $db->executeSelectAll($sql);

            $out = array();
            foreach($list as $item) {
                $obj = new $object_name();
                foreach($item as $field=>$value) {
                    $obj->$field = $value;
                }
            }

            return $out;
        }


        public function getEmptyObject($object_name) {
            Application::loadObjectClass($object_name);
            return new $object_name();
        }

        public function loadObject($object_name, $id) {
            $id = (int)$id;
            if (!$id) return null;
            Application::loadObjectClass($object_name);

            $params['where'] = " id=$id ";
            $list = self::loadObjectList($object_name, $params);
            if (!$list) return null;
            return $list[0];
        }


        public function saveObject(DataObject &$object) {
            $object_name = get_class($object);
            $object_table = call_user_func(array($object_name, 'get_table_name'));
            $fields = self::getFieldList($object_name);

            $insert_fields = array();
            $insert_values = array();
            $update = array();

            foreach($fields as $field) {
                $value = $object->$field;
                $value = is_null($value) ? "NULL" : "'" . addslashes($value) . "'";
                $insert_fields[] = "`$field`";
                $insert_values[] = $value;
                if ($field == 'id') continue;
                $update[] = "`$field`=$value";
            }

            $insert_fields = implode(",", $insert_fields);
            $insert_values = implode(",", $insert_values);
            $update = implode(",", $update);

            $sql = "
                INSERT INTO `$object_table` ($insert_fields)
                VALUES($insert_values)
                ON DUPLICATE KEY UPDATE $update
            ";

            $db = Application::getDb();
            $db->execute($sql);

            $object->id = $db->getLastAutoIncrementValue();
            return $object->id;
        }


    }




