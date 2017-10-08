<?php

    class FooterBlock extends Block {

        function run($params=array()) {
            Application::loadObjectClass('blocks');

            $blocks = new blocks();
            $footer_html = $blocks->getBlockToSite('footer')->html_code;

            $smarty = Application::getSmarty();
            $smarty->assign('footer_html', $footer_html);
            $template_path = $this->getTemplatePath();
            return $smarty->fetch($template_path);
        }

    }
