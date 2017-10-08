<?php

    class RealestateRewriteRule extends RewriteRule {

        static protected $categories_lookup;
        static protected $items_lookup;

        public function __construct() {
            self::$categories_lookup = null;
            self::$items_lookup = null;
        }

        protected function &get_categories_lookup() {
            if (self::$categories_lookup === null) {
                Application::loadObjectClass('realestate/categories');
                self::$categories_lookup = $this->fetch_lookup_info(realestate_categories::get_table_name(), 'id', 'url');
            }
            return self::$categories_lookup;
        }

        protected function &get_items_lookup() {
            if (self::$items_lookup === null) {
                Application::loadObjectClass('realestate/realestate');
                self::$items_lookup = $this->fetch_lookup_info(realestate::get_table_name(), 'id', 'url');
            }
            return self::$items_lookup;
        }

        public function seoToInternal(URL $seo_url) {
            $parts = explode('/', $seo_url->getAddress());
            $str_key = array_shift($parts);

            if (!$str_key) return false;

            if( $str_key == 'realestate-search' )
            {
            	$seo_url->setParts( "realestate/list_search", $parts );
            	return $seo_url;
            }
            
            $categories_lookup =& $this->get_categories_lookup();
            $category_id = array_search($str_key, $categories_lookup);
            if ($category_id !== false) {
                $seo_url->setParts("realestate/list_category/$category_id", $parts);
                return $seo_url;
            }

            $items_lookup =& $this->get_items_lookup();
            $item_id = array_search($str_key, $items_lookup);
            if ($item_id !== false) {
                $seo_url->setParts("realestate/detail/$item_id", $parts);
                return $seo_url;
            }

            return false;
        }


        public function internalToSeo(URL $internal_url) {
            $parts = explode('/', $internal_url->getAddress());
            $task = array_shift($parts);
            switch($task) {
            	case 'list_search':
            		$internal_url->setParts('realestate-search', $parts);
            		return $internal_url;
                case 'list_category':
                    $category_id = (int)array_shift($parts);
                    if (!$category_id) return false;
                    $categories_lookup =& $this->get_categories_lookup();
                    if (!isset($categories_lookup[$category_id])) return false;
                    $internal_url->setParts($categories_lookup[$category_id], $parts);
                    return $internal_url;
                case 'detail':
                    $item_id = (int)array_shift($parts);
                    if (!$item_id) return false;
                    $items_lookup =& $this->get_items_lookup();
                    if (!isset($items_lookup[$item_id])) return false;
                    $internal_url->setParts($items_lookup[$item_id], $parts);
                    return $internal_url;
            }

            /*$internal_url->setParts('realestate', $parts);
            return $internal_url;*/
            return false;
        }


    }










