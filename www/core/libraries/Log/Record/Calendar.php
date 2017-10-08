<?php
include_once 'Record.php';
Application::loadObjectClass('home/calendar');
//include_once 'homecalendarsclass.inc.php';

class Log_Record_Calendar extends Log_Record
{
    public function __construct($first, $second)
    {
        if ($first instanceof homecalendars and $second instanceof homecalendars) {
            $first->normalizeTypes();
            $second->normalizeTypes();

            parent::__construct($first, $second);

            $idHome = (int)(empty($this->_second->home_id)
                ? $this->_first->home_id
                : $this->_second->home_id);
            $this->_idHome = $idHome;
            $this->_ObjectType = self::OBJECT_CALENDAR;
            $this->_ObjectName = empty($this->_second->id)
                ? $this->_first->name
                : $this->_second->name;
            $this->_ObjectName = "Calendar '{$this->_ObjectName}'";

            $this->_ID     = (int)(empty($this->_second->id)
                ? $this->_first->id
                : $this->_second->id);
        } else {
            throw new Exception('Unknown objects');
        }
    }
}