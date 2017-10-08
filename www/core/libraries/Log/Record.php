<?php
//include_once 'Log.php';

class  Log_Record extends Log
{
    const ACTION_ADD      = 'ADD';
    const ACTION_UPDATE   = 'UPDATE';
    const ACTION_DELETE   = 'DELETE';

    const OBJECT_HOUSE    = 'HOUSE';
    const OBJECT_CALENDAR = 'CALENDAR';
    const OBJECT_SPECIAL  = 'SPECIAL';
    const OBJECT_ACCESS   = 'ACCESS';

    protected $_idRecord;
    protected $_idHome;
    protected $_ObjectType;
    protected $_ObjectName;
    protected $_ID;
    protected $_Action;
    protected $_ActionDate;

    private function _checkAction()
    {
        return in_array($this->_Action, array(
            self::ACTION_ADD,
            self::ACTION_UPDATE,
            self::ACTION_DELETE,
        ));
    }

    protected function getDiff()
    {
        $fields = get_class_vars(get_class($this->_first));
        /*foreach ($fields as $k=>$f) if (strpos($k, 'internal_')===0) unset($fields[$k]);
        print_r($fields);*/
        return $this->_diff($this->_first, $this->_second, $fields);
    }

    protected function _diff($old, $new, $fields)
    {
        /*$_old = get_object_vars($old);
        $_old = array_intersect_key($_old, $fields);

        $_new = get_object_vars($new);
        $_new = array_intersect_key($_new, $fields);

        $diff = array_diff_assoc($_new, $_old);

        $difference = array();
        foreach ($diff as $field => $value) {
            $difference[] = array(
                'table' => $old->get_table_name(),
                'field' => $field,
                'old'   => $_old[$field],
                'new'   => $_new[$field],
            );
        }
        */

        $keys = array_keys($fields);
        $difference = array();
        foreach($keys as $key) {
            $new_value = @$new->$key;
            $old_value = @$old->$key;
            if (
                is_array($new_value) ||
                is_object($new_value) ||
                is_array($old_value) ||
                is_object($old_value)
                ) continue;
            if ($new_value != $old_value) $difference[] = array(
                'table' => $old->get_table_name(),
                'field' => $key,
                'old'   => $old_value,
                'new'   => $new_value
            );
        }

        return $difference;
    }

    private function _saveDiff($difference)
    {
        global $db;



        foreach ($difference as $diff) {
            $field = $this->_getField($diff['table'], $diff['field']);

            if (! is_array($field)) {
                continue;
            }

            $fields = array(
                'idRecord'    => $this->_idRecord,
                'idField'     => $field['idField'],
                'BeforeValue' => $diff['old'],
                'AfterValue'  => $diff['new'],
            );

            $sql = $this->_assembleInsertQuery(self::TABLE_NAME_ENTRY, $fields);

            $db->execute($sql);
        }

    }

    private function _save($action)
    {
        global $db;

        if (! $this->_checkUser()) {
            return false;
        }

        $this->_Action = $action;
        if (! $this->_checkAction()) {
            return false;
        }

        if (! $this->_insert()) {
            return false;
        }


        return true;
    }

    protected function save($action)
    {
        $diff = $this->getDiff();

        if (!empty ($diff)) {
            if (!$this->_save($action) ) {
                return false;
            }
            $this->_saveDiff($diff);
            return true;
        } else {
            return false;
        }
    }

    private function _assembleInsertQuery($table, $fields)
    {
        global $dbEngine;
        foreach ($fields as $name => $value) {
            $fields[$name] = $dbEngine->prepareValue($value);
        }
        return "INSERT INTO "
            . $table
            . '('
            . "`" . implode("`, `", array_keys($fields)) . "`"
            . ') VALUES ('
            . implode(", ", array_values($fields))
            . ')';
    }

    private function _insert()
    {
        global $db, $dbEngine;

        $this->_ActionDate = time();

        $fields = array(
            'idUser'     => (int)$this->_idUser,
            'UserType'   => $this->_UserType,
            'idHome'     => (int)$this->_idHome,
            'ObjectType' => $this->_ObjectType,
            'ObjectName' => $this->_ObjectName,
            'ID'         => (int)$this->_ID,
            'Action'     => $this->_Action,
        );

        $sql = $this->_assembleInsertQuery(self::TABLE_NAME_RECORD, $fields);


        $result = $db->execute($sql);
        if ($result) {
            $this->_idRecord = $db->getLastAutoincrementValue();
            return true;
        } else {
            return false;
        }


    }

    private function _getField($table, $field)
    {
        global $db;

        $tableName = $db->escapeString($table);
        $fieldName = $db->escapeString($field);

        $table = self::TABLE_NAME_FIELD;

        $sql = <<<SQL
SELECT *
FROM {$table}
WHERE
    `TableName` = '{$tableName}' AND
    `FieldName` = '{$fieldName}'
LIMIT 1
SQL;

        return $db->executeSelectAssocArray($sql);
    }

    public function LogAdd()
    {
        $class = get_class($this->_first);
        $this->_first = new $class;
        return $this->save(self::ACTION_ADD);
    }

    public function LogUpdate()
    {
        return $this->save(self::ACTION_UPDATE);
    }

    public function LogDelete()
    {
        $class = get_class($this->_second);
        $this->_second = new $class;
        return $this->save(self::ACTION_DELETE);
    }

    public static function factory($type, $oldRecord, $newRecord)
    {
        $log = null;
        switch ($type) {
            case self::OBJECT_HOUSE:
                require_once 'Record/House.php';
                $log = new Log_Record_House($oldRecord, $newRecord);
                break;

            case self::OBJECT_SPECIAL:
                require_once 'Record/Special.php';
                $log = new Log_Record_Special($oldRecord, $newRecord);
                break;

            case self::OBJECT_CALENDAR:
                require_once 'Record/Calendar.php';
                $log = new Log_Record_Calendar($oldRecord, $newRecord);
                break;

            case self::OBJECT_ACCESS:
                require_once 'Record/Calendar/Access.php';
                $log = new Log_Record_Calendar_Access($oldRecord, $newRecord);
                break;

            default: return null;
        }

        $log->setUser(@$_SESSION['is_login_owner'], @$_SESSION['auth_user_type']);
        return $log;
    }
}
