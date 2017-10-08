<?php
        
    class UserSession {
    	static $user_id;
    	static $user_account;    	
    	static $live;    	
    	var $auto_save;
    	
    	function getSessionKey() {
    		return Application::getApplicationName() . "UserSession"; 
    	}
    	
    	function __construct() {    		    		
    		if (self::$live) {    			
    			$this->auto_save = 0;
    			return;
    		}
    		$this->auto_save = 1;
    		
    		self::$live = 1;
    		self::$user_id = null;
    		self::$user_account = null;    		
    		    		
    		self::$user_id = (int)@$_SESSION[$this->getSessionKey()];
    		 
    		if (!self::$user_id) return;
    		
    		
    		$user = Application::getObjectInstance('user');
    		self::$user_account = $user->load(self::$user_id);

    		if (!self::$user_account) {
    			self::$user_id = null;
    			return;
    		}
    		
    		if (!self::$user_account->active) {
    			self::$user_id = null;
    			return;
    		}
    		
    	}
    	
    	function __destruct() {    		
    		if (!$this->auto_save) return;    		
    		$_SESSION[$this->getSessionKey()] = self::$user_id;    		
    	}
    	
    	function getSerializableFields() {
    		return array("user_id");
    	}
    	
    	function __sleep() {
    		return $this->getSerializableFields();
    	}
    	
    	function auth($login, $pass) {
    		self::$user_id = null;
    		self::$user_account = null;
    		    		
    		$user = Application::getObjectInstance('user');
    		$table = $user->get_table_name();
    		$db = Application::getDb();
    		    		
    		$login = addslashes($login);
    		$pass = addslashes($pass);
    		
    		$user_id = $db->executeScalar("
    			SELECT id
    			FROM $table
    			WHERE login='$login' AND pass='$pass' AND active=1    			
    		");
    		
    		if (!$user_id) return false;
    		    		    		
    		self::$user_account = $user->load($user_id);
    		self::$user_id = $user_id;
    		$_SESSION[$this->getSessionKey()] = self::$user_id;
    		return true; 
    	}
    	
/*    	function forceLogin($user_id) {
    		$account = new mypmsUserAccount();
    		$account = $account->load($user_id);
    		
    		if (!$account) return false;
    		if ($account->block) return false;
    		
    		self::$user_account = $user_account;
    		self::$user_id = $user_id;
    		$_SESSION[$this->getSessionKey()] = self::$user_id;
    		return true;
    	}
    	
    	function checkPassword($pass) {
    		$user_account = $this->getUserAccount();
    		if (!$user_account) return false;
    		
    		$credentials = array(
    		    'username' => $user_account->email, 
    		    'password' => $pass
    		);
    		
		    jimport( 'joomla.user.authentication');
		    $authenticate = &JAuthentication::getInstance();
		    $response = $authenticate->authenticate($credentials, array());		    
		        		
		    if ($response->status !== JAUTHENTICATE_STATUS_SUCCESS) return false;
		    return true;    		
    	}*/
    	
   	
    	function logout() {
    		unset($_SESSION[$this->getSessionKey()]);
    		self::$user_id = 0;
    		self::$user_account = null;
    	}
    	
    	function userLogged() {
    		return is_object(self::$user_account);
    	}
    	
        function getUserAccount() {
        	return self::$user_account;
        }

        function getUserID() {
        	return self::$user_id;
        }
        
    	/*function register_ownership($object_type, $object_id, $user_id=null) {    		
    		if (!$user_id) $user_id = $this->getUserID();
    		if (!$user_id) return false;
    		
    		$object_type = addslashes($object_type);
    		$object_id = (int)$object_id;
    		
    		$db = JFactory::getDBO();
    		$db->execute("
    			REPLACE INTO #__mypms_owned_objects VALUES(
    				$user_id, $object_id, '$object_type'
    			)
    		");
    			
    		return true;
    		
    	}*/
    	    	
    	 
    	
    	
    	
    	
    }