<?php

set_include_path(get_include_path()
    . PATH_SEPARATOR . Application::getSitePath() . '/core/libraries'
    . PATH_SEPARATOR . Application::getSitePath() . '/core/libraries/Log'
    . PATH_SEPARATOR . Application::getSitePath() . '/core/libraries/Log/Record'
    . PATH_SEPARATOR . Application::getSitePath() . '/core/libraries/Log/Record/Calendar'
    . PATH_SEPARATOR . Application::getSitePath() . '/core/libraries/Log/Entry'
    . PATH_SEPARATOR . Application::getSitePath() . '/core/libraries/Log/Entry/Calendar'
);


Application::loadLibrary('olmi/class');
Application::loadLibrary('log/Record');
Application::loadLibrary('log/Entry');

class Log
{
    protected $_idUser;
    protected $_UserType;

    protected $_first;
    protected $_second;

    const USER_TYPE_ADMIN   = 'ADMIN';
    const USER_TYPE_OWNER   = 'OWNER';
    const USER_TYPE_MANAGER = 'MANAGER';

    const TABLE_NAME_RECORD  = 'log_records';
    const TABLE_NAME_ENTRY   = 'log_entries';
    const TABLE_NAME_FIELD   = 'log_fields';

    public function __construct($first = null, $second = null)
    {
        $this->_first  = $first;
        $this->_second = $second;
    }


    public function setUser($idUser, $userType)
    {
        $this->_idUser   = (int)$idUser;
        $this->_UserType = $userType;
        return $this->_checkUser();
    }

    protected function _checkUser()
    {
        global $db;

        $userTypes = array(
            self::USER_TYPE_ADMIN,
            self::USER_TYPE_OWNER,
            self::USER_TYPE_MANAGER,
        );

        if (! in_array($this->_UserType, $userTypes)) {
            return false;
        }

        $tables = array(
            self::USER_TYPE_ADMIN   => 'cms_users',
            self::USER_TYPE_OWNER   => 'owners',
            self::USER_TYPE_MANAGER => 'managers',
        );

        $fields = array(
            self::USER_TYPE_ADMIN   => 'id',
            self::USER_TYPE_OWNER   => 'id',
            self::USER_TYPE_MANAGER => 'id',
        );

        $sql = <<<SQL
SELECT COUNT(*)
FROM {$tables[$this->_UserType]}
WHERE
    `{$fields[$this->_UserType]}` = {$this->_idUser}
SQL;
        return (bool)$db->executeScalar($sql);
    }

    public function count()
    {
        global $db;

        $user = (int)$this->_idUser;
        switch ($this->_UserType) {
            case self::USER_TYPE_ADMIN:
                $sql = "SELECT COUNT(*) FROM " . self::TABLE_NAME_RECORD;
                break;

            case self::USER_TYPE_OWNER:
                $sql =
                    "SELECT COUNT(*)
                    FROM " . self::TABLE_NAME_RECORD . " AS lr
                        INNER JOIN `homes` AS h ON
                            lr.`idHome` = h.`id` AND
                            h.`owner_id` = {$user}
                         ";
                break;

            case self::USER_TYPE_MANAGER:
                $sql =
                    "SELECT COUNT(*)
                    FROM " . self::TABLE_NAME_RECORD . " AS lr
                    WHERE
                        lr.`idUser` = {$user}";
                break;

            default:
                return 0;
        }

        return $db->executeScalar($sql);
    }

    public function getUserInfo()
    {
        global $db;

        $user = (int)$this->_idUser;
        switch ($this->_UserType) {
            case self::USER_TYPE_ADMIN:
                $sql =
                    "SELECT
                        `id`,
                        CONCAT_WS(' ', firstname, lastname) AS `name`,
                        email,
                        'Admin' AS `type`
                    FROM `cms_users`
                    WHERE
                        `id` = {$user}";
                break;

            case self::USER_TYPE_OWNER:
                $sql =
                    "SELECT
                        `id`,
                        CONCAT_WS(' ', owner_first, owner_last) AS `name`,
                        `owner_email` AS `email`,
                        'Owner' AS `type`
                    FROM `owners`
                    WHERE
                        `id` = {$user}";
                break;

            case self::USER_TYPE_MANAGER:
                $sql =
                    "SELECT
                        `id`, `name`, `email`,
                        'Manager' AS `type`
                    FROM `managers`
                    WHERE
                        `id` = {$user}";
                break;

            default:
                return array(
                    'id' =>'', 'name' => '', 'email' => '', 'type' => '');
        }

        return $db->executeSelectAssocArray($sql);
    }

    public function getList($page = 1, $limit = null)
    {
        global $db;
        $user = (int)$this->_idUser;
        $tblRecords = self::TABLE_NAME_RECORD;
        $sql = "SELECT lr.*,
                        CONCAT_WS(' ', u.`firstname`, u.`lastname`, o.`owner_first`, o.`owner_last`, m.`name`) AS UserName
                FROM {$tblRecords} AS lr
                    LEFT JOIN `cms_users` AS u ON u.`id` = lr.`idUser` AND lr.`UserType` = 'ADMIN'
                    LEFT JOIN `owners`    AS o ON o.`id` = lr.`idUser` AND lr.`UserType` = 'OWNER'
                    LEFT JOIN `managers`  AS m ON m.`id` = lr.`idUser` AND lr.`UserType` = 'MANAGER'";

        switch ($this->_UserType) {
            case self::USER_TYPE_ADMIN:
                break;

            case self::USER_TYPE_OWNER:
                $sql .=
                    "
                        INNER JOIN `homes` AS h ON
                            lr.`idHome` = h.`id` AND
                            h.`owner_id` = {$user}
                         ";
                break;

            case self::USER_TYPE_MANAGER:
                $sql .=
                    "
                    WHERE
                        lr.`idUser` = {$user}";
                break;

            default:
                return array();
        }

        $sql .= "
            ORDER BY lr.`ActionDate` DESC
        ";
        if (! is_null($limit)) {
            $page = (int)$page;
            $page = $page > 0 ? $page : 1;
            $offset = ($page - 1) * $limit;
            $limit = (int)$limit;

            $sql .= "
                LIMIT {$offset}, {$limit}
            ";
        }

        return $db->executeSelectAll($sql);
    }

    public function find($id)
    {
        global $db;

        $idRecord = (int)$id;

        $tblRecords = self::TABLE_NAME_RECORD;
        $sql = "SELECT * FROM {$tblRecords} WHERE `idRecord` = {$idRecord}";
        $record = $db->executeSelectAssocArray($sql);
        if (! is_array($record)) {
            return null;
        }

        $tblEntries = self::TABLE_NAME_ENTRY;
        $tblFields  = self::TABLE_NAME_FIELD;
        $sql =
            "SELECT *
            FROM {$tblEntries} AS le
                INNER JOIN {$tblFields} AS lf USING(`idField`)
            WHERE
                `idRecord` = {$idRecord}";
        $record['entries'] = $db->executeSelectAll($sql);
        require_once 'Log/Entry.php';
        foreach ($record['entries'] as $index => $fields) {
            $entry = Log_Entry::load($fields);
            $record['entries'][$index] = $entry->getFields();
        }

        return $record;

    }
}

class log_time extends CMSObject{
    var $id;
    var $cdate;
    var $ip;
    var $method;
    var $url;
    var $gentime;
    var $host;

    function get_table_name() {
        return "log_time";
    }
}
