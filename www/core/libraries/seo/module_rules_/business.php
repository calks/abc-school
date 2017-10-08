<?php

    class BusinessRewriteRule extends RewriteRule {

        static protected $categories_lookup;
        static protected $items_lookup;
        static protected $tags_lookup;

        static protected $item_tags_lookup;
        static protected $category_tags_lookup;

        public function __construct() {

            Application::loadObjectClass('business/business');
            Application::loadObjectClass('business/categories');

            // It's nasty, I'm sorry for that
            if (Application::getApplicationName() == 'kauai') Application::loadObjectClass('business/category_tag');

            self::$categories_lookup = null;
            self::$items_lookup = null;
            self::$tags_lookup = null;

            self::$item_tags_lookup = null;
            self::$category_tags_lookup = null;

        }

        protected function &get_categories_lookup() {
            if (self::$categories_lookup === null) {
                self::$categories_lookup = $this->fetch_lookup_info(business_categories::get_table_name(), 'id', 'url', 'is_active=1');
            }
            return self::$categories_lookup;
        }

        protected function &get_category_tags_lookup() {
            if (self::$category_tags_lookup === null) {
                self::$category_tags_lookup = $this->fetch_lookup_info(business_categories::get_table_name(), 'id', 'tag_id');
            }
            return self::$category_tags_lookup;
        }

        protected function &get_items_lookup() {
            if (self::$items_lookup === null) {
                self::$items_lookup = $this->fetch_lookup_info(business::get_table_name(), 'id', 'url');
            }
            return self::$items_lookup;
        }


        protected function &get_item_tags_lookup() {
            if (self::$item_tags_lookup === null) {
                $db = Application::getDb();
                /* Technically, businesses has many-to-many relationship with categories
                 * But client believes that there will not be any reason for business to
                 * be included in more than one. So where should not be any problems
                 * like having /activities/something URL for item under /dining
                 */
                $data = $db->executeSelectAllObjects("
                    SELECT b.id AS busienss_id, t.id AS tag_id
                    FROM business b LEFT JOIN business_to_categories btc ON btc.business_id = b.id
                    LEFT JOIN business_categories bc ON bc.id = btc.category_id AND bc.tag_id IS NOT NULL
                    LEFT JOIN business_category_tag t ON t.id = bc.tag_id
                ");

                self::$item_tags_lookup = array();
                foreach($data as $item) self::$item_tags_lookup[$item->busienss_id] = $item->tag_id;

            }
            return self::$item_tags_lookup;
        }


        protected function &get_tags_lookup() {
            if (self::$tags_lookup === null) {
                self::$tags_lookup = business_category_tag::get_url_mapping();
            }
            return self::$tags_lookup;
        }

        public function seoToInternal(URL $seo_url) {
            $parts = explode('/', $seo_url->getAddress());
            $str_key = array_shift($parts);
            if (!$str_key) return false;


            // and I did it again, sorry
            if (Application::getApplicationName() == 'kauai') {
                // test if url contain directory tag url
                $tags_lookup =& $this->get_tags_lookup();
                $tag_id = array_search($str_key, $tags_lookup);
                if ($tag_id !== false) {
                    if (empty($parts)) {
                        $seo_url->setParts("business/list_all/$tag_id", $parts);
                        return $seo_url;
                    }
                    else {
                        $str_key = array_shift($parts);
                    }
                }
            }


            $categories_lookup =& $this->get_categories_lookup();
            $category_id = array_search($str_key, $categories_lookup);
            if ($category_id !== false) {
                $seo_url->setParts("business/list_category/$category_id", $parts);
                return $seo_url;
            }

            $items_lookup =& $this->get_items_lookup();
            $item_id = array_search($str_key, $items_lookup);
            if ($item_id !== false) {
                $seo_url->setParts("business/detail/$item_id", $parts);
                return $seo_url;
            }

            if ($str_key=='directory') {
                $seo_url->setParts("business", $parts);
                return $seo_url;
            }


            return false;
        }


        public function internalToSeo(URL $internal_url) {
            $parts = explode('/', $internal_url->getAddress());
            $part = array_shift($parts);

            if (is_numeric($part)) {
                $part = (int)$part;
                $tags_lookup =& $this->get_tags_lookup();
                if (!isset($tags_lookup[$part])) return false;
                $internal_url->setParts($tags_lookup[$part], $parts);
                return $internal_url;
            }


            switch($part) {
                case 'list_category':
                    $category_id = (int)array_shift($parts);
                    if (!$category_id) return false;
                    $categories_lookup =& $this->get_categories_lookup();
                    if (!isset($categories_lookup[$category_id])) return false;
                    $category_url = $categories_lookup[$category_id];

                    /*$category_tags_lookup =& $this->get_category_tags_lookup();
                    if (!isset($category_tags_lookup[$category_id])) return false;
                    $tag_id = $category_tags_lookup[$category_id];

                    $tags_lookup =& $this->get_tags_lookup();
                    if (!isset($tags_lookup[$tag_id])) return false;
                    $tag_url = $tags_lookup[$tag_id];*/

                    //$internal_url->setParts("$tag_url/$category_url", $parts);
                    $internal_url->setParts("$category_url", $parts);
                    return $internal_url;
                case 'detail':
                    $item_id = (int)array_shift($parts);
                    if (!$item_id) return false;
                    $items_lookup =& $this->get_items_lookup();
                    if (!isset($items_lookup[$item_id])) return false;
                    $item_url = $items_lookup[$item_id];

                    $item_tags_lookup =& $this->get_item_tags_lookup();

                    if (!isset($item_tags_lookup[$item_id])) return false;
                    $tag_id = $item_tags_lookup[$item_id];

                    /*$tags_lookup =& $this->get_tags_lookup();
                    if (!isset($tags_lookup[$tag_id])) return false;
                    $tag_url = $tags_lookup[$tag_id];*/

                    $internal_url->setParts("$item_url", $parts);
                    return $internal_url;
            }

            $internal_url->setParts('directory', $parts);
            return $internal_url;
        }


    }










