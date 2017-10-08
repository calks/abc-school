<?php

    class Page {
        protected static $_instance = array();
        protected static $_accessibleProperties = array();
        protected static $_keyMetaAttributes = array();

        protected $_heading = '';
        protected $_title = '';
        protected $_title_static_part = '';
        protected $_title_delimeter = '';
        protected $_description = '';
        protected $_keywords = '';
        protected $_meta = '';

        protected $_stylesheets = array();
        protected $_scripts = array();

        public static function getInstance() {
            if (!self::$_instance)
                self::$_instance = new Page();

            return self::$_instance;
        }

        private function __construct() {
            self::$_accessibleProperties = array('_heading', '_title', '_description');
            self::$_keyMetaAttributes = array('name', 'property', 'http-equiv');

            $this->_title = '';
            $this->_description = '';
            $this->_keywords = '';

            $this->_meta = array();
            $this->_stylesheets = array();
            $this->_scripts = array();
        }

        private function __clone() {
        }

        public function __set($property, $value) {
            $property = '_'.strtolower($property);
            if (in_array($property, self::$_accessibleProperties))
                $this->{$property} = $value;
        }

        public function __get($property) {
            $property = '_'.strtolower($property);
            return in_array($property, self::$_accessibleProperties) ? $this->{$property} : null;
        }

        public function addMeta(array $meta) {
            $key = '';
            foreach (self::$_keyMetaAttributes as $keyAttr) {
                if (isset($meta[$keyAttr]))
                    $key .= "{$keyAttr}=\"{$meta[$keyAttr]}\" ";
            }
            $key = md5($key);

            $this->_meta[$key] = $meta;

            return $this;
        }

        public function setTitle($title) {
            $this->_title = $title;
        }

        public function setTitleStaticPart($static_part, $delimeter = '|') {
            $this->_title_static_part = $static_part;
            $this->_title_delimeter = $delimeter;
        }

        public function setDescription($description) {
            $this->_description = $description;
        }

        public function setKeywords($keywords) {
            $this->_keywords = $keywords;
        }

        public function addScript($source, $type = 'text/javascript') {
            $app_path = Application::getApplicationDir() . '/static' . $source;
            if (is_file($app_path)) $source = Application::getApplicationUrl() . '/static' . $source;

            if (!isset($this->_scripts[$type]))
                $this->_scripts[$type] = array();
            if (in_array($source, $this->_scripts[$type]))
                return $this;
            $this->_scripts[$type][] = $source;

            return $this;
        }


        public function addStylesheet($source) {
        	//$source = Application :: getSitePath() . $source;        	
        	if (substr($source, 0, 7) == 'http://') {
        		if(!in_array($source, $this->_stylesheets)) $this->_stylesheets[] = $source;
        		return $this;
        	}
        	
        	if(!is_file($source)) {
        		if (substr($source, 0, 1) == '/') $source = substr($source, 1);
        		$source = Application :: getApplicationDir() . '/static/css/' . $source;        		        		
        	}	        		
        	if(is_file($source)) {        		
				$source = str_replace( Application :: getSitePath(), '', $source );
				if(!in_array($source, $this->_stylesheets)) $this->_stylesheets[] = $source;				
        	}
        	
            return $this;
        }

        protected function prepareString($str) {
        	return strip_tags(htmlspecialchars($str, ENT_QUOTES, 'utf-8'));
        }
        
        public function getHtmlHead() {
            $out = "<head>\n";
            $title = $this->getTitle();
            $title = $this->prepareString($title);

            $out .= "\t<title>$title</title>\n";
            
            $keywords = $this->prepareString($this->_keywords);
            $out .= "\t<meta name=\"keywords\" content=\"{$keywords}\">\n";

            $description = $this->prepareString($this->_description);
            $out .= "\t<meta name=\"description\" content=\"{$description}\">\n";

            foreach ($this->_meta as $meta) {
                $out .= "\t<meta ";
                foreach ($meta as $attr => $value)
                    $out .= "{$attr}=\"{$value}\" ";
                $out .= ">\n";
            }

            foreach ($this->_stylesheets as $stylesheet)
                $out .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"{$stylesheet}\">\n";

            foreach ($this->_scripts as $type => $items) {
                foreach ($items as $item) {
                    $out .= "\t<script type=\"$type\" src=\"$item\"></script>\n";
                }
            }

            $out .= "</head>\n";

            return $out;
        }

        public function getTitle( $useStaticPart = true )
        {
            $title = $this->_title;
            if( $useStaticPart && $this->_title_static_part) {
                if( $title )
                    $title .= $this->_title_delimeter ? " $this->_title_delimeter " : ' ';
                $title .= $this->_title_static_part;
            }

            return $title;
        }
    }
