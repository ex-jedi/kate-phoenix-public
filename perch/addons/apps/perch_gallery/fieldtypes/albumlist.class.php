<?php
/**
 * A field type for selecting an album from the Gallery app.
 */
class PerchFieldType_albumlist extends PerchAPI_FieldType
{
    public function render_inputs($details=array())
    {
        $id  = $this->Tag->input_id();
        $val = '';
        
        if (isset($details[$id]) && $details[$id]!='') {
            $json = $details[$id];
            $val  = $json['albumSlug']; 
        }
        
        $API    = new PerchAPI(1, 'perch_gallery');
        $Albums = new PerchGallery_Albums($API);
        $albums = $Albums->return_all();

        $opts   = array();
        $opts[] = array('label'=>'', 'value'=>'');

        if (PerchUtil::count($albums)) {
            foreach($albums as $Album) {
                $opts[] = array('label'=>$Album->albumTitle(), 'value'=>$Album->albumSlug());
            }
        }
       
        if(PerchUtil::count($opts)) {
        	$s = $this->Form->select($id, $opts, $val);
        } else {
        	$s = '-';
        }
        
        return $s;
    }
       
    public function get_raw($post=false, $Item=false) 
    {
        $store  = array();
        $id     = $this->Tag->id();

        if ($post===false) $post = $_POST;
        
        if (isset($post[$id])) {
            $this->raw_item = trim($post[$id]);
            $store['albumSlug'] = $this->raw_item;
            $store['_default'] = $this->raw_item;
        }
        
        return $store;
    }
    
    public function get_processed($raw=false)
    {    
        if (is_array($raw) && isset($raw['albumSlug'])) { 
            return $raw['albumSlug'];
        }

        return $raw;
    }
    
    public function get_search_text($raw=false)
    {
		return false;
    }

}
