<?php

    class HomegroupsRewriteRule extends RewriteRule {

        static protected $items_lookup;

        public function __construct() {
            self::$items_lookup = null;
        }

        protected function &get_items_lookup() {
            if (self::$items_lookup === null) {
                Application::loadModule('homegroups');
                $module_class = Application::getClassName( APP_COMPONENT_TYPE_MODULE, 'homegroups' );
                $urls = call_user_func(array($module_class, 'getUrls'));

                foreach($urls as $url) self::$items_lookup[$url->id] = $url->url;
            }
            return self::$items_lookup;
        }

        public function seoToInternal(URL $seo_url) {
            $parts = explode('/', $seo_url->getAddress());

            /*$str_key = array_shift($parts);
            if ($str_key != '123') return false;*/

            $str_key = array_shift($parts);
            if (!$str_key) return false;

            $items_lookup =& $this->get_items_lookup();
            $item_id = array_search($str_key, $items_lookup);
            if ($item_id !== false) {
                $seo_url->setParts("homegroups/$item_id", $parts);
                return $seo_url;
            }

            return false;
        }


        public function internalToSeo(URL $internal_url) {
            $parts = explode('/', $internal_url->getAddress());
            $item_id = (int)array_shift($parts);
            if ($item_id) {
                unset($parts[1]);
                $items_lookup =& $this->get_items_lookup();
                if (!isset($items_lookup[$item_id])) return false;
                $internal_url->setParts($items_lookup[$item_id], $parts);
                return $internal_url;
            }

            return false;
        }


    }










