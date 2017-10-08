<?php

    class sitemapModule extends Module {

        public function run($params=array()) {

            $sitemap_data = $this->getSitemap();

            $smarty = Application::getSmarty();
            $template_path = $this->getTemplatePath();
            $smarty->assign('sitemap_data', $sitemap_data);
            $smarty->assign('template_path', $template_path);

            return $smarty->fetch($template_path);

        }


        protected function getSitemap($parent_id=0, $language_id=LANGUAGES_ENGLISH){
            $db = Application::getDb();

            $doc = Application::getObjectInstance('document');
            $table = $doc->get_table_name();

            if (!is_array($parent_id)) $parent_id = array($parent_id);
            foreach ($parent_id as &$id) $id = (int)$id;
            $parent_id = implode(',', $parent_id);

            $subquery = $doc->get_content_subquery($language_id);

            $query = "
                SELECT id, parent_id, open_link, url_name, url_image, open_new_window, menu_title, color
                FROM $table JOIN $subquery AS content ON content.document_id = $table.id
                WHERE parent_id IN($parent_id) AND active = 1
                GROUP BY document_id ORDER BY seq ASC
            ";

            $objects_raw = $db->executeSelectAllObjects($query);
            $objects = array();
            $id_s = array();
            foreach($objects_raw as $obj) {
                $id_s[] = $obj->id;
                $obj->children = array();
                $objects[$obj->id] = $obj;
            }


            if (!$parent_id) {

                $children = $this->getSitemap($id_s);

                foreach ($children as $child) {
                    $objects[$child->parent_id]->children[] = $child;
                }

                foreach ($objects as &$object) {
                    if($object->open_link != '') $url = $object->open_link;
                    else $url = $object->url_name;

                    $children = $object->children;

                    switch ($url) {
                        case "business":
                            Application::loadObjectClass('business/categories');
                            $tmp = Application::getObjectInstance('business/categories');
                            $children = array_merge($tmp->getMenu(), $children);
                            $object->category = 1;
                            break;

                        case "realestate":
                            $tmp = Application::getObjectInstance('realestate/categories');
                            $children = array_merge($tmp->getMenu(), $children);
                            $object->category = 1;
                            break;

                        case "community":
                            Application::loadObjectClass('community');
                            $tmp = new community();
                            $children = array_merge($tmp->getMenu(), $children);
                            $object->category = 1;
                            break;

                        case "news.php":
                            Application::loadObjectClass('news');
                            $tmp = new news();
                            $children = array_merge($tmp->getLastNews(3), $children);
                            break;

                        case "eventcalendar":
                            Application::loadObjectClass('eventcalendar/events_calendar');
                            $tmp = new events_calendar();
                            $children = array_merge($tmp->getMenu(), $children);
                            break;

                        case "classifieds":
                            Application::loadObjectClass('classifieds/classifieds');
                            $tmp = new classifieds();
                            $children = array_merge($tmp->getMenu(), $children);
                            break;

                    }

                    foreach ($children as &$child) {
                        $child->link = stringUtils :: urlDocument(($child->open_link != '') ? $child->open_link : $child->url_name);
                    }

                    $object->children = $children;
                    if ($object->id == INDEX_PAGE_ID) $object->link = '/';
                    else $object->link = stringUtils :: urlDocument($url);

                }
            }

            return $objects;
        }


    }
