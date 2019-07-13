<?php

    spl_autoload_register(function($class_name){
        if (strpos($class_name, 'PerchGallery')===0) {
            include(PERCH_PATH.'/addons/apps/perch_gallery/lib/'.$class_name.'.class.php');
            return true;
        }
        return false;
    });

    include(__DIR__.'/fieldtypes.php');

    /**
     * 
     * list albums, if return_image is set to true will also return an image for each album. 
     * This will be the first returned using the default sort order.
     * @param string $template
     * @param bool $return_image
     * @param bool $return
     */
    function perch_gallery_albums($opts=array(), $return=false) 
    {
    	$default_opts = array(
    	   'template'=>'a_album.html',
    	   'image'=>false,
    	   'skip-template'=>false
    	);
    	
    	$opts = array_merge($default_opts, $opts);
    	
        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

    	if ($opts['skip-template']) $return=true;
    	
    	$API = new PerchAPI(1.0, 'perch_gallery');
    	
    	$Albums = new PerchGallery_Albums($API);
    	
    	$albums = $Albums->get_custom($opts);
    	
    	$Template = $API->get('Template');
    	$Template->set('gallery/'.$opts['template'], 'gallery');
    	
    	if($opts['image']) {
    		$r = array();
    		if(PerchUtil::count($albums)>0) {
    			$Images = new PerchGallery_Images($API);
    			foreach($albums as $details) {
    				$FirstImage = $Images->get_by_album_id($details['albumID'], false, 1);
    				if(is_object($FirstImage)) {		
			    		$details = array_merge($FirstImage->to_array(), $details);
    				}			
			    	$r[] = $details;
    			}
    		}
    		if (!$opts['skip-template']) $r = $Template->render_group($r, true);
    	}else{
    		// just render basic album data
    		if (!$opts['skip-template']) $r = $Template->render_group($albums, true);
    	}
    	
    	if ($opts['skip-template']) return $albums;
    		
    	if($return) return $r;
    	
    	echo $r;
    	
    }
    
    // alias
    function perch_gallery_album_listing($opts=array(), $return=false) {
        return perch_gallery_albums($opts, $return);
    }


    /**
     * 
     * build a single page gallery display of albums and images within them
     * @see perch_gallery_album
     * @param string $template_album
     * @param string $template_listing
     * @param string $template_image
     * @param bool $return
     */
    function perch_gallery_build($opts=array(), $return=false) 
    {	
    	$default_opts = array(
    	   'template'         =>'b_static_list_image.html', 
    	   'skip-template'    =>false
    	);
    	
    	$opts = array_merge($default_opts, $opts);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);
    	
    	if ($opts['skip-template']) $return=true;
    	
    	$API = new PerchAPI(1.0, 'perch_gallery');
    	
    	$Albums = new PerchGallery_Albums($API);

    	$list = $Albums->return_all();
    	

    	if ($opts['skip-template']) {
    	    $out = array();
    	    foreach($list as $Album) {
    			$out[] = perch_gallery_album_images($Album->albumSlug(), $opts, true);
    		}
    		return $out;
    	}
    	    	
    	$r = false;
    	    	    	
    	$Template = $API->get('Template');
    	
    	if(PerchUtil::count($list)>0) {
    		foreach($list as $Album) {
    			$r.= perch_gallery_album_images($Album->albumSlug(), $opts, true);
    		}	
    	}
	    	
    	if($return) return $r;
    	
    	echo $r;
    	
    }
    
    
    /**
     * 
     * Display images for an album
     * @param string $slug
     * @param string $template_listing
     * @param string $template_image
     * @param bool $return
     */
    function perch_gallery_album_images($slug, $opts=array(), $return=false) 
    {	
    	$default_opts = array(
    	   'template'   =>'a_list_image.html',
    	   'skip-template'=>false
    	);
    	
    	$opts = array_merge($default_opts, $opts);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);
    	
    	if ($opts['skip-template']) $return=true;
    	
    	$API = new PerchAPI(1.0, 'perch_gallery');
    	
    	$Albums = new PerchGallery_Albums($API);
    	$Images = new PerchGallery_Images($API);
    	
    	$Album = $Albums->find_by_slug($slug);
    	if(is_object($Album)) {
	    	$album_array = $Album->to_array();
	    	
	    	if(is_object($Album)) {
	    		$Template = $API->get('Template');
		    	
	    		$Template->set('gallery/'.$opts['template'], 'gallery');
	    		
	    		$Versions = new PerchGallery_ImageVersions;
		    	$Versions->preload_for_album($Album->albumID());
	    		
		    	$list = $Images->get_custom($Album->albumID(), $opts, $Versions);
		    			    	
		    	if(PerchUtil::count($list)) {
		    	    foreach($list as &$item) {
		    	        $item = array_merge($album_array, $item);
		    	    }
		    	    
		    	    if ($opts['skip-template']) return $list;
		    	    
		    		$r = $Template->render_group($list, true);
		    		
		    		if($return) return $r;
    	    		echo $r;
    	    		return;
		    	}

	    	}
    	}
    	return false;
    }
    
    // alias
    function perch_gallery_album($slug, $opts=array(), $return=false) 
    {
        return perch_gallery_album_images($slug, $opts, $return);
    }

    function perch_gallery_album_details($slug, $opts=array(), $return=false) 
    {   
        $default_opts = array(
           'template'   =>'album.html',
           'skip-template'=>false
        );
        
        $opts = array_merge($default_opts, $opts);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);
        
        if ($opts['skip-template']) $return=true;
        
        $API = new PerchAPI(1.0, 'perch_gallery');
        
        $Albums = new PerchGallery_Albums($API);
        
        $Album = $Albums->find_by_slug($slug);
        if(is_object($Album)) {
            if ($opts['skip-template']) return $Album->to_array();

            $Template = $API->get('Template');
                
            $Template->set('gallery/'.$opts['template'], 'gallery');
                    
            $r = $Template->render($Album);
                
            if($return) return $r;
            echo $r;
            return;
        }

        return false;
    }

    function perch_gallery_album_field($slug, $field, $return=false)
    {
        $slug = rtrim($slug, '/');

        $API  = new PerchAPI(1.0, 'perch_gallery');
        
        $Albums = new PerchGallery_Albums($API);
        
        $Album = $Albums->find_by_slug($slug);
        
        $r = false;
        
        $encode = true;

        if (is_object($Album)) {
            $field = $Album->$field();
            if (is_array($field)) {
                if (isset($field['processed'])) {
                    $r = $field['processed'];
                    $encode = false;
                }elseif (isset($field['_default'])) {
                    $r = $field['_default'];
                }else{
                    $r = $field;
                }
            }else{
                $r = $field;
            }
        }
        
        if ($return) return $r;
        
        if ($encode) {
            $HTML = $API->get('HTML');
            echo $HTML->encode($r);
        }else{
            echo $r;
        }
        
    }



    function perch_gallery_images($opts=array(), $return=false)
    {
        $default_opts = array(
    	   'template'   =>'e_list_image.html',
    	   'skip-template'=>false
    	);
    	
    	$opts = array_merge($default_opts, $opts);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);
    	
    	if ($opts['skip-template']) $return=true;
    	
    	$API = new PerchAPI(1.0, 'perch_gallery');
    	
    	$Images = new PerchGallery_Images($API);
    	
        $Versions = new PerchGallery_ImageVersions;
        $Versions->preload_all();

        $list = $Images->get_custom(false, $opts, $Versions);
	    	
        if(is_array($list)) {
            if ($opts['skip-template']) return $list;
            
            $Template = $API->get('Template');
            $Template->set('gallery/'.$opts['template'], 'gallery');
                        
            $r = $Template->render_group($list, true);

            if($return) return $r;

            echo $r;
        }
        
        return false;
    }
    
    function perch_gallery_image($imageID, $opts=array(), $return=false)
    {
        $default_opts = array(
    	   'template' =>'b_static_image.html', 
    	   'skip-template'=>false
    	);
    	
    	$opts = array_merge($default_opts, $opts);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);
            
        $API    = new PerchAPI(1.0, 'perch_gallery');
        $Images = new PerchGallery_Images($API);
        $Image  = $Images->get_image_versions($imageID);
    	
    	if(is_object($Image)) {
    	    
    	    if ($opts['skip-template']) return $Image->to_array();
    	    
    		$Template = $API->get('Template');
    		$Template->set('gallery/'.$opts['template'], 'gallery');	
    		$r = $Template->render($Image, true);
    		
    		if($return) return $r;	
    		echo $r;
    	}
    }
    
    function perch_gallery_adjacent_images($imageID, $opts=array(), $return=false)
    {
        $default_opts = array(
    	   'template' =>'b_adjacent_images.html', 
    	   'skip-template'=>false
    	);
    	
    	$opts = array_merge($default_opts, $opts);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);
        
        $API = new PerchAPI(1.0, 'perch_gallery');
    	$Images = new PerchGallery_Images($API);
    	$images = $Images->get_next_and_previous_images($imageID);
    	
    	if (PerchUtil::count($images)) {

    	    $image_data = array();
    	    
    	    foreach($images as $prevnext=>$Image) {
    	        if(is_object($Image)) {
            		$data = $Image->to_array();
            		
            		foreach($data as $key=>$val) {
            		    $image_data[$prevnext.'-'.$key] = $val;
            		    if ($key=='_id') {
            		        $image_data[$prevnext.'-id'] = $val; // just to tidy up keys like "prev-_id" to "prev-id"
            		    }
            		}
            	}
    	    }
    	    
    	    if ($opts['skip-template']) return $image_data;
    	    
    	    $Template = $API->get('Template');
    		$Template->set('gallery/'.$opts['template'], 'gallery');

    		$r = $Template->render($image_data, true);

    		if($return) return $r;

    		echo $r;
    	}
        
    }
