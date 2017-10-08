<?php

    define('HOME_CURRENCY_UNKNOWN', 0);
    define('HOME_CURRENCY_USD', 1);
    define('HOME_CURRENCY_PESOS', 2);

    define('HOME_FEATURES_COUNT', 6);

    /*define("CONF_PATH", realpath(dirname(__FILE__)."/../..").'/');*/

    Application::loadLibrary('olmi/stringUtils');
    Application::loadLibrary('olmi/redirect');

    define("ADMIN_EMAIL", "admin@".Application::getHost());

    function extract_ids(&$list) {
        $ids = array();
        foreach ($list as $item) {
            if (is_object($item)) {
                if (isset($item->id))
                    $ids[] = $item->id;
            } elseif (is_array($item)) {
                if (isset($item['id']))
                    $ids[] = $item['id'];
            }
        }

        return $ids;
    }

    function arrange_values(&$list, $param_name, $values) {

        foreach ($list as & $item) {
            if (is_object($item)) {
                if (isset($item->id)) {
                    $id = $item->id;
                    if (isset($values[$id]))
                        $item->$param_name = $values[$id];
                    else
                        $item->$param_name = NULL;
                }
            } elseif (is_array($item)) {
                if (isset($item['id'])) {
                    $id = $item['id'];
                    if (isset($values[$id]))
                        $item[$param_name] = $values[$id];
                    else
                        $item[$param_name] = NULL;
                }
            }

        }

    }

    function make_pass($password_length) {
        //return substr(md5(time()+rand(0,10000)),rand(0,32-$password_length),$password_length);
        return rand((int) pow(10, $password_length - 1), (int) pow(10, $password_length) - 1);
    }

    function checkValidDate($date, $format) {
        if ($format == 'mm/dd/yyyy') {
            if (preg_match("/^\d{2}\/\d{2}\/\d{4}$/", $date))
                return true;
        } elseif ($format == 'mm/dd/yy') {
            if (preg_match("/^\d{2}\/\d{2}\/\d{2}$/", $date, $m))
                return true;
        }
        return false;
    }

    function CheckValidateEmail($email) {
        $valid_address = true;

        if (!strstr($email, '@'))
            return false;
        list($user, $domain) = explode("@", $email);
        $valid_ip_form = '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}';
        $valid_email_pattern = '^[a-z0-9]+[a-z0-9_\.\'\-]*@[a-z0-9]+[a-z0-9\.\-]*\.(([a-z]{2,6})|([0-9]{1,3}))$';
        $space_check = '[ ]';

        if ((ereg('^["]', $user) && ereg('["]$', $user))) {
            $user = ereg_replace('^["]', '', $user);
            $user = ereg_replace('["]$', '', $user);
            $user = ereg_replace($space_check, '', $user); //spaces in quoted addresses OK per RFC (?)
            $email = $user."@".$domain; // contine with stripped quotes for remainder
        }

        if (strstr($domain, ' '))
            return false;

        if (ereg($valid_ip_form, $domain)) {
            $digit = explode(".", $domain);
            for ($i = 0; $i < 4; $i++) {
                if ($digit[$i] > 255) {
                    $valid_address = false;
                    return $valid_address;
                    exit;
                }
                // stop crafty people from using internal IP addresses
                if (($digit[0] == 192) || ($digit[0] == 10)) {
                    $valid_address = false;
                    return $valid_address;
                    exit;
                }
            }
        }

        if (!ereg($space_check, $email)) { // trap for spaces in
            if (eregi($valid_email_pattern, $email)) { // validate against valid email patterns
                $valid_address = true;
            } else {
                $valid_address = false;
                return $valid_address;
                exit;
            }
        }
        return $valid_address;
    }

    function CheckValidateURL($url) {
        $error = array();

        if (preg_match("/[^0-9a-z-_\.]+/i", $url)) {
            $error = array('Invalid URL! Allowed characters: "a-z", "0-9", "-", "."');
        }

        return $error;

    }

    function checkBadStrings($is_recursive_calling = false, $data = array()) {
        $patterns = "/(Content-Type:|MIME-Version:|Content-Transfer-Encoding:|bcc:|cc:|Subject:)/is";
        $data = $is_recursive_calling ? $data : $_POST;

        if (is_array($data)) {
            foreach ($data as $value)
                if (!checkBadStrings(true, $value))
                    return false;
        } elseif (preg_match($patterns, $data, $match)) {
            return false;
        }

        return true;
    }

    function createValidDate($year, $month, $day) {
        if (!$year)
            $date[] = "0000";
        else
            $date[] = $year;
        if (!$month || $month == '0')
            $date[] = "00";
        else
            $date[] = preg_replace("/^(\d?)$/", "0\\1", $month);
        if (!$day || $day == '0')
            $date[] = "00";
        else
            $date[] = preg_replace("/^(\d?)$/", "0\\1", $day);
        return join("-", $date);
    }

    function mailto($type, $email, $extra = array()) {
        $smarty = Application::getSmarty();

        Application::loadLibrary('olmi/MailSender');

        $msg = MailSender::createMessage();
        $emails = preg_split('/\s*,\s*/i', $email, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($emails as $e) {
            $msg->addTo(trim($e));
        }

        Application::loadObjectClass('blocks');
        $blocks = new blocks();

        $message = '';
        switch ($type) {
        case 'forgot':
            $subject = 'Kauai.com CMS Password';
            $smarty->assign('row', $extra);
            $message = $smarty->fetch('messages/get_password.tpl');
            $msg->setFrom(ADMIN_EMAIL);
            $msg->setReplyTo(ADMIN_EMAIL);
            break;

            /*case 'forgot_owner':
             $subject = 'Kauai.com Owner Password Recovery';
             $smarty->assign('row', $extra);
             $message = $smarty->fetch('messages/get_password_owner.tpl');
             $msg->setFrom(ADMIN_EMAIL);
             $msg->setReplyTo(ADMIN_EMAIL);
             break;

             case 'forgot_business_owner':
             $subject = 'Kauai.com Business Owner Password Recovery';
             $smarty->assign('row', $extra);
             $message = $smarty->fetch('messages/get_password_business_owner.tpl');
             $msg->setFrom(ADMIN_EMAIL);
             $msg->setReplyTo(ADMIN_EMAIL);
             break;*/

        case 'comment_to_business_owner':
            $subject = 'Kauai.com Business Request';
            $smarty->assign('row', $extra);

            require_once "inc/blocksclass.inc.php";
            $block = $blocks->getBlockToSite("Individual Bussiness page: email comment text");
            //$smarty->assign('comment_to_owners', $block->html_code);

            $message = $smarty->fetch('messages/comment_to_business_owner.tpl');
            $msg->setFrom($extra['email']);
            $msg->setReplyTo($extra['email']);
            break;

        case 'contact':
            $subject = $blocks->getTextBlockToSite('contact_us');
            //$subject = 'Information request from SayulitaLife.com';
            $smarty->assign('row', $extra);
            $message = $smarty->fetch('messages/contact.tpl');
            $msg->setFrom($extra['Email']);
            $msg->setReplyTo($extra['Email']);
            break;

        case 'contact_community':
            $subject = 'Kauai.com Community Request';
            $smarty->assign('row', $extra);
            $message = $smarty->fetch('messages/contact.tpl');
            $msg->setFrom($extra['Email']);
            $msg->setReplyTo($extra['Email']);
            break;

        case 'comment_add_business':
            $subject = $blocks->getTextBlockToSite('comment_add_business');
            //$subject = 'New Business signup.';
            $smarty->assign('row', $extra);
            $message = $smarty->fetch('messages/comment_add_business.tpl');
            $msg->setFrom(ADMIN_EMAIL);
            $msg->setReplyTo(ADMIN_EMAIL);

            break;

        case 'create_new_event':
            $subject = $blocks->getTextBlockToSite('eventcalendar_add');
            //$subject = 'SayulitaLife New Event Posted';
            $smarty->assign('row', $extra);
            $message = $smarty->fetch('messages/create_new_event.tpl');
            $msg->setFrom($extra['email']);
            $msg->setReplyTo($extra['email']);
            break;

        case 'create_new_classified':
            $subject = $blocks->getTextBlockToSite('classifieds_add');
            //$subject = 'SayulitaLife New Classified Posted';
            $smarty->assign('row', $extra);
            $message = $smarty->fetch('messages/create_new_classified.tpl');
            $msg->setFrom($extra['from_email']);
            $msg->setReplyTo($extra['from_email']);
            break;

        case 'faqs':
            $subject = $blocks->getTextBlockToSite('faqs');
            //$subject = 'SayulitaLife: FAQs about buying and selling real estate in Sayulita Mexico';
            $smarty->assign('row', $extra);
            $message = $smarty->fetch('messages/faqs.tpl');
            $msg->setFrom(ADMIN_EMAIL);
            $msg->setReplyTo(ADMIN_EMAIL);
            break;

        case 'realestate_add':
            $subject = $blocks->getTextBlockToSite('signup-realestate');
            //$subject = 'SayulitaLife: New Real Estate added';
            $smarty->assign('row', $extra);
            $message = $smarty->fetch('messages/realestate_add.tpl');
            $msg->setFrom(ADMIN_EMAIL);
            $msg->setReplyTo(ADMIN_EMAIL);
            break;

        case 'community_modify':
            $subject = 'Kauai.com Community';
            $smarty->assign('user', $extra['user']);
            $smarty->assign('community', $extra['community']);
            $message = $smarty->fetch('messages/community_modify.tpl');
            $msg->setFrom(ADMIN_EMAIL);
            $msg->setReplyTo(COMMUNITY_NOTIFICATION);
            break;

        case 'special_expires':
            $subject = $extra['subject'];
            $message = $extra['text'];
            $msg->setFrom(SPECIAL_EXPIRES_EMAIL, "Kauai.com Specials");
            $msg->setReplyTo(SPECIAL_EXPIRES_EMAIL, "Kauai.com Specials");
            break;

            /*case 'SIGNUP_HOME':
             $subject = $blocks->getTextBlockToSite('signup');
             $smarty->assign('home', $extra['home']);
             $smarty->assign('categories', $extra['categories']);
             $message = $smarty->fetch('messages/signup.tpl');
             $msg->setFrom($extra['from_email'], $extra['from_name']);
             $msg->setReplyTo($extra['from_email'], $extra['from_name']);
             break;*/

        case 'owner_contact_form':
            $subject = $extra['subject_name'].' Service Request';
            $smarty->assign('fields', $extra);
            $message = $smarty->fetch('messages/owner_contact_form.tpl');
            $msg->setFrom($extra['email']);
            $msg->setReplyTo($extra['email']);
            break;
        }

        $msg->setSubject($subject);
        $msg->setBody($message, "text/html", "iso-8859-1", "8bit");
        MailSender::send($msg);

        return $message;
    }

    function check($text) {
        $dbEngine = Application::getDbEngine();
        return $dbEngine->prepareValue($text);
    }

    function FCK_Config(&$fckInstance) {
        $fckInstance->BasePath = '/core/libraries/fck/';
        $fckInstance->Config['AutoDetectLanguage'] = false;
        $fckInstance->Config['DefaultLanguage'] = 'en';
        $fckInstance->Height = 400;
        $fckInstance->ToolbarSet = "Full";
    }

    function website_address_valid($url) {
        $regexp = "/(?P<protocol>(?:(?:f|ht)tp|https):\/\/)?
                  (?P<domain>(?:(?!-)
                  (?P<sld>[a-zA-Z\d\-]+)(?<!-)
                  [\.]){1,2}
                  (?P<tld>(?:[a-zA-Z]{2,}\.?){1,}){1,}
                  |
                  (?P<ip>(?:(?(?<!\/)\.)(?:25[0-5]|2[0-4]\d|[01]?\d?\d)){4})
                  )
                  (?::(?P<port>\d{2,5}))?
                  (?:\/
                  (?P<script>[~a-zA-Z\/.0-9-_]*)?
                  (?:\?(?P<parameters>[=a-zA-Z+%&0-9,.\/_ -]*))?
                  )?
                  (?:\#(?P<anchor>[=a-zA-Z+%&0-9._]*))?/x";

        return preg_match($regexp, $url, $m);
    }

    function email_valid($email) {
        $pockets = array();
        preg_match('/([0-9a-zA-Z])([0-9a-zA-Z_\.-]*)@([0-9a-zA-Z])([0-9a-z\.-]*)\.([a-zA-Z]+)/is', $email, $pockets);
        if ($email != @$pockets[0])
            return false;
        return true;
    }

    function add_protocol($link) {
        if (!$link)
            return "";
        if (substr($link, 0, 7) != 'http://' && substr($link, 0, 8) != 'https://') {
            return "http://".$link;
        } else {
            return $link;
        }
    }

    function setURLParam($url, $name, $value) {
        $pieces = parse_url($url);

        $url = '';

        $params = array();
        if (isset($pieces['query'])) {
            parse_str($pieces['query'], $params);
        }
        $params[$name] = $value;

        unset($pieces['query']);
        $p = array();
        foreach ($params as $k => $v) {
            $p[] = urldecode($k).'='.urldecode($v);
        }
        unset($params);

        if (count($p) > 0) {
            $pieces['query'] = implode('&', $p);
        }

        $url .= isset($pieces['scheme']) ? "{$pieces['scheme']}://" : '';
        $url .= isset($pieces['user']) ? $pieces['user'] : '';
        $url .= isset($pieces['pass']) ? ":{$pieces['pass']}" : '';
        $url .= isset($pieces['user']) ? '@' : '';
        $url .= isset($pieces['host']) ? $pieces['host'] : '';
        $url .= isset($pieces['path']) ? $pieces['path'] : '';
        $url .= isset($pieces['query']) ? "?{$pieces['query']}" : '';
        $url .= isset($pieces['fragment']) ? "#{$pieces['fragment']}" : '';

        return $url;
    }

    function writeLogTime($starttime, $stoptime) {
        $dbEngine = Application::getDbEngine();
        $db = Application::getDb();
        Application::loadLibrary('log/Log');

        $now = date("Y-m-d");

        $sql = "
            DELETE FROM log_time
            WHERE cdate <= DATE_SUB('$now', INTERVAL 1 WEEK)
        ";

        $db->execute($sql);

        $log_time = new log_time();
        $log_time->cdate = date('Y-m-d H:i:s');
        $log_time->ip = $_SERVER['REMOTE_ADDR'];
        $log_time->method = $_SERVER['REQUEST_METHOD'];
        $log_time->url = $_SERVER['REQUEST_URI'];
        $log_time->host = $_SERVER['HTTP_HOST'];
        $log_time->gentime = round($stoptime - $starttime, 4);

        $dbEngine->insertObject($log_time, $log_time->get_table_name());

    }

    function get_empty_select($add_null_item = false) {
        $select = array();

        if ($add_null_item) {
            if (is_string($add_null_item)) {
                $select[0] = $add_null_item;
            } elseif (is_array($add_null_item)) {
                $keys = array_keys($add_null_item);
                if (count($add_null_item) == 1) {
                    $select[$keys[0]] = $add_null_item[$keys[0]];
                } else {
                    $select[$add_null_item[$keys[0]]] = $add_null_item[$keys[1]];
                }
            } else {
                $select[0] = "-- Выберите --";
            }
        }

        return $select;
    }

    function encodeJson($data) {
        if (is_object($data) or is_array($data)) {
            $out = array();
            foreach ($data as $k => $v)
                $out[] = "'{$k}':".encodeJson($v);
            $out = implode(',', $out);
            return '{'.$out.'}';
        } elseif (is_null($data))
            return 'null';
        else {
            $data = str_replace(array("\n", "\r"), '', $data);
            $data = str_replace("'", "\'", $data);
            return "'{$data}'";
        }
    }

    function load_review_switches(&$objects, $object_table) {
        global $db;
        if (!$objects)
            return;
        $ids = array();
        foreach ($objects as $obj)
            $ids[] = $obj->id;
        $ids = implode(',', $ids);
        $sql = "
            SELECT object_id, enabled FROM review_enable
            WHERE object_table='$object_table' AND object_id IN($ids)
        ";

        $data = $db->executeSelectAllObjects($sql);
        $enabled = array();
        foreach ($data as $d)
            $enabled[$d->object_id] = $d->enabled;

        foreach ($objects as & $obj) {
            if (isset($enabled[$obj->id]))
                $obj->reviews_enabled = $enabled[$obj->id];
            else
                $obj->reviews_enabled = true;
        }

    }

    function toggle_review_switches($object, $object_table) {
        global $db;
        if (!$object)
            return;
        $object_id = (int) $object->id;
        $old_value = $db->executeScalar("
            SELECT enabled FROM review_enable
            WHERE object_table='$object_table' AND object_id=$object_id
        ");
        $old_value = ($old_value === '0') ? 0 : 1;
        $new_value = $old_value ? 0 : 1;
        $db->execute("
            REPLACE INTO review_enable (object_table, object_id, enabled)
            VALUES ('$object_table', $object_id, $new_value)
        ");

    }
    
    
	function encode_header_utf_8($str) {
		if (!$str) return "";
	    return '=?utf-8?B?'.base64_encode($str).'?=';
	}
    

