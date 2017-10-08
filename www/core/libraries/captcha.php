<?php


    class captcha {

        protected $name;

        public function __construct($name) {
            $this->name = 'captcha_code_' . $name;
            if(!isset($_SESSION[$this->name])) {
                $this->regenerate();
            }
        }

        public function display() {
            print '<img class="captcha" src="/captcha/' . $this->name . '/' . time() . '" width="150" height="50" alt="Number Verification">';
        }

        public function regenerate() {
            $_SESSION[$this->name] = make_pass(4);
        }

        public function get_code() {
            return $_SESSION[$this->name];
        }

        public function code_valid($code) {
            return strtolower($this->get_code()) == strtolower($code);
        }

    }
