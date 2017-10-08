<?php

    abstract class Module {

        protected $run_mode;

        abstract public function run($params = array());

        public function __construct() {
            $this->run_mode = (Router::getModuleName() == $this->getName()) ? 'module' : 'block';
        }

        protected function getModuleType() {
            return 'module';
        }

        protected function getName() {
            $class = get_class($this);
            //$class = strtolower($class);
            $module_type = $this->getModuleType();
            $class = str_replace(ucfirst($module_type), '', $class);

            $application_name = Application::getApplicationName();
            $application_name_camel_case = str_replace(' ', '', ucwords(str_replace('_', ' ', $application_name))); 
            
            $class = str_replace($application_name, '', $class);
            $class = str_replace($application_name_camel_case, '', $class);
            
            $out = '';
            while($letter = substr($class, 0, 1)) {
            	if ($out && $letter != strtolower($letter)) $out .= '_';
            	$out .= strtolower($letter);
            	$class = substr($class, 1);
            }            

            return $out;
            
            return $class;
        }

        protected function terminate() {
            switch ($this->getRunMode()) {
            case 'module':
                return Application::runModule('page404');
            default:
                return '';
            }
        }

        protected function runTaskByParams($params) {
            $task = array_shift($params);
            if (!trim($task)) return $this->terminate();
            $method_name = 'task'.ucfirst(strtolower($task));
            if (!method_exists($this, $method_name)) return $this->terminate();
            return call_user_func(array($this, $method_name), $params);
        }

        public function baseDir() {
            $class = $this->getName();
            $module_type = $this->getModuleType();
            $method_name = 'get'.ucfirst(strtolower($module_type)).'Path';
            $module_path = call_user_func(array('Application', $method_name), $class);
            return dirname($module_path);
        }

        public function getTemplatePath($template_name = '') {
            if (!$template_name) $template_name = $this->getName();

            $module_name = $this->getName();
                        
            $application_name = Application::getApplicationName();
            $directory_name = $this->getModuleDirectoryName();

            $site_path = Application::getSitePath();
            $standard_path = "$site_path/core/$directory_name/$module_name/templates/$template_name.tpl";
            $override_path = "$site_path/applications/$application_name/$directory_name/$module_name/templates/$template_name.tpl";

            if (is_file($override_path)) return $override_path;
            else return $standard_path;
        }

        public function getRunMode() {
            return $this->run_mode;
        }

        protected function getModuleDirectoryName() {
            switch ($this->getModuleType()) {
            case 'module':
                return 'modules';
            case 'block':
                return 'blocks';
            default:
                die('Can\'t determine type of myself');
            }
        }

        public function getUrl() {
            return Application::getModuleUrl($this->getName());
        }

        public function getStaticResourceUrl($relative_path) {
            if (substr($relative_path, 0, 1) == '/') $relative_path = substr($relative_path, 1);
            $module_name = $this->getName();
            $application_name = Application::getApplicationName();
            $directory_name = $this->getModuleDirectoryName();

            $standard_path = "/core/$directory_name/$module_name/$relative_path";
            $override_path = "/applications/$application_name/$directory_name/$module_name/$relative_path";

            if (is_file(Application::getSitePath().$override_path)) {
                //return Application::getSiteUrl().$override_path;
                return $override_path;
            } else {
                //return Application::getSiteUrl().$standard_path;
                return $standard_path;
            }
        }


    }
