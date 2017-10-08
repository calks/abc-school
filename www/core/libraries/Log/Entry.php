<?php
class Log_Entry
{
    protected $idRecord;
    protected $idField;
    protected $TableName;
    protected $FieldName;
    protected $FullTableName;
    protected $FullFieldName;
    protected $FieldType;

    protected $BeforeValue;
    protected $AfterValue;

    const TYPE_BOOLEAN  = 'BOOLEAN';
    const TYPE_NUMBER   = 'NUMBER';
    const TYPE_TEXT     = 'TEXT';
    const TYPE_DATETIME = 'DATE_TIME';

    static public function load($fields)
    {
        $entry = null;
        switch ($fields['TableName']) {
            case 'homes':
                require_once 'Log/Entry/House.php';
                $entry = new  Log_Entry_House();
                break;

            case 'specials':
                require_once 'Log/Entry/Special.php';
                $entry = new  Log_Entry_Special();
                break;

            case 'home_calendars':
                require_once 'Log/Entry/Calendar.php';
                $entry = new  Log_Entry_Calendar();
                break;

            case 'calendar':
                require_once 'Log/Entry/Calendar/Access.php';
                $entry = new  Log_Entry_Calendar_Access();
                break;

            default: return null;
        }

        $entry->_loadFromArray($fields);
        return $entry;
    }

    public function getFields()
    {
        return get_object_vars($this);
    }

    private function _loadFromArray($fields)
    {
        $objFields = get_object_vars($this);
        if(is_array($fields)) {
            foreach ($fields as $name => $value) {
                if (array_key_exists($name, $objFields)) {
                    $this->{$name} = $value;
                }
            }
        }
        $this->_parse();
    }

    protected function _parse()
    {
        switch ($this->FieldType) {
            case self::TYPE_BOOLEAN:
                $this->BeforeValue = $this->_parseBoolean($this->BeforeValue);
                $this->AfterValue  = $this->_parseBoolean($this->AfterValue);
                break;

            case self::TYPE_NUMBER:
                $this->BeforeValue = $this->_parseNumber($this->BeforeValue);
                $this->AfterValue  = $this->_parseNumber($this->AfterValue);
                break;

            case self::TYPE_DATETIME:
                $this->BeforeValue = $this->_parseDateTime($this->BeforeValue);
                $this->AfterValue  = $this->_parseDateTime($this->AfterValue);
                break;

            case self::TYPE_TEXT:
                $this->BeforeValue = $this->_parseText($this->BeforeValue);
                $this->AfterValue  = $this->_parseText($this->AfterValue);
                break;
        }
    }

    private function _parseDateTime($value)
    {
        $timestamp = strtotime($value);
        if ($timestamp > 0) {
            $date = date('m/d/Y H:i:s', $timestamp);
            return preg_replace('/\s00:00:00$/i', '', $date);
        } else {
            return 'n/a';
        }
    }

    private function _parseNumber($value)
    {
        return is_numeric($value) ? $value : 'n/a';
    }

    private function _parseText($value)
    {
        return stripslashes($value);
    }

    private function _parseBoolean($value)
    {
        return (bool)$value ? 'yes' : 'no';
    }



}