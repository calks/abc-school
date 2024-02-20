<?php

	define('APP_COMPONENT_TYPE_BLOCK', 'block');
	define('APP_COMPONENT_TYPE_FILTER', 'filter');
	define('APP_COMPONENT_TYPE_LIBRARY', 'library');
	define('APP_COMPONENT_TYPE_MODULE', 'module');
	define('APP_COMPONENT_TYPE_OBJECT', 'object');

	class Application {
		private static $application_name;
		private static $site_path;
		private static $site_url;
		private static $host;
		private static $config;		
		private static $db;
		private static $dbEngine;
		private static $smarty;
		private static $breadcrumbs;
		private static $context;
		private static $page;
		private static $user_session;

		private static $loaded;
		private static $className;

		private static $mobile;

		public static function init($application_name) {
			
			$config_path = self::$site_path."/conf/$application_name.php";
			include_once $config_path;
			self::$config = $config;
			
			
			self::$application_name = $application_name;
			self::$site_path = realpath(dirname(__FILE__)."/../../..");


			if (isset($_SERVER['HTTP_HOST'])) {
				self::$host = @strtolower($_SERVER['HTTP_HOST']);
			} else {
				self::$host = "";
			}

			self::$site_url = SITE_PROTOCOL . '://'.self::$host;

			self::$db = null;
			self::$breadcrumbs = null;

			self::$loaded = array(
				'modules' => array(),
				'blocks' => array(),
				'libraries' => array(),
				'objects' => array(),
				'filters' => array()
			);

			self::$className = array(
				APP_COMPONENT_TYPE_BLOCK => array(),
				APP_COMPONENT_TYPE_FILTER => array(),
				APP_COMPONENT_TYPE_LIBRARY => array(),
				APP_COMPONENT_TYPE_MODULE => array(),
				APP_COMPONENT_TYPE_OBJECT => array()
			);

			self::$mobile = (int) ((bool) (self::detectMobileBrowser()));


			session_start();
		}

		public static function isMobile() {
			return self::$mobile;
		}

		public static function getModulePath($module_name) {
			return self::$site_path.'/core/modules/'.$module_name.'/'.$module_name.'.php';
		}

		public static function getAppSpecificModulePath($module_name) {
			return self::$site_path.'/applications/'.self::getApplicationName().'/modules/'.$module_name.'/'.$module_name.'.php';
		}

		public static function getModuleUrl($module_name) {
			$site_path = self::getSitePath();
			$application_name = self::getApplicationName();

			$application_path = self::getAppSpecificModulePath($module_name);
			$core_path = self::getModulePath($module_name);

			if (is_file($application_path)) {
				return self::$site_url."/applications/$application_name/modules/$module_name";
			} else {
				return self::$site_url."/core/modules/$module_name";
			}
		}

		public static function includeCoreModuleClass($module_name) {
			$path = self::getModulePath($module_name);
			include_once $path;
		}

		public static function loadModule($module_name, $core_only = false) {
			if (in_array($module_name, self::$loaded['modules'])) return true;

			$application_name = self::getApplicationName();
			$site_path = self::getSitePath();

			$application_path = self::getAppSpecificModulePath($module_name);
			$core_path = self::getModulePath($module_name);

			if (is_file($application_path) && !$core_only) {
				include_once($application_path);
				self::$loaded['modules'][] = $module_name;
				return true;
			} elseif (is_file($core_path)) {
				include_once($core_path);
				self::$loaded['modules'][] = $module_name;
				return true;
			}

			return false;
		}

		public static function loadParentModule($module_name) {
			self::loadModule($module_name, true);
		}

		public static function getLibraryPath($library_name) {
			$library_name = explode('/', $library_name);
			$path = self::$site_path.'/core/libraries';
			foreach ($library_name as $fragment) $path .= '/'.$fragment;
			$path .= '.php';
			return $path;
		}

		public static function getAppSpecificLibraryPath($library_name) {
			$library_name = explode('/', $library_name);
			$path = self::$site_path.'/applications/'.self::getApplicationName().'/libraries';
			foreach ($library_name as $fragment) $path .= '/'.$fragment;
			$path .= '.php';
			return $path;
		}

		public static function loadLibrary($library_name) {
			$path = self::getLibraryPath($library_name);
			$app_path = self::getAppSpecificLibraryPath($library_name);
			if (is_file($app_path)) include_once($app_path);
			elseif (is_file($path)) include_once($path);
			else return false;
			return true;
		}

		protected static function getCallerInfo() {
			$backtrace = debug_backtrace();
			if (!isset($backtrace[1])) return '';
			$file = $backtrace[1]['file'];
			$line = $backtrace[1]['line'];
			return "Called from $file (line $line).";
		}

		public static function runModule($moduleName, $params = array()) {
			$className = application::getClassName(APP_COMPONENT_TYPE_MODULE, $moduleName);

			if ($className) {
				if (USE_PROFILER) {
					$profiler = new profiler("Module $moduleName");
					$profiler->start();
				}
				$module = new $className();
				$out = call_user_func(array($module, 'run'), $params);
				if (USE_PROFILER) $profiler->stop();
				return $out;
			} else die("Module $moduleName not found. ".self::getCallerInfo());
		}

		public static function getBlockPath($block_name) {
			return self::$site_path.'/core/blocks/'.$block_name.'/'.$block_name.'.php';
		}

		public static function getAppSpecificBlockPath($block_name) {
			return self::$site_path.'/applications/'.self::getApplicationName().'/blocks/'.$block_name.'/'.$block_name.'.php';
		}

		public static function getBlockUrl($block_name) {
			$site_path = self::getSitePath();
			$application_name = self::getApplicationName();

			$application_path = self::getAppSpecificBlockPath($block_name);
			$core_path = self::getBlockPath($block_name);

			if (is_file($application_path)) {
				return self::$site_url."/applications/$application_name/blocks/$block_name";
			} else {
				return self::$site_url."/core/blocks/$block_name";
			}
		}

		public static function includeCoreBlockClass($block_name) {
			$path = self::getBlockPath($block_name);
			include_once $path;
		}

		public static function loadBlock($block_name, $core_only = false) {
			if (in_array($block_name, self::$loaded['blocks'])) return true;

			$application_name = self::getApplicationName();
			$site_path = self::getSitePath();

			$application_path = self::getAppSpecificBlockPath($block_name);
			$core_path = self::getBlockPath($block_name);

			if (is_file($application_path) && !$core_only) {
				include_once($application_path);
				self::$loaded['blocks'][] = $block_name;
				return true;
			} elseif (is_file($core_path)) {
				include_once($core_path);
				self::$loaded['blocks'][] = $block_name;
				return true;
			}

			return false;
		}

		public static function loadParentBlock($block_name) {
			self::loadBlock($block_name, true);
		}

		public static function getBlockContent($block_name, $params = array()) {

			$className = self::getClassName(APP_COMPONENT_TYPE_BLOCK, $block_name);
			if ($className) {
				if (USE_PROFILER) {
					$profiler = new profiler("Block $block_name");
					$profiler->start();
				}

				$block = new $className();
				$out = call_user_func(array($block, 'run'), $params);
				if (USE_PROFILER) $profiler->stop();
				return $out;
			} else die("Class for block $block_name is not defined. ".self::getCallerInfo());
		}

		public static function getDb() {
			if (!self::$db) {
				self::loadLibrary('olmi/mysql');
				self::$db = new MySqlDatabase(self::$config['database']['host'], self::$config['database']['name'], self::$config['database']['user'], self::$config['database']['pass']);
				self::$db->execute("set names utf8");				
			}
			return self::$db;
		}

		public static function getDbEngine() {
			if (!self::$dbEngine) {
				self::loadLibrary('olmi/basedbengine');
				self::$dbEngine = new BaseDbEngine(self::getDb());
			}
			return self::$dbEngine;
		}

		public static function getPage() {
			if (!self::$page) {
				self::loadLibrary('core/page');
				self::$page = Page::getInstance();
			}

			return self::$page;
		}
		
		public static function getUserSession() {
			if (!self::$user_session) {
				self::loadLibrary('core/user_session');
				self::$user_session = new UserSession();
			}

			return self::$user_session;
		}
		

		public static function getObjectPath($object_name) {
			return self::$site_path.'/core/objects/'.$object_name.'.php';
		}

		public static function loadObjectClass($object_name, $core_only = false) {
			if (in_array($object_name, self::$loaded['objects'])) return true;

			$application_name = self::getApplicationName();
			$site_path = self::getSitePath();

			$application_path = "$site_path/applications/$application_name/objects/$object_name.php";
			$application_path2 = "$site_path/applications/$application_name/objects/$object_name/$object_name.php";
			$core_path = "$site_path/core/objects/$object_name.php";
			$core_path2 = "$site_path/core/objects/$object_name/$object_name.php";

			if (is_file($application_path) && !$core_only) {
				include_once($application_path);
				self::$loaded['objects'][] = $object_name;
				return true;
			} elseif (is_file($application_path2) && !$core_only) {
				include_once($application_path2);
				self::$loaded['object'][] = $object_name;
				return true;
			} elseif (is_file($core_path)) {
				include_once($core_path);
				self::$loaded['object'][] = $object_name;
				return true;
			} elseif (is_file($core_path2)) {
				include_once($core_path2);
				self::$loaded['object'][] = $object_name;
				return true;
			}

			die("Can't find class file $object_name. ".self::getCallerInfo());

		}

		public static function loadParentObjectClass($object_name) {
			self::loadObjectClass($object_name, true);
		}

		public static function getEntityInstance($objectName) {
			return self::getObjectInstance($objectName);
		}
		
		public static function getObjectInstance($objectName) {
			$className = self::getClassName(APP_COMPONENT_TYPE_OBJECT, $objectName);
			if ($className) return new $className();
			else die("Can't instantiate object {$objectName}" );
		}

		public static function getSmarty() {
			if (!self::$smarty) {

				self::loadLibrary('smarty/Smarty.Class');

				self::$smarty = new Smarty();

				self::$smarty->template_dir = self::$site_path.self::$config['templating']['template_dir'];
				self::$smarty->compile_dir = self::$site_path.self::$config['templating']['compile_dir'];
				self::$smarty->config_dir = self::$site_path.self::$config['templating']['config_dir'];
				self::$smarty->cache_dir = self::$site_path.self::$config['templating']['cache_dir'];

				self::$smarty->caching = false;
			}
			return self::$smarty;
		}

		public static function getBreadcrumbs() {
			if (!self::$breadcrumbs) {
				self::loadLibrary('core/breadcrumbs');
				self::$breadcrumbs = new Breadcrumbs();
				self::$breadcrumbs->addNode('/', 'Home');
			}
			return self::$breadcrumbs;
		}

		public static function getHost() {
			return self::$host;
		}

		public static function getSitePath() {
			return self::$site_path;
		}

		public static function getSiteUrl() {
			return self::$site_url;
		}

		public static function getApplicationName() {
			return self::$application_name;
		}

		public static function getSeoUrl($internal_url) {
			return UrlRewriter::internalToSeo($internal_url);
		}

		public static function getContextVar($name, $default = null) {
			if (!isset(self::$context[$name])) return $default;
			return self::$context[$name];
		}

		public static function setContextVar($name, $value) {
			self::$context[$name] = $value;
		}

		public static function getSiteName() {
			return 'Kauai.com';
		}

		public static function getApplicationDir() {
			return self::getSitePath()."/applications/".self::getApplicationName();
		}

		public static function getApplicationUrl() {
			return self::getSiteUrl()."/applications/".self::getApplicationName();
		}

		public static function loadParentFilter($filterName) {
			self::loadFilter($filterName, true);
		}

		public static function loadFilter($filterName, $core_only = false) {
			if (in_array($filterName, self::$loaded['filters'])) return true;

			$applicationName = self::getApplicationName();
			$sitePath = self::getSitePath();

			$applicationPath = "{$sitePath}/applications/{$applicationName}/filters/{$filterName}.php";
			$corePath1 = "{$sitePath}/core/filters/{$filterName}.php";
			$corePath2 = "{$sitePath}/core/filters/{$filterName}/{$filterName}.php";

			if (is_file($applicationPath) && !$core_only) {
				include_once($applicationPath);
				self::$loaded['filters'][] = $filterName;
				return true;
			} elseif (is_file($corePath1)) {
				include_once($corePath1);
				self::$loaded['filters'][] = $filterName;
				return true;
			} elseif (is_file($corePath2)) {
				include_once($corePath2);
				self::$loaded['filters'][] = $filterName;
				return true;
			}

			die("Can't find filter $filterName. ".self::getCallerInfo());
		}

		public static function getFilter($filterName, array $params = array()) {
			$className = self::getClassName(APP_COMPONENT_TYPE_FILTER, $filterName);

			if ($className) return new $className($params);
			else die("Can't create instance of '{$filterName}' filter" );
		}

		public static function getClassName($componentType, $componentName) {
			if (isset(self::$className[$componentType][$componentName])) return self::$className[$componentType][$componentName];

			$appName = strtolower(self::getApplicationName());
			switch ($componentType) {
			case APP_COMPONENT_TYPE_BLOCK:
				$className = self::getBlockClassName($componentName);
				break;
			case APP_COMPONENT_TYPE_FILTER:
				$className = self::getFilterClassName($componentName);
				break;
			case APP_COMPONENT_TYPE_LIBRARY:
				return false;
			case APP_COMPONENT_TYPE_MODULE:
				$className = self::getModuleClassName($componentName);
				break;
			case APP_COMPONENT_TYPE_OBJECT:
				$className = self::getObjectClassName($componentName);
				break;
			default:
				return false;
			}

			self::$className[$componentType][$componentName] = $className;
			return $className;
		}

		protected static function getBlockClassName($blockName) {
			self::LoadBlock($blockName);

			$blockName = str_replace(' ', '', ucwords(str_replace('_', ' ', $blockName)));			
			$classNameCore = $blockName.'Block';			
			$classNameApp = strtolower(self::getApplicationName()).$classNameCore;

			return class_exists($classNameApp) ? $classNameApp : (class_exists($classNameCore) ? $classNameCore : false);
		}

		protected static function getFilterClassName($filterName) {
			self::loadFilter($filterName);

			$filterName = str_replace(' ', '', ucwords(str_replace('_', ' ', $filterName)));

			$classNameCore = $filterName.'Filter';
			$classNameApp = strtolower(self::getApplicationName()).$classNameCore;

			return class_exists($classNameApp) ? $classNameApp : (class_exists($classNameCore) ? $classNameCore : false);
		}

		protected static function getModuleClassName($moduleName) {
			self::loadModule($moduleName);
			
			$moduleName = str_replace(' ', '', ucwords(str_replace('_', ' ', $moduleName)));

			$classNameCore = $moduleName.'Module';
			$classNameApp = strtolower(self::getApplicationName()).$classNameCore;

			return class_exists($classNameApp) ? $classNameApp : (class_exists($classNameCore) ? $classNameCore : false);
		}

		protected static function getObjectClassName($objectName) {
			self::loadObjectClass($objectName);
			$appName = strtolower(self::getApplicationName());

			$classPrefixes = array($appName, "{$appName}_", '');
			$objectNameParts = explode('/', $objectName);
			$lastPart = $objectNameParts[count($objectNameParts) - 1];

			$names = array();
			foreach ($classPrefixes as $prefix) {
				if (count($objectNameParts) > 1) {
					// Try "class/name" => "ClassName"
					$names[] = $prefix.str_replace(' ', '', ucwords(implode(' ', $objectNameParts)));
					// Try "class/name" => "class_name"
					$names[] = $prefix.strtolower(implode('_', $objectNameParts));
				}
				// Try "part1/part2/.../partN/name" ( or simply "name" ) => "Name"
				$names[] = $prefix.ucfirst($lastPart);
			}

			foreach ($names as $className) if (class_exists($className)) return $className;

			return false;
		}

		protected static function detectMobileBrowser() {
			$user_agent = strtolower(getenv('HTTP_USER_AGENT'));
			$accept = strtolower(getenv('HTTP_ACCEPT'));

			if ((strpos($accept, 'text/vnd.wap.wml') !== false) || (strpos($accept, 'application/vnd.wap.xhtml+xml') !== false)) {
				return 1; // ��������� ������� ��������� �� HTTP-����������
			}

			if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
				return 2; // ��������� ������� ��������� �� ���������� �������
			}

			if (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|'.'wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|'.'lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|'.'mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|'.'m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|'.'r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|'.'i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|'.'htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|'.'sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|'.'p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|'.'_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|'.'s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|'.'d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |'.'sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|'.'up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|'.'pocket|kindle|mobile|psp|treo)/', $user_agent)) {
				return 3; // ��������� ������� ��������� �� ��������� User Agent
			}

			if (in_array(substr($user_agent, 0, 4), Array("1207", "3gso", "4thp", "501i", "502i", "503i", "504i", "505i", "506i",
				"6310", "6590", "770s", "802s", "a wa", "abac", "acer", "acoo", "acs-",
				"aiko", "airn", "alav", "alca", "alco", "amoi", "anex", "anny", "anyw",
				"aptu", "arch", "argo", "aste", "asus", "attw", "au-m", "audi", "aur ",
				"aus ", "avan", "beck", "bell", "benq", "bilb", "bird", "blac", "blaz",
				"brew", "brvw", "bumb", "bw-n", "bw-u", "c55/", "capi", "ccwa", "cdm-",
				"cell", "chtm", "cldc", "cmd-", "cond", "craw", "dait", "dall", "dang",
				"dbte", "dc-s", "devi", "dica", "dmob", "doco", "dopo", "ds-d", "ds12",
				"el49", "elai", "eml2", "emul", "eric", "erk0", "esl8", "ez40", "ez60",
				"ez70", "ezos", "ezwa", "ezze", "fake", "fetc", "fly-", "fly_", "g-mo",
				"g1 u", "g560", "gene", "gf-5", "go.w", "good", "grad", "grun", "haie",
				"hcit", "hd-m", "hd-p", "hd-t", "hei-", "hiba", "hipt", "hita", "hp i",
				"hpip", "hs-c", "htc ", "htc-", "htc_", "htca", "htcg", "htcp", "htcs",
				"htct", "http", "huaw", "hutc", "i-20", "i-go", "i-ma", "i230", "iac",
				"iac-", "iac/", "ibro", "idea", "ig01", "ikom", "im1k", "inno", "ipaq",
				"iris", "jata", "java", "jbro", "jemu", "jigs", "kddi", "keji", "kgt",
				"kgt/", "klon", "kpt ", "kwc-", "kyoc", "kyok", "leno", "lexi", "lg g",
				"lg-a", "lg-b", "lg-c", "lg-d", "lg-f", "lg-g", "lg-k", "lg-l", "lg-m",
				"lg-o", "lg-p", "lg-s", "lg-t", "lg-u", "lg-w", "lg/k", "lg/l", "lg/u",
				"lg50", "lg54", "lge-", "lge/", "libw", "lynx", "m-cr", "m1-w", "m3ga",
				"m50/", "mate", "maui", "maxo", "mc01", "mc21", "mcca", "medi", "merc",
				"meri", "midp", "mio8", "mioa", "mits", "mmef", "mo01", "mo02", "mobi",
				"mode", "modo", "mot ", "mot-", "moto", "motv", "mozz", "mt50", "mtp1",
				"mtv ", "mwbp", "mywa", "n100", "n101", "n102", "n202", "n203", "n300",
				"n302", "n500", "n502", "n505", "n700", "n701", "n710", "nec-", "nem-",
				"neon", "netf", "newg", "newt", "nok6", "noki", "nzph", "o2 x", "o2-x",
				"o2im", "opti", "opwv", "oran", "owg1", "p800", "palm", "pana", "pand",
				"pant", "pdxg", "pg-1", "pg-2", "pg-3", "pg-6", "pg-8", "pg-c", "pg13",
				"phil", "pire", "play", "pluc", "pn-2", "pock", "port", "pose", "prox",
				"psio", "pt-g", "qa-a", "qc-2", "qc-3", "qc-5", "qc-7", "qc07", "qc12",
				"qc21", "qc32", "qc60", "qci-", "qtek", "qwap", "r380", "r600", "raks",
				"rim9", "rove", "rozo", "s55/", "sage", "sama", "samm", "sams", "sany",
				"sava", "sc01", "sch-", "scoo", "scp-", "sdk/", "se47", "sec-", "sec0",
				"sec1", "semc", "send", "seri", "sgh-", "shar", "sie-", "siem", "sk-0",
				"sl45", "slid", "smal", "smar", "smb3", "smit", "smt5", "soft", "sony",
				"sp01", "sph-", "spv ", "spv-", "sy01", "symb", "t-mo", "t218", "t250",
				"t600", "t610", "t618", "tagt", "talk", "tcl-", "tdg-", "teli", "telm",
				"tim-", "topl", "tosh", "treo", "ts70", "tsm-", "tsm3", "tsm5", "tx-9",
				"up.b", "upg1", "upsi", "utst", "v400", "v750", "veri", "virg", "vite",
				"vk-v", "vk40", "vk50", "vk52", "vk53", "vm40", "voda", "vulc", "vx52",
				"vx53", "vx60", "vx61", "vx70", "vx80", "vx81", "vx83", "vx85", "vx98",
				"w3c ", "w3c-", "wap-", "wapa", "wapi", "wapj", "wapm", "wapp", "wapr",
				"waps", "wapt", "wapu", "wapv", "wapy", "webc", "whit", "wig ", "winc",
				"winw", "wmlb", "wonu", "x700", "xda-", "xda2", "xdag", "yas-", "your",
				"zeto", "zte-"))) {
				return 4; // ��������� ������� ��������� �� ��������� User Agent
			}

			return 0; // ��������� ������� �� ���������
		}

	}
