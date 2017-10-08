<?php

    include_once Application::getObjectPath('document');

    class TextpageModule extends Module {
        public function run($params = array()) {
            $url = @$params[0] ? $params[0] : 'index';

            $document = Application::getObjectInstance('document');

            if (!$page = $document->loadToUrl($url, CURRENT_LANGUAGE)) {
                return Application::runModule('page404');
            }
            
            if (!$page->active) {
            	return Application::runModule('page404');
            }
            
            pagePropertiesHelper::setTitleDescKeysFromObject($page);
            
            $breadcrumbs = Application::getBreadcrumbs();
            if ($page->parent_id ) {
                if ($parent_document = $document->load($page->parent_id )) {
                    $breadcrumbs->addNode(Application::getSeoUrl("/{$parent_document->url}" ), $parent_document->menu );
                }
            }

            $breadcrumbs->addNode(Application::getSeoUrl("/{$page->url}" ), $page->title );
            $breadcrumbs_html = Application::getBlockContent('breadcrumbs');

            Application::setContextVar('displayed_page_id', $page->id );

            $smarty = Application::getSmarty();
            $smarty->assign('page', $page);
            $smarty->assign('breadcrumbs', $breadcrumbs_html);

            $module_dir = $this->baseDir();
            $module_dir_app = dirname(Application::getAppSpecificModulePath($this->getName()));

            
            preg_match("/^([^.]+)(\.?.*)/", $page->url, $m);
            if (!empty($m[1])) {
                $script_name = str_replace('/', '_', $m[1]);

                $script_page_default = "{$module_dir}/scripts/{$script_name}.script.php";
                $script_page_app = "{$module_dir_app}/scripts/{$script_name}.script.php";

                $template_file = $this->getTemplatePath($script_name);

                $top_template_file = "{$module_dir}/templates/top/{$script_name}.tpl";

                if (file_exists($script_page_app)) {
                    require_once($script_page_app);
                }
                elseif (file_exists($script_page_default)) {
                    require_once($script_page_default);
                }

                if (file_exists($top_template_file)) {
                    $page_top_content = $smarty->fetch($top_template_file);
                    Application::loadModule('pagetop');
                    PagetopModule::setContent($page_top_content);
                }

                if (file_exists($template_file)) {
                    return $smarty->fetch($template_file);
                }
            }
            

            if( 'index.htm' == $url )
            {
                $b = Application :: getObjectInstance( 'blocks' );
                $socialBlock = $b->getBlockToSite( 'IndexPageSocial' );
                $smarty->assign( 'socialBlock', $socialBlock );
            }

            $template_path = $this->getTemplatePath('textpage');
            return $smarty->fetch($template_path);
        }
    }
