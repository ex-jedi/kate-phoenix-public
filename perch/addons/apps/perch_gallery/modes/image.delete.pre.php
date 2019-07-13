<?php
    
    $GalleryImages = new PerchGallery_Images($API);
    $Albums = new PerchGallery_Albums($API);

    $HTML = $API->get('HTML');
    $Form = $API->get('Form');
	
	$message = false;
	
	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Image = $GalleryImages->find($_GET['id']);
	}else{
	    PerchUtil::redirect($API->app_path().'/');
	}
	

    if ($Form->submitted()) {
    	if (is_object($Image)) {
    	    $albumID = $Image->albumID();
    	    $Image->delete();
            $Album = $Albums->find($albumID);
            if (is_object($Album)) $Album->update_image_count();
            PerchUtil::redirect($API->app_path().'/images/?id='.$albumID);
        }else{
            $message = $HTML->failure_message('Sorry, the image could not be deleted.');
        }
    }
    
    
    $details = $Image->to_array();



?>