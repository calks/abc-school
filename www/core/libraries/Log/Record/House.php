<?php
include_once 'log/Record.php';


class Log_Record_House extends Log_Record
{
    public function __construct($first, $second)
    {
        if ($first instanceof home and $second instanceof home) {

            /*$first->normalizeTypes();
            $second->normalizeTypes();*/


            parent::__construct($first, $second);
            $idHome = (int)(empty($this->_second->id) ? $this->_first->id : $this->_second->id);
            $this->_idHome = $idHome;
            $this->_ObjectType = self::OBJECT_HOUSE;
            $this->_ObjectName = empty($this->_second->id)
                ? $this->_first->name
                : $this->_second->name;

            $this->_ObjectName = "House '{$this->_ObjectName}'";

            $this->_ID     = $idHome;
        } else {
            throw new Exception('Unknown objects');
        }
    }
}
