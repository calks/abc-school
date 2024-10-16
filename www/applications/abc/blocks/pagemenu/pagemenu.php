<?php

	define('INDEX_PAGE_ID', 1);

    class PagemenuBlock extends Block {

        public function run($params=array()) {            
        	$menu_type = @$params['type'];
            if (!$menu_type) $menu_type = 'top';
            
            
            $template_path = $this->getTemplatePath($menu_type);	
            if (!is_file($template_path)) return '';
            
            $depth = $menu_type=='top' ? 3 : 1;
            $data = $this->getMenu($menu_type, 0, CURRENT_LANGUAGE, $depth);
                        
            $smarty = Application::getSmarty();
            $smarty->assign('menu', $data);
            
            $page = Application::getPage();
            $static_dir = Application::getBlockUrl($this->getName()) . '/static';
            $page->addStylesheet("$static_dir/css/pagemenu.css");
            $page->addScript("$static_dir/js/dropmenu.js");

            return $smarty->fetch($template_path);

        }

        protected function getMenu($type, $parent_id=0, $language_id=CURRENT_LANGUAGE, $depth=1){
        	
            $db = Application::getDb();

            $doc = Application::getObjectInstance('document');

            $table = $doc->get_table_name();
			
            if (!is_array($parent_id)) $parent_id = array($parent_id);
            elseif (!$parent_id) return array();
                        
            foreach ($parent_id as &$id) $id = (int)$id;
            $parent_id = implode(',', $parent_id);

            switch ($type) {
                case 'top':
                    $menu = 'menu & ' . SITE_MENUS_TOP_MENU;
                    break;
                case 'footer':
                    $menu = 'menu & ' . SITE_MENUS_FOOTER_MENU;
                    break;
                default:
                    return array();
            }

            $subquery = $doc->get_content_subquery($language_id);

            $query = "
                SELECT id, parent_id, open_link, url, open_new_window, title
                FROM $table JOIN $subquery AS content ON content.document_id = $table.id
                WHERE parent_id IN($parent_id) AND active = 1 AND $menu
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


            if ($depth > 1) {
                $children = $this->getMenu($type, $id_s, $language_id, $depth-1);

                foreach ($children as $child) {
                    $objects[$child->parent_id]->children[] = $child;
                }

                foreach ($objects as &$object) {
                    if($object->open_link != '') $url = $object->open_link;
                    else $url = $object->url;

                    $children = $object->children;

                    foreach ($children as &$child) {
                        $child->link = stringUtils :: urlDocument(($child->open_link != '') ? $child->open_link : $child->url);
                    }

                    $object->children = $children;
                    if ($object->id == INDEX_PAGE_ID) $object->link = '/';
                    else $object->link = stringUtils :: urlDocument($url);

                }
            }
            
            return $objects;
        }

    }
