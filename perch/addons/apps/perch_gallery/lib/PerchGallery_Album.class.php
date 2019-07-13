<?php

class PerchGallery_Album  extends PerchAPI_Base
{
    protected $table  = 'gallery_albums';
    protected $pk     = 'albumID';
    
    public function update($data)
    {
        parent::update($data);
        return true;
    }
    
    
 	/**
 	 * deleting an album also needs to delete imagesets and images.
 	 * @see PerchBase::delete()
 	 */
    public function delete() {
    	
    	//get imagesets for this album
    	$PerchGallery_Images = new PerchGallery_Images();
    	$images = $PerchGallery_Images->get_by_album_id($this->id());
    	
    	if(is_array($images)) {
    		foreach($images as $Image) {
    			//call the delete method which also deletes any versions for this image
    			$Image->delete();
    		}
    	}
    	
    	parent::delete();
    	
    	return true;
    }
    
  	public function to_array()
  	{
        $out = parent::to_array();
    
  	    if ($out['albumDynamicFields'] != '') {
            $dynamic_fields = PerchUtil::json_safe_decode($out['albumDynamicFields'], true);
            if (PerchUtil::count($dynamic_fields)) {
                foreach($dynamic_fields as $key=>$value) {
                    $out['perch_'.$key] = $value;
                }
            }
            $out = array_merge($dynamic_fields, $out);
        }
        
        return $out;
  	}
   
   
    public function get_image_count()
    {
        $PerchGallery_Images = new PerchGallery_Images();
        return $PerchGallery_Images->get_count_for_album($this->id());
    }

    public function update_image_count()
    {
        $this->update(array(
            'imageCount'=>$this->get_image_count()
            ));
    }
}

?>