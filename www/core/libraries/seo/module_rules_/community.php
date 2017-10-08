<?php

    class CommunityRewriteRule extends RewriteRule {

        static protected $items_lookup;

        public function __construct() {
            self::$items_lookup = null;
        }

        protected function &get_items_lookup() {
            if (self::$items_lookup === null) {
                Application::loadObjectClass('community');
                self::$items_lookup = $this->fetch_lookup_info(community::get_table_name(), 'id', 'url');
            }
            return self::$items_lookup;
        }

        public function seoToInternal(URL $seo_url) {
            $parts = explode('/', $seo_url->getAddress());
            $str_key = array_shift($parts);

            if ($str_key != 'community') return false;
            $str_key = array_shift($parts);
            if (!$str_key) return false;

            $items_lookup =& $this->get_items_lookup();
            $item_id = array_search($str_key, $items_lookup);
            if ($item_id !== false) {
                $seo_url->setParts("community/$item_id", $parts);
                return $seo_url;
            }

            return false;
        }


        public function internalToSeo(URL $internal_url) {
            $parts = explode('/', $internal_url->getAddress());
            $item_id = (int)@$parts[1];
            if ($item_id) {
                unset($parts[1]);
                $items_lookup =& $this->get_items_lookup();
                if (!isset($items_lookup[$item_id])) return false;
                $internal_url->setParts('community/' . $items_lookup[$item_id], $parts);
                return $internal_url;
            }
            else {
                $internal_url->setParts('community', $parts);
                return $internal_url;
            }

        }


    }










