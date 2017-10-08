<?php

    Application::loadObjectClass('point/point');

    class PointRewriteRule extends RewriteRule {

        static protected $types_lookup;
        static protected $items_lookup;
        static protected $item_types_lookup;

        public function __construct() {
            self::$items_lookup = null;
        }


        protected function &get_types_lookup() {
            if (self::$types_lookup === null) {
                self::$types_lookup = point_type::get_url_lookup();
            }
            return self::$types_lookup;
        }


        protected function &get_items_lookup() {
            if (self::$items_lookup === null) {
                self::$items_lookup = $this->fetch_lookup_info(point::get_table_name(), 'id', 'url');
            }
            return self::$items_lookup;
        }


        protected function &get_item_types_lookup() {
            if (self::$item_types_lookup === null) {
                self::$item_types_lookup = $this->fetch_lookup_info(point::get_table_name(), 'id', 'type');
            }
            return self::$item_types_lookup;
        }


        public function seoToInternal(URL $seo_url) {
            $parts = explode('/', $seo_url->getAddress());
            $type_url = array_shift($parts);

            if (!$type_url) return false;

            $types_lookup =& $this->get_types_lookup();
            $type_id = array_search($type_url, $types_lookup);
            if ($type_id === false) return false;

            $item_url = array_shift($parts);
            if (!$item_url) {
                $seo_url->setParts("point/list/$type_id", $parts);
                return $seo_url;
            }

            $items_lookup =& $this->get_items_lookup();
            $item_id = array_search($item_url, $items_lookup);
            if ($item_id !== false) {
                $seo_url->setParts("point/detail/$item_id", $parts);
                return $seo_url;
            }

            return false;
        }


        public function internalToSeo(URL $internal_url) {
            $parts = explode('/', $internal_url->getAddress());
            $task = array_shift($parts);
            switch($task) {
                case 'list':
                    $type_id = (int)array_shift($parts);
                    if (!$type_id) return false;
                    $url = point_type::get_url($type_id);
                    if (!$url) return false;
                    $internal_url->setParts($url, $parts);
                    return $internal_url;
                case 'detail':
                    $item_id = (int)array_shift($parts);
                    if (!$item_id) return false;
                    $items_lookup =& $this->get_items_lookup();
                    $item_types_lookup =& $this->get_item_types_lookup();
                    if (!isset($items_lookup[$item_id])) return false;
                    if (!isset($item_types_lookup[$item_id])) return false;

                    $item_type_id = $item_types_lookup[$item_id];

                    $item_type_url = point_type::get_url($item_type_id);
                    if(!$item_type_url) return false;

                    $internal_url->setParts("$item_type_url/{$items_lookup[$item_id]}", $parts);
                    return $internal_url;
            }

            return false;
        }




    }
