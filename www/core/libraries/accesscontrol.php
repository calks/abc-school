<?php

    Application::loadLibrary('olmi/class');
    Application::loadObjectClass('home/owner');
    Application::loadObjectClass('business/owner');

    function is_auth_user($user, $table='', $destroy_session=true)
    {
        Application::loadLibrary('Log/Log');

        if (!$table) return false;

        $user_id = (int)$user;
        $table = addslashes($table);

        $db = Application::getDb();

        $user = $db->executeSelectObject("
            SELECT * FROM $table WHERE id=$user_id
        ");

        if (!$user) {
            if ($destroy_session) session_destroy();
            return false;
        }

        if ($table != 'cms_users') {
            return true;
        } elseif($user->is_admin < 1) {
            return false;
        }

        $_SESSION['auth_user_admin'] = $user;
        $_SESSION['auth_admin_type'] = $user->is_admin;

        $type = isset($_SESSION['auth_admin_type'])
                    ? intval($_SESSION['auth_admin_type'])
                    : 0;
        $type = in_array($type, array(Log::USER_TYPE_OWNER, Log::USER_TYPE_MANAGER)) ? USER_ADMIN : $type;


        switch ($type)
        {
            case USER_HIRED_ADMIN:
            case USER_ADMIN:
                return true;

            case USER_CALENDAR_EDITOR:
                return in_array($_SERVER['PHP_SELF'],
                        array(
                            "/admin/index.php",
                            "/admin/eventscalendar.php"
                        ));
                break;

            case USER_COMMUNITY:
                return in_array($_SERVER['PHP_SELF'],
                        array(
                            "/admin/index.php",
                            "/admin/community.php"
                        ));
                break;

            case USER_SIR_EMPLOYEE:
                return in_array($_SERVER['PHP_SELF'],
                        array(
                            "/admin/index.php",
                            "/admin/realestate.php",
                            "/admin/photo.php",
                            "/admin/realestate_banner.php",
                            "/admin/map.php"
                        ));
                break;

            default:
                return false;
        }
    }

    function dispatch_user()
    {

        $type = isset($_SESSION['auth_admin_type'])
                    ? intval($_SESSION['auth_admin_type'])
                    : 0;
        switch ($type)
        {
            case USER_HIRED_ADMIN:
            case USER_ADMIN:
                //die('dispatch_user');
                Redirector::redirect("homes.php");
                break;

            case USER_CALENDAR_EDITOR:
                Redirector::redirect("eventscalendar.php");
                break;

            case USER_COMMUNITY:
                Redirector::redirect("community.php");
                break;

            case USER_SIR_EMPLOYEE:
                Redirector::redirect("realestate.php");
                break;

            default:
                Redirector::redirect("homes.php");
                return false;
        }
    }


    function need_register() {
        Redirector::redirect("/admin/login.php");
    }

    function need_register_owner() {
        Redirector::redirect("/rentalowner/login");
    }

    function need_register_businessowner() {
        Redirector::redirect("/businessowner/login");
    }

    function not_need_register() {
        Redirector::redirect("/admin/index.php");
    }

    function not_need_register_owner() {
        Redirector::redirect("/rentalowner/index.php");
    }

    function not_need_register_businessowner() {
        Redirector::redirect("/businessowner/index.php");
    }



