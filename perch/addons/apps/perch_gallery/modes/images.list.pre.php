<?php
    
    // An imageset is a container for the meta information around an image so we can store the original, main and thumb sizes in images.
    // In the UI we refer to an image - generally this is really an imageset object unless we are dealing with one image file itself
    $Images         = new PerchGallery_Images($API);
    $GalleryAlbums  = new PerchGallery_Albums($API);
    
    if (isset($_GET['id']) && $_GET['id']!='') {
        $albumID = (int) $_GET['id'];    
        $Album = $GalleryAlbums->find($albumID);
        $details = $Album->to_array();
    }

    $filter = 'images';
    
    

    $Form = $API->get('Form');
    $message = false;
    
    if ($Form->submitted()) {
        $data = $Form->receive(['batch','action']);
        
        if ($data['action']=='delete') {
            if (PerchUtil::count($data['batch'])) {
                foreach($data['batch'] as $imageID) {
                    $Image = $Images->find($imageID);
                    if (is_object($Image)) {
                        $Image->delete();
                        if (is_object($Album)) $Album->update_image_count();
                    }
                }
                
                $message = $HTML->success_message('The selected images have been deleted.');
            }
        }

    }
    
    $SortForm = $API->get('Form');
    $SortForm->set_name('sort');

    if ($SortForm->submitted()) {
	    if ($SortForm->submitted_via_ajax) {
	        $postvars = array('order');
            $data = $Form->receive($postvars);
            $order = explode(',', $data['order']);
            
            if (PerchUtil::count($order)) {
                $i=1;
                foreach($order as $imageID) {
                    $Image = $Images->find(trim($imageID));
                    if (is_object($Image)) {
                        $new_order = array();
                        $new_order['imageOrder'] = $i;
                        $Image->update($new_order);
                    }
                    
                    $i++;
                }
            }
            echo $SortForm->get_token();
	        exit;
	    }        
    }
    
    
    $images = $Images->get_by_album_id($albumID, array('admin_thumb'));
