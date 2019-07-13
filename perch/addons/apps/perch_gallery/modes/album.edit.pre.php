<?php
    
    $GalleryAlbums = new PerchGallery_Albums($API);
    $message = false;
    

    if (isset($_GET['id']) && $_GET['id']!='') {
        $albumID = (int) $_GET['id'];    
        $Album = $GalleryAlbums->find($albumID);
        $details = $Album->to_array();
        
        $heading1 = 'Editing album options';
        $heading2 = 'Edit album';
        
    }else{
        $Album = false;
        $albumID = false;
        $details = array();
        
        $heading1 = 'Creating a new album';
        $heading2 = 'Add album';

        if (!$CurrentUser->has_priv('perch_gallery.album.create')) die('Your role does not have permission to create new albums.');
    }

    $Template   = $API->get('Template');
    $Template->set('gallery/album.html', 'gallery');



    $result = false;

    $Form = $API->get('Form');

    $Form->handle_empty_block_generation($Template);

    $Form->require_field('albumTitle', 'Required');
    
    $Form->set_required_fields_from_template($Template, $details);

    if ($Form->submitted()) {

           
        $postvars = array('albumID','albumTitle','albumOrder');
		
    	$data = $Form->receive($postvars);
    	$dynamic_fields = $Form->receive_from_template_fields($Template, $details, $GalleryAlbums, $Album);
    	$data['albumDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);
    	

    	if (is_object($Album)) {
    	    $result = $Album->update($data);
    	}else{
    	    if (isset($data['albumID'])) unset($data['albumID']);
    	    $new_post = $GalleryAlbums->create($data);
    	    if ($new_post) {
    	        $result = true;
    	        PerchUtil::redirect($API->app_path() .'/edit/?id='.$new_post->id().'&created=1');
    	    }else{
    	        $message = $HTML->failure_message('Sorry, that album could not be updated.');
    	    }
    	}
    	
    	
        if ($result) {
            $message = $HTML->success_message('Your album has been successfully updated. Return to %salbum listing%s', '<a href="'.$API->app_path() .'">', '</a>');  
        }else{
            $message = $HTML->failure_message('Sorry, that album could not be updated.');
        }
        
        if (is_object($Album)) {
            $details = $Album->to_array();
           
        }else{
            $details = array();
        }
        
    }
    
    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your album has been successfully created. Return to %salbum listing%s', '<a href="'.$API->app_path() .'">', '</a>'); 
    }
