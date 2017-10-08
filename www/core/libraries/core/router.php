<?php

	Application::loadLibrary('seo/rewrite');

	class Router {

		private static $source_url;
		private static $rewritten_url;
		private static $module_name;
		private static $module_params;
		private static $request_params;
		private static $defaultModuleName = 'textpage';

		public function route($url) {
			self::$source_url = $url;
			self::$rewritten_url = UrlRewriter::seoToInternal($url);
			self::$module_params = array();
			self::$request_params = array();
			self::$module_name = self::$defaultModuleName;

			$url = new URL(self::$rewritten_url);
			$address = $url->getAddress();

			
			if (!$url_parts = explode('/', $address)) {
				return;
			}
			
			$module_name = array_shift($url_parts);
			
			$module_path = Application::getModulePath($module_name);
			$app_specific_module_path = Application::getAppSpecificModulePath($module_name);

			if (is_file($app_specific_module_path)) {
				self::$module_name = $module_name;
			} elseif (is_file($module_path)) {
				self::$module_name = $module_name;
			} else {
				array_unshift($url_parts, $module_name);
			}

			//self::$module_params = array_merge($url_parts, $url->getGetParams());
			self::$module_params = $url_parts;

			//foreach ($_POST as $key => $value) self::$module_params[$key] = $value;
		}

		public function getModuleName() {
			return self::$module_name;
		}

		public function setModuleName($module_name) {
			$module_path = Application::getModulePath($module_name);
			$app_specific_module_path = Application::getAppSpecificModulePath($module_name);

			if (is_file($app_specific_module_path) || is_file($module_path)) {
				self::$module_name = $module_name;
			}
		}

		public function getModuleParams() {
			return self::$module_params;
		}

		public function getRequestParams() {
			return self::$request_params;
		}

		public function getSourceUrl() {
			return self::$source_url;
		}

		public function getRewrittenUrl() {
			return self::$rewritten_url;
		}

		public function getRequestParam($name, $default = null) {
			return isset(self::$request_params[$name]) ? self::$request_params[$name] : $default;
		}

		public static function setDefaultModuleName($moduleName) {
			self::$defaultModuleName = $moduleName;
		}

	}
