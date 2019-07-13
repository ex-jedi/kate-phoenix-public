<?php
	require('../../../runtime.php');

	$Perch = Perch::fetch();

	PerchSession::commence();

	// HTTP headers for no cache etc
	header("Expires: Mon, 02 Jan 2013 00:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

    $albumID = (int) $_REQUEST["albumID"];
    
    $API        = new PerchAPI(1.0, 'perch_gallery');
    $Images     = new PerchGallery_Images($API);
    $Albums     = new PerchGallery_Albums($API); 
	
	// Settings
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

	

	$bucket = $Perch->get_resource_bucket($bucket_name);
	PerchUtil::initialise_resource_bucket($bucket);

	$targetDir = $bucket['file_path'];

	if (!file_exists($targetDir) || !is_writable($targetDir)) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 404, "message": "Unable to write to resource bucket, or bucket does not exist."}, "id" : "id"}');
	}

	// 10 minutes execution time
	@set_time_limit(10 * 60);

	// Get parameters
	$chunk    = isset($_REQUEST["chunk"]) ? (int) $_REQUEST["chunk"] : 0;
	$chunks   = isset($_REQUEST["chunks"]) ? (int) $_REQUEST["chunks"] : 0;
	$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

	$contentType = '';

	// Clean the fileName for security reasons
	$fileName = strtolower(preg_replace('/[^\w\._\-]+/', '', $fileName));
	$originalFileName = $fileName;

	/*
	    If this is the first chunk, check the file name is unique.
	    If it's not unique, generate a unique one.
	    Then store the file name in the session for future chunks to reference.
	*/

	if ($chunk === 0) {
	    if (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
	    	$ext = strrpos($fileName, '.');
	    	$fileName_a = substr($fileName, 0, $ext);
	    	$fileName_b = strtolower(substr($fileName, $ext));

	    	$count = 1;
	    	while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '-' . $count . $fileName_b))
	    		$count++;

	    	$fileName = $fileName_a . '-' . $count . $fileName_b;
	    }
	    PerchSession::set($originalFileName, $fileName);
	}else{
	    $fileName = PerchSession::get($originalFileName);
	}


	// add a _uploading_ prefix while we're uploading. We'll remove it later.
	$fileName = '_uploading_'.$fileName;


	// Look for the content type header
	if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
		$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

	if (isset($_SERVER["CONTENT_TYPE"]))
		$contentType = $_SERVER["CONTENT_TYPE"];

	// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
	if (strpos($contentType, "multipart") !== false) {
		if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
			// Open temp file
			$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen($_FILES['file']['tmp_name'], "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
				fclose($in);
				fclose($out);
				@unlink($_FILES['file']['tmp_name']);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	} else {
		// Open temp file
		$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
		if ($out) {
			// Read binary input stream and append it to temp file
			$in = fopen("php://input", "rb");

			if ($in) {
				while ($buff = fread($in, 4096))
					fwrite($out, $buff);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

			fclose($in);
			fclose($out);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	}

	// If it's the last chunk
	if ($chunks==0 || ($chunk == $chunks-1)) {
	    $newFileName = str_replace('_uploading_', '', $fileName);
	    rename($targetDir.DIRECTORY_SEPARATOR.$fileName, $targetDir.DIRECTORY_SEPARATOR.$newFileName);
	    

	    $Template   = $API->get('Template');
	    $Template->set('gallery/image.html', 'gallery');
	    
		$data                = array();
		$data['imageAlt']    = PerchUtil::strip_file_extension($newFileName);
		$data['albumID']     = $albumID;
		$data['imageStatus'] = 'uploading';
		$data['imageBucket'] = $bucket['name'];
		$Image               = $Images->create($data);
		
		if (is_object($Image)) {
		    $Image->process_versions($newFileName, $Template, $bucket);
		}
	    
	    PerchUtil::set_file_permissions($targetDir.DIRECTORY_SEPARATOR.$newFileName);
	    
	    $Image->update(array('imageStatus'=>'active'));

	    $Album = $Albums->find($albumID);
        if (is_object($Album)) $Album->update_image_count();

	    // file_put_contents(__DIR__.'/log.txt', strip_tags(PerchUtil::output_debug(true)));
	}


	// Return JSON-RPC response
	die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
