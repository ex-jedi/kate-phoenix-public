<?php
    include('../../../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'perch_gallery');
    $Lang = $API->get('Lang');
    $HTML = $API->get('HTML');
    $Paging = $API->get('Paging');

    # Set the page title
    $Perch->page_title = $Lang->get('Gallery: Upload images');

    # $Perch->add_css($API->app_path().'/admin.css');
    # $Perch->add_css($API->app_path().'/js/jquery.plupload.queue/css/jquery.plupload.queue.css');
    #         
    # $Perch->add_javascript($API->app_path().'/js/plupload.js');
    # $Perch->add_javascript($API->app_path().'/js/plupload.gears.js');
    # $Perch->add_javascript($API->app_path().'/js/plupload.silverlight.js');
    # $Perch->add_javascript($API->app_path().'/js/plupload.flash.js');
    # $Perch->add_javascript($API->app_path().'/js/plupload.html4.js');
    # $Perch->add_javascript($API->app_path().'/js/plupload.html5.js');
    # $Perch->add_javascript($API->app_path().'/js/jquery.plupload.queue/jquery.plupload.queue.js');
    # 
    # $Perch->add_javascript($API->app_path().'/upload.js');

    $Perch->add_fe_plugin('plupload', str_replace('PERCH_LOGINPATH', PERCH_LOGINPATH, file_get_contents(__DIR__.'/../../js/_config.json')));

    include('../../modes/_subnav.php');
    include('../../modes/image.upload.pre.php');
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');
    
    # Display your page
    include('../../modes/image.upload.post.php');
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
