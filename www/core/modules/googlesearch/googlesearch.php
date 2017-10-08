<?php

    class GooglesearchModule extends Module {

        public function run($params=array()) {

            Application::loadObjectClass('document');
            $doc = new document();
            $page = $doc->loadToUrl('googlesearch');

            $smarty = Application::getSmarty();
            $smarty->assign('search_id', GOOGLE_SEARCH_ID);

            $smarty->assign('page', $page);
            $template_path = $this->getTemplatePath();
            return $smarty->fetch($template_path);
        }

    }







