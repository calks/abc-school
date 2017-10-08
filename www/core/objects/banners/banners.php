<?php

    class banners extends DataObject
    {
        const TABLE_NAME = 'banners';

        var $id;
        var $name;
        var $html_code;
        var $banner_code;

        protected $internal_positionH;   // 'right', 'left' or 'center'
        protected $internal_positionV;   // 'top', 'bottom' or 'middleN' ( N = 1, 2, 3,... )
        protected $internal_pageurl;     // part of page url, where banner must be displayed
                                         // ('all' for displaying at all pages with lowest priority)

        protected static $internal_cache = array();

        function get_table_name() {
            return self :: TABLE_NAME;
        }

        function load_list( $params = array() )
        {
            $list = parent :: load_list( $params );

            foreach( $list as $banner )
            {
                $banner->name = stripslashes( $banner->name );
                $banner->html_code = trim( stripslashes( $banner->html_code ) );
                $banner->decodeBannerPosition();
            }
            return $list;
        }

        function make_form(&$form) {
            $form->addField(new THiddenField("id"));
            $form->addField(new TEditField("name", "", 85, 100));
            $form->addField(new TEditField("banner_code", "", 85, 100));
            $form->addField(new TEditorField("html_code", ""));
            return $form;
        }

        protected function decodeBannerPosition()
        {
            preg_match( '#(left|center|right)-(top|bottom|middle\d+)-(.+)#', strtolower( $this->banner_code ), $result );
            if( !isset( $result[ 0 ] ) )
            {
                $this->internal_positionH = '';
                $this->internal_positionV = '';
                $this->internal_pageurl = '';
            }
            else
            {
                $this->internal_positionH = $result[ 1 ];
                $this->internal_positionV = $result[ 2 ];
                $this->internal_pageurl = implode( '/', explode( '-', $result[ 3 ] ) );
            }
        }

        public static function search( $positionH = '', $positionV = '', $pageUrl = '' )
        {
            if( !self :: $internal_cache )
            {
                $b = Application :: getObjectInstance( 'banners' );
                self :: $internal_cache = $b->load_list();
            }

            if( !$pageUrl )
                $pageUrl = 'index';

            foreach( self :: $internal_cache as $banner )
                if( $banner->internal_positionH == $positionH && $banner->internal_positionV == $positionV &&
                    $banner->internal_pageurl == $pageUrl )
                {
                    return $banner;
                }

            if( 'all' == $pageUrl )
                return null;
            else
            {
                $pageUrl = explode( '/', $pageUrl );
                array_pop( $pageUrl );
                $pageUrl = $pageUrl
                    ? implode( '/', $pageUrl )
                    : 'all';

                return self :: search( $positionH, $positionV, $pageUrl );
            }
        }
    }
