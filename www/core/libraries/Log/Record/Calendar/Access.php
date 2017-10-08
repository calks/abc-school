<?php
include_once 'Record/Calendar.php';

class Log_Record_Calendar_Access extends Log_Record_Calendar
{
    public function __construct($first, $second)
    {
        parent::__construct($first, $second);
        $this->_ObjectType = self::OBJECT_ACCESS;
        $this->_ObjectName = empty($this->_second->id)
                ? $this->_first->name
                : $this->_second->name;
        $this->_ObjectName = "Availability in '{$this->_ObjectName}' calendar";
    }

    protected function getDiff()
    {
        $diffCalendars = parent::getDiff();
        $oldAccess = $this->_sortAccess($this->_first->access);
        $newAccess = $this->_sortAccess($this->_second->access);

        $diffAccess = array();
        $fields = get_class_vars('homeaccess');

        $free     = array_keys(array_diff_key($oldAccess, $newAccess));
        foreach ($free as $record) {
            $access = clone $oldAccess[$record];
            $access->book_type = "{$record}|free";

            $oldAccess[$record]->book_type = "{$oldAccess[$record]->cdate}|{$oldAccess[$record]->book_type}";
            $diffAccess = array_merge($diffAccess, $this->_diff( $oldAccess[$record], $access, $fields));
        }

        $change   = array_keys(array_intersect_key($oldAccess, $newAccess));
        foreach ($change as $record) {
            $oldAccess[$record]->book_type = "{$oldAccess[$record]->cdate}|{$oldAccess[$record]->book_type}";
            $newAccess[$record]->book_type = "{$newAccess[$record]->cdate}|{$newAccess[$record]->book_type}";
            $diffAccess = array_merge($diffAccess, $this->_diff( $oldAccess[$record],  $newAccess[$record], $fields));
        }

        $reserved = array_keys(array_diff_key($newAccess, $oldAccess));
        foreach ($reserved as $record) {
            $access = clone $newAccess[$record];
            $access->book_type = 'free';
            $access->book_type = "{$record}|free";

            $newAccess[$record]->book_type = "{$newAccess[$record]->cdate}|{$newAccess[$record]->book_type}";
            $diffAccess = array_merge($diffAccess, $this->_diff( $access, $newAccess[$record], $fields));
        }

        return array_merge($diffCalendars, $diffAccess);
    }

    private function _sortAccess($accessDates)
    {
        $accessList = array();
        if (is_array($accessDates)) {
            foreach ($accessDates as $access) {
                if ($access instanceof homeaccess ) {
                    $accessList[$access->cdate] = clone $access;
                }
            }
        }

        return $accessList;
    }
}
