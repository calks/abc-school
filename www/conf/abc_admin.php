<?php


    $config['database'] = array(
    	'host' => 'localhost',
    	'name' => 'abc',
    	'user' => 'root',
    	'pass' => ''
   );


    $config['templating'] = array(
        'template_dir' => '/applications/abc_admin/templates',
        'config_dir' => '/applications/abc_admin/templates/config',
        'cache_dir' => '/temp/smarty/cache/abc_admin',
        'compile_dir' => '/temp/smarty/compile/abc_admin'
    );


    define('LANGUAGES_ENGLISH', 1);    
    define('LANGUAGES_RUSSIAN', 2);

    define('CURRENT_LANGUAGE', LANGUAGES_RUSSIAN);


    define("FILE_PATH", Application::getSitePath() . "/files/");
    define("FILE_URL", Application::getSiteUrl() . "/files/");
    define("UPLOAD_PHOTOS", Application::getSitePath() . '/photos/abc');
    define("PHOTOS_URL", '/photos/abc');
    define("STATIC_PATH", Application::getSitePath() . '/applications/' . Application::getApplicationName() . '/static/' );
    define("STATIC_IMG_PATH", STATIC_PATH . 'img/' );
    
    
    require_once 'mail.php';
    require_once 'period.php';