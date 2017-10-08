<?php
include_once 'Log/Record.php';
include_once 'special.class.php';
include_once 'homeclass.inc.php';

class Log_Record_Special extends Log_Record
{
    public function __construct($first, $second)
    {
        if ($first instanceof special and $second instanceof special) {
            $first->normalizeTypes();
            $second->normalizeTypes();

            parent::__construct($first, $second);

            $idHome = (int)(empty($this->_second->home_id)
                ? $this->_first->home_id
                : $this->_second->home_id);
            $this->_idHome = $idHome;
            $this->_ObjectType = self::OBJECT_SPECIAL;

            $home = new home();
            $home = $home->load($idHome);
            $this->_ObjectName = 'Specials for ' . @$home->name;

            $this->_ID     = (int)(empty($this->_second->id)
                ? $this->_first->id
                : $this->_second->id);
        } else {
            throw new Exception('Unknown objects');
        }
    }
}