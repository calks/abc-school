<?php

    error_reporting(E_ALL);
    
    if ($_SERVER['REQUEST_URI'] == '/index') $_SERVER['REQUEST_URI'] = '/';

    include 'core/libraries/core/application.php';

    $host = @$_SERVER['HTTP_HOST'];
    
    $URI = $_SERVER['REQUEST_URI'];
    if (substr($URI, 0, 1) == '/') $URI = substr($URI, 1);
    if (substr($URI, strlen($URI)-1, 1) == '/') $URI = substr($URI, 0, strlen($URI)-1);
    
    $URI_parts = explode('/', $URI);
        
    if ($URI_parts && strtolower($URI_parts[0]) == 'admin') {
    	$application_name = 'abc_admin';
    	unset($URI_parts[0]);
    	$_SERVER['REQUEST_URI'] = implode('/', $URI_parts);	
    }
    else {
    	$application_name = 'abc';
    }
    
   
    Application::init($application_name);

    Application::loadLibrary('misc');
    Application::loadLibrary('core/dataobject');
    Application::loadLibrary('core/debug');
    Application::loadLibrary('page_properties_helper');

    define('USE_PROFILER', false);

    if (USE_PROFILER) {
        Application::loadLibrary('core/profiler');
        $profiler = new profiler("Total time consumption");
        $profiler->start();
    }

    require Application::getSitePath()."/applications/{$application_name}/index.php";

    if (USE_PROFILER)
    $profiler->stop();

