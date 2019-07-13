<?php
    # include the API
    include('../../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'perch_gallery');
    $Lang = $API->get('Lang');
    $HTML = $API->get('HTML');
    $Paging = $API->get('Paging');

    # Set the page title
    $Perch->page_title = $Lang->get('Gallery: List Images');

    $Perch->add_css($API->app_path().'/admin.css');
    //$Perch->add_javascript($API->app_path().'/upload.js');

    $Perch->add_fe_plugin('plupload', str_replace('PERCH_LOGINPATH', PERCH_LOGINPATH, file_get_contents(__DIR__.'/../js/_config.json')));

    # Do anything you want to do before output is started
    include('../modes/_subnav.php');
    include('../modes/images.list.pre.php');
    
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');

    
    # Display your page
    include('../modes/images.list.post.php');
    
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
