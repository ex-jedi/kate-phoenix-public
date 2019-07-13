<?php

class PerchGallery_ImageVersion  extends PerchAPI_Base
{
    protected $table  = 'gallery_image_versions';
    protected $pk     = 'versionID';
    
    public function path($bucket=false)
    {
    	$file  		 = $this->details['versionPath'];

    	if ($bucket===false) {
    		$bucket_name = $this->details['imageBucket'];
    		$Perch = Perch::fetch();
    		$bucket = $Perch->get_resource_bucket($bucket_name);
    	}

    	return $bucket['web_path'].'/'.$file;

    }

}

?>