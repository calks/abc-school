<?php

    class OwnerloginRewriteRule extends RewriteRule {

        public function seoToInternal(URL $seo_url) {
            $parts = explode('/', $seo_url->getAddress());

            $directory = array_shift($parts);
            if (!in_array($directory, array('rentalowner', 'businessowner'))) return false;
            $task = array_shift($parts);
            if (!in_array($task, array('login', 'forgot'))) return false;

            $property_type = ($directory=='rentalowner') ? 'home' : 'business';
            $seo_url->setParts("ownerlogin/$property_type/$task", $parts);
            return $seo_url;
        }


        public function internalToSeo(URL $internal_url) {
            $parts = explode('/', $internal_url->getAddress());

            $property_type = array_shift($parts);
            $directory = ($property_type=='home') ? 'rentalowner' : 'businessowner';
            $task = array_shift($parts);
            $internal_url->setParts("$directory/$task", $parts);
            return $internal_url;
        }


    }










