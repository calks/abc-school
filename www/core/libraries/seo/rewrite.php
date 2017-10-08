<?php

    include_once 'rule.php';
    include_once 'url.php';

    class UrlRewriter {

        static protected $rules;

        protected function &getRules() {
            if (!is_array(self::$rules)) {
                self::$rules = array();
                $rules_dir = realpath(dirname(__FILE__)).'/module_rules';
                $app_specific_rules_dir = Application::getSitePath().'/applications/'.Application::getApplicationName().'/libraries/seo/module_rules';

                $directories = array($app_specific_rules_dir, $rules_dir);

                foreach ($directories as $dir_path) {
                    if (!$dir = @opendir($dir_path))
                        continue;

                    while ($file = readdir($dir)) {
                        if ('.' == $file || '..' == $file || false === strpos($file, '.php'))
                            continue;

                        $module_name = str_replace('.php', '', $file);
                        if (array_key_exists($module_name, self::$rules))
                            continue;

                        $class_name = ucfirst($module_name).'RewriteRule';

                        include_once $dir_path.'/'.$file;
                        self::$rules[$module_name] = new $class_name();
                    }
                    closedir($dir);

                }

            }
            return self::$rules;
        }

        public function seoToInternal($seo_url) {
            if ('/' == substr($seo_url, 0, 1)) {
                $leading_slash = '/';
                $seo_url = substr($seo_url, 1);
            } else {
                $leading_slash = '';
            }

            $rules =& self::getRules();
            $url = new URL($seo_url);

            foreach ($rules as $rule) {
                $internal_url = $rule->seoToInternal($url);
                if (false === $internal_url) {
                    continue;
                }

                // Rewriter can add some new GET-parameters,
                // so $_GET and $_REQUEST arrays must be updated before further using.
                $_GET = $internal_url->getGetParams();
                $_REQUEST = array_merge($_REQUEST, $_GET);

                return $leading_slash.$internal_url->toString();
            }

            return $leading_slash.$seo_url;
        }

        public function internalToSeo($internal_url) {
            //return $internal_url;
            if (substr($internal_url, 0, 1) == '/') {
                $leading_slash = '/';
                $url = substr($internal_url, 1);
            } else {
                $url = $internal_url;
                $leading_slash = '';
            }

            if (!$url)
                return $leading_slash;
            $slash_pos = strpos($url, '/');

            if ($slash_pos !== false) {
                $module_name = substr($url, 0, $slash_pos);
                $url = substr($url, $slash_pos + 1);
            } else {
                $module_name = $url;
                $url = '';
            }

            $rules =& self::getRules();

            if (isset($rules[$module_name])) {
                $rule = $rules[$module_name];
                $url = new URL($url);

                $seo_url = $rule->internalToSeo($url);
                if ($seo_url !== false)
                    return $leading_slash.$seo_url->toString();
            }

            return $internal_url;
        }

    }

