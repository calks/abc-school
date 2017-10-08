<?php

    class PagetopModule extends Module {
        static protected $content;

        public function run($params=array()) {
            return self::$content;
        }


        public function setContent($content) {
            self::$content = $content;
        }

    }
