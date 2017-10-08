<?php

    class HeaderBlock extends Block {

        public function run($params=array()) {

            $smarty = Application::getSmarty();
            $template_path = $this->getTemplatePath();

            $top_menu = Application::getBlockContent('pagemenu', array('type'=>'top'));
            $top_lower_menu = Application::getBlockContent('pagemenu', array('type'=>'top_lower'));

            $search_box = Application::getBlockContent('googlesearch');

            $smarty->assign('top_menu', $top_menu);
            $smarty->assign('top_lower_menu', $top_lower_menu);
            $image_base_url = Application::getSiteUrl() . "/applications/" . Application::getApplicationName() . "/static/images/header";
            $smarty->assign('image_base_url', $image_base_url);
            $smarty->assign('search_box', $search_box);

            return $smarty->fetch($template_path);
        }

    }
