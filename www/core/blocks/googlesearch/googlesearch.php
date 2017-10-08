<?php

    class GooglesearchBlock extends Block {

        public function run($params=array()) {

            $smarty = Application::getSmarty();
            $smarty->assign('form_action', Application::getSeoUrl('/googlesearch'));
            $smarty->assign('search_id', GOOGLE_SEARCH_ID);
            $template_path = $this->getTemplatePath();
            return $smarty->fetch($template_path);
        }

    }
