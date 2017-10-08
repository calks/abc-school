<?php
include_once 'Log/Record.php';

class Log_Record_Auth extends Log_Record
{
    public function __construct()
    {
        parent::__construct(null, null);
        $this->_ObjectType = self::OBJECT_AUTH;
        $this->_ObjectName = 'Sign In/Out';
    }

    protected function _diff()
    {
        return false;
    }

    public function signIn()
    {
        return $this->save(Log::ACTION_SIGN_IN);
    }

    public function signOut()
    {
        return $this->save(Log::ACTION_SIGN_OUT);
    }

    public function getFields()
    {
        return null;
    }
}
