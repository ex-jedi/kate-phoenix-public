<?php

class PerchGallery_Image  extends PerchAPI_Base
{
    protected $table  = 'gallery_images';
    protected $pk     = 'imageID';
    
    public function update($data)
    {
        parent::update($data);
        return true;
    }
    
    /** 
     * 
     * remove images and image files for this set, in preparation to replace them.
     */
	public function delete_versions() {
		//get the files.
		$PerchGallery_ImageVersions = new PerchGallery_ImageVersions();
		
		$versions = $PerchGallery_ImageVersions->get_for_image($this->id());

        $Perch = Perch::fetch();

        $bucket = $Perch->get_resource_bucket($this->imageBucket());
		
		if(PerchUtil::count($versions)) {
			foreach($versions as $Version) {
				PerchUtil::debug('Checking:'.PerchUtil::file_path($bucket['file_path'].'/'.$Version->versionPath()));
				if(file_exists(PerchUtil::file_path($bucket['file_path'].'/'.$Version->versionPath()))) {
					PerchUtil::debug('Unlinking:'.PerchUtil::file_path($bucket['file_path'].'/'.$Version->versionPath()));
					unlink(PerchUtil::file_path($bucket['file_path'].'/'.$Version->versionPath()));
					
				}
			}
		
			$sql = 'DELETE FROM '.PERCH_DB_PREFIX.'gallery_image_versions WHERE imageID = '.$this->id();
		
			$this->db->execute($sql);
		}
	}
	
    public function delete() {
    	$this->delete_versions();
    	return parent::delete();
    }
    
	/**
     * 
     * Returns an image object for the version of an image in a set
     * @param string $version original|main|thumb|admin_thumb
     * @return object
     */
    public function get_version($versionKey) 
    {
    	$PerchGallery_ImageVersions = new PerchGallery_ImageVersions();
    	
    	$Version = $PerchGallery_ImageVersions->get_by_key($this->id(), $versionKey);
    	
    	return $Version;
    	
    }
    
    /**
     * 
     * returns an array of image objects for each image in the version
     * @param int $setID
     */
    public function get_versions_for_image($PerchGallery_ImageVersions=false) 
    {	
    	if ($PerchGallery_ImageVersions==false) $PerchGallery_ImageVersions = new PerchGallery_ImageVersions();
    	
    	$Versions = $PerchGallery_ImageVersions->get_for_image($this->id());
    	
    	return $Versions;
    }
    
    
    public function to_array($template_ids=false, $PerchGallery_ImageVersions=false)
    {
        $out = parent::to_array();
        
        if ($out['imageDynamicFields'] != '') {
            $dynamic_fields = PerchUtil::json_safe_decode($out['imageDynamicFields'], true);
            if (PerchUtil::count($dynamic_fields)) {
                foreach($dynamic_fields as $key=>$value) {
                    $out['perch_'.$key] = $value;
                }
            }
            $out = array_merge($dynamic_fields, $out);
        }
        
        $versions = $this->get_versions_for_image($PerchGallery_ImageVersions);
        
        if (PerchUtil::count($versions)) {

            $bucket_name = $this->details['imageBucket'];
            $Perch  = Perch::fetch();
            $bucket = $Perch->get_resource_bucket($bucket_name);


            foreach($versions as $Version) {
                $out[$Version->versionKey()] = $Version->path($bucket);
                $out[$Version->versionKey().'-w'] = $Version->versionWidth();
                $out[$Version->versionKey().'-h'] = $Version->versionHeight();
                $out[$Version->versionKey().'-id'] = $Version->versionID();
                $out[$Version->versionKey().'-key'] = $Version->versionKey();
            }
        }
        
        $out['_id'] = $this->id();
        
        return $out;        
    }
    
    
    /**
     * Take the original uploaded file and make all the different versions, based on the given template.
     *
     * @param string $filename 
     * @param string $Template 
     * @return void
     * @author Drew McLellan
     */
    public function process_versions($filename, $Template, $bucket)
    {
        $this->delete_versions();
        
        $result = false;
        
        $image_file = PerchUtil::file_path($bucket['file_path'].'/'.$filename);
        PerchUtil::debug('123: '. $image_file);
        
        if (!file_exists($image_file)) return false;
        
        $API = new PerchAPI(1.0, 'perch_gallery');
        $Image = $API->get('Image');
        
        $Versions = new PerchGallery_ImageVersions;
        
        $tags = $Template->find_all_tags();

        $Perch = Perch::fetch();
        
        if (!is_array($tags)) $tags = array();
        
        // add defaults we need for admin
        $tags[] = new PerchXMLTag('<perch:gallery id="image" type="image" />'); // default full size
        $tags[] = new PerchXMLTag('<perch:gallery id="image" type="image" width="150" height="150" density="2" crop="true" key="admin_thumb" />'); // admin thumb
        $tags[] = new PerchXMLTag('<perch:gallery id="image" type="image" width="180" density="2" key="admin_preview" />'); // admin preview
        
        if (PerchUtil::count($tags)) {
            foreach($tags as $Tag) {
                if ($Tag->id()=='image' && $Tag->type()=='image') {

                    //$bucket = $Perch->get_resource_bucket($Tag->bucket());

                  
                    $Image->reset_defaults();

                    if ($Tag->quality()) $Image->set_quality($Tag->quality());
                    if ($Tag->sharpen()) $Image->set_sharpening($Tag->sharpen());
                    if ($Tag->density()) $Image->set_density($Tag->density());
                    
                    if ($Tag->width() || $Tag->height()) {
                        $details = $Image->resize_image($image_file, $Tag->width(), $Tag->height(), $Tag->crop());
                    }else{
                        $details = array();
                        $details['file_name'] = $filename;
                        PerchUtil::debug('152: '. $filename);
                        
                        $info = getimagesize($image_file);
                        
                        if (is_array($info)) {
                            $details['w'] = $info[0];
                            $details['h'] = $info[1];
                        }
                    }
                                                    
                    
                    if ($details) {
                        $data = array();
                        $data['imageID'] = $this->id();
                        
                        if (strpos($details['file_name'], DIRECTORY_SEPARATOR)!==false) {
                            $parts = explode(DIRECTORY_SEPARATOR, $details['file_name']);
                            $details['file_name'] = array_pop($parts);
                        }
                        
                        $data['versionPath'] = $details['file_name'];
                        //PerchUtil::debug('167: '. $details['file_name']);
                        
                        if ($Tag->key()) {
                            $data['versionKey'] = $Tag->key();
                        }else{
                            $data['versionKey'] = $this->_generate_version_key($Tag->width(), $Tag->height());
                        }
                        

                        if ($Tag->crop()) {
                            $data['versionWidth'] = $Tag->width();
                            $data['versionHeight'] = $Tag->height();
                        }else{
                            $data['versionWidth'] = $details['w'];
                            $data['versionHeight'] = $details['h'];
                        }
                        
                        $Version = $Versions->create($data);
                        
                        if (is_object($Version)) $result = true;
                    }
                }
            }
        }
        
        return $result;
    }
    
    
    /**
     * Take a width and height and generate the version key from it.
     *
     * @param string $w 
     * @param string $h 
     * @return void
     * @author Drew McLellan
     */
    private function _generate_version_key($w, $h) 
    {
        $key = '';
        
        if ($w) {
            $key .= 'w'.$w;
        }
        
        if ($h) {
            $key .= 'h'.$h;
        }
        
        if ($key=='') {
            $key = 'original'; // not resized - the original image
        }
        
        return $key;
    }
}

?>