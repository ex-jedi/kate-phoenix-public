<?php
    
    $Albums     = new PerchGallery_Albums($API);
    $Images     = new PerchGallery_Images($API);
    $Versions   = new PerchGallery_ImageVersions($API);
    
    $Template   = $API->get('Template');
    $Template->set('gallery/image.html', 'gallery');
    
    if (!$CurrentUser->has_priv('perch_gallery.image.upload')) die('Your role does not have permission to upload images.');
    
    $message    = false;
        
    if (isset($_GET['album_id']) && $_GET['album_id']!='') {
        $albumID = (int) $_GET['album_id'];    
        $Album = $Albums->find($albumID);
    }
    

    $apps_php = file_get_contents(PerchUtil::file_path(PERCH_PATH.'/config/apps.php'));
    if (!strpos($apps_php, 'perch_gallery')) {
        $message = $HTML->warning_message('You need to add the Gallery app to your %sconfig/apps.php%s file.', '<code>', '</code>');    
    }

    
    $Form = $API->get('Form');
    
    if ($Form->submitted()) {

        $bucket_name = 'default'; 

        $Settings = PerchSettings::fetch();

        $bucket_mode = $Settings->get('perch_gallery_bucket_mode')->val();
        if ($bucket_mode == '') $bucket_mode = 'single';

        switch($bucket_mode) {
            case 'dynamic':
                $Album = $Albums->find($albumID);
                if (is_object($Album)) {
                    $bucket_name = $Album->albumSlug();
                } 
                break;
            default:
                $bucket_name = $Settings->get('perch_gallery_bucket')->val();
                break;
        }

        if ($bucket_name == '') $bucket_name = 'default';

        

        $Perch = Perch::fetch();

        $bucket = $Perch->get_resource_bucket($bucket_name);
        PerchUtil::initialise_resource_bucket($bucket);

        $targetDir = $bucket['file_path'];


        
        $image_folder_writable = is_writable($targetDir);
        $filesize = 0;
        
        if (isset($_FILES['upload'])) {
        	$file = $_FILES['upload']['name'];
        	$filesize = $_FILES['upload']['size'];
        }
    	
    	// if file is greater than 0 process it into resources
    	if($filesize > 0) {
    		
    		if($image_folder_writable && isset($file)) {
    			$filename = PerchUtil::tidy_file_name($file);
    			if(strpos($filename,'.php')!==false) $filename.='.txt'; //checking for naughty uploading of php files.
    			$target = PerchUtil::file_path($targetDir.'/'.$filename);
    			if(file_exists($target)) {
                    $ext = strrpos($filename, '.');
                    $fileName_a = substr($filename, 0, $ext);
                    $fileName_b = substr($filename, $ext);

                    $count = 1;
                    while (file_exists(PerchUtil::file_path($targetDir.'/'.$fileName_a.'_'.$count.$fileName_b))) {
                        $count++;
                    }

                    $filename = $fileName_a . '_' . $count . $fileName_b;
                    $target = PerchUtil::file_path($targetDir.'/'.$filename);
    			}
    		}
    		
    		PerchUtil::move_uploaded_file($_FILES['upload']['tmp_name'], $target);
    		
            $data                = array();
            $data['imageAlt']    = PerchUtil::strip_file_extension($filename);
            $data['albumID']     = $albumID;
            $data['imageStatus'] = 'uploading';
            $data['imageBucket'] = $bucket['name'];
            $Image               = $Images->create($data);
    		
    		if (is_object($Image)) {
    		    $Image->process_versions($filename, $Template, $bucket);
    		}
    		
            $Image->update(array('imageStatus'=>'active'));

            $Album = $Albums->find($albumID);
            if (is_object($Album)) $Album->update_image_count();
    				
    		
    	}
        
    }
    