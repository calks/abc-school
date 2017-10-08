<?php

    /*
     * For now, the most significant difference between
     * blocks and modules is that blocks can't be run
     * by passing their names through request string.
     *
     * In other words, blocks are modules placed in other
     * directory, outside of router's scope
     */

    Application::loadLibrary('core/module');

    abstract class Block extends Module {

        public function __construct() {
            $this->run_mode = 'block';
        }

        protected function getModuleType() {
            return 'block';
        }

        public function getUrl($relative_path) {
            return Application::getBlockUrl();
        }


    }
