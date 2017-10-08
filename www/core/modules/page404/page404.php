<?php

    class Page404Module extends Module {

        public function run($params=array()) {
            header("HTTP/1.0 404 Not Found");
            $page = Application::getPage();
            $page->setTitle('Страница не найдена');
            $page->setDescription('');
            $page->setKeywords('');
            $smarty = Application::getSmarty();
            $template_path = Application::getSitePath() . '/core/modules/page404/templates/page404.tpl'; 
            return $smarty->fetch($template_path);
        }
    }
