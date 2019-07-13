<?php
    
    $GalleryAlbums = new PerchGallery_Albums($API);

    $HTML = $API->get('HTML');
    $Form = $API->get('Form');
    $Form->set_name('delete');
	
	$message = false;
	
	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Album = $GalleryAlbums->find($_GET['id']);
	}else{
	    PerchUtil::redirect($API->app_path());
	}
	

    if ($Form->submitted()) {
    	
    	if (is_object($Album)) {
    	    $Album->delete();


            if ($Form->submitted_via_ajax) {
                echo $API->app_path();
                exit;
            }else{
                PerchUtil::redirect($API->app_path());
            }

        }else{
            $message = $HTML->failure_message('Sorry, that album could not be deleted.');
        }
    }

    
    
    $details = $Album->to_array();
