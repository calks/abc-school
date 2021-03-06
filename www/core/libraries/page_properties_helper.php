<?php

    class pagePropertiesHelper {

        protected static $_document;

        public static function getDocument()
        {
            if( !self :: $_document )
                self :: $_document = self :: findDocument();

            return self :: $_document;
        }

        public static function setTitleDescKeysFromDocument()
        {
            self :: setTitleDescKeysFromObject( self :: getDocument() );
        }


        /*
         * Function tries to fetch title, meta description and meta keywords
         * from documents basing on the page's URL.
         */
        public static function findDocument() {
            $module_name = Router::getModuleName();
            $doc = Application::getObjectInstance('document');

            if ($module_name == 'textpage') {
                $module_params = Router::getModuleParams();
                $url = @$module_params[0] ? $module_params[0] : 'index';
                $doc = $doc->loadToUrl($url, CURRENT_LANGUAGE);
            } else {
                $url = Router::getSourceUrl();

                // removing GET params
                if (($qmark = strpos($url, '?')) !== false) $url = substr($url, 0, $qmark);
                // removing hash
                if (($hash = strpos($url, '#')) !== false) $url = substr($url, 0, $hash);
                // removing leading slash
                if (substr($url, 0, 1) == '/') $url = substr($url, 1);

                $url = explode('/', $url);

                $doc_id = 0;
                // if there are more than one parts in the URL...
                if (isset($url[1])) {
                    // trying to find doc id from 1st and 2nd url parts combined
                    $doc_id = isset($url[1]) ? self::getDocumentIdByUrl($url[0].'/'.$url[1]) : null;
                    // trying to find doc id from 2nd url part
                    $doc_id = $doc_id ? null : self::getDocumentIdByUrl($url[1]);
                }

                // If where were nothing found, use only the first part
                if (!$doc_id) {
                    $doc_id = self::getDocumentIdByUrl($url[0]);
                }


                $doc_id = (int) $doc_id;
                $doc = $doc->load($doc_id);
            }

            return $doc;
        }

        protected function getDocumentIdByUrl($url) {
            $db = Application::getDb();
            $url = addslashes($url);
            $seo_url = addslashes(UrlRewriter::internalToSeo($url));
            $internal_url = addslashes(UrlRewriter::seoToInternal($url));
            $doc = Application::getObjectInstance('document');

            $table = $doc->get_table_name();
            $sql = "
                SELECT id, IF(TRIM(open_link)='$url', 1, 0) as is_external
                FROM $table
                WHERE
                    TRIM(open_link)='$url' OR TRIM(url)='$url' OR
                    TRIM(open_link)='$seo_url' OR TRIM(url)='$seo_url' OR
                    TRIM(open_link)='$internal_url' OR TRIM(url)='$internal_url'
                ORDER BY is_external DESC
                LIMIT 1
            ";

            //echo $sql . "<br>";

            $result = $db->executeSelectObject($sql);
            if (!$result) return null;
            else return $result->id;

        }

        protected static function getTitleFieldNames() {
            return array(
                'full_name', 'title', 'meta_title', 'page_title', 'title_tag'
            );
        }

        protected static function getDescriptionFieldNames() {
            return array(
                'meta_desc', 'meta_description'
            );
        }

        protected static function getKeywordsFieldNames() {
            return array(
                'meta_key', 'meta_keywords'
            );
        }

        public static function setTitleDescKeysFromObject($object) {
            $page = Application::getPage();

            foreach (self::getTitleFieldNames() as $field_name) {
                if (!isset($object->$field_name)) continue;
                if (!trim($object->$field_name)) continue;
                $page->setTitle($object->$field_name);
            }

            foreach (self::getDescriptionFieldNames() as $field_name) {
                if (!isset($object->$field_name)) continue;
                if (!trim($object->$field_name)) continue;
                $page->setDescription($object->$field_name);
            }

            foreach (self::getKeywordsFieldNames() as $field_name) {
                if (!isset($object->$field_name)) continue;
                if (!trim($object->$field_name)) continue;
                $page->setKeywords($object->$field_name);
            }
        }

    }

