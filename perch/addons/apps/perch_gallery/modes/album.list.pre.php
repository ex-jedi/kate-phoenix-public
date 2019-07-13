<?php
    
	$GalleryAlbums = new PerchGallery_Albums($API);

    // Try to update
    if (file_exists('update.php')) include('update.php');
    
    $albums = $GalleryAlbums->all($Paging);
    
	
    // Install
    if ($albums == false) {
    	$GalleryAlbums->attempt_install();
    }
       
    