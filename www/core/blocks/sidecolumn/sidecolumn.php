<?php

    class SidecolumnBlock extends Block {

        public function run($params = array()) {
            $side = @$params['side'];
            if (!in_array($side, array('left', 'right'))) return '';

            if ($side=='left') $items = $this->get_left_column();
            else $items = $this->get_right_column();

            $smarty = Application::getSmarty();
            $smarty->assign('items', $items);

            $template_path = $this->getTemplatePath();
            return trim($smarty->fetch($template_path));
        }

        protected function get_left_column() {
            $items = array();

			$items[] = Application::getBlockContent( 'banner', array( 'positionH' => 'left', 'positionV' => 'top' ) );
            $items[] = Application::getBlockContent('homesearch');
            $items[] = Application::getBlockContent('realestatesearch');
            $items[] = Application::getBlockContent('searchonmap');
            $items[] = Application::getBlockContent('homefeatures');
            $items[] = Application::getBlockContent('pagemenu', array('type' => 'left'));
            //$items[] = Application::getBlockContent('facebook');
            $items[] = Application::getBlockContent('newsevents');
			$items[] = Application::getBlockContent( 'banner', array( 'positionH' => 'left', 'positionV' => 'bottom' ) );
            
            return $items;
        }


        protected function get_right_column() {
            $items = array();

			$items[] = Application::getBlockContent( 'banner', array( 'positionH' => 'right', 'positionV' => 'top' ) );
            $items[] = Application::getBlockContent('eventcalendar');
            $items[] = Application::getBlockContent('newsletter');
            $items[] = Application::getBlockContent( 'banner', array( 'positionH' => 'right', 'positionV' => 'middle1' ) );
            $items[] = Application::getBlockContent('homecalendar');
			$items[] = Application::getBlockContent( 'banner', array( 'positionH' => 'right', 'positionV' => 'bottom' ) );
            
            return $items;
        }

    }





