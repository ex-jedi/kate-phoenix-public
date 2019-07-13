<?php

class PerchGallery_Images extends PerchAPI_Factory
{
    protected $table     = 'gallery_images';
	protected $pk        = 'imageID';
	protected $singular_classname = 'PerchGallery_Image';
	
	protected $default_sort_column = 'imageOrder';
	
	public $static_fields   = array('image', 'imageAlt', 'imageOrder', 'albumID');
    
    function __construct($api=false) 
    {
        $this->cache = array();
        parent::__construct($api);
    }
    
    /**
     * 
     * Gets all images linked to an album. If an array of versions is passed in also returns the Image Objects for those versions alongside the Image data.
     * If count is set this limits the set returned. If count is set to 1 returns a single Imagset object
     * @param int $albumID
     * @param array $versions
     * @param int $count 
     * @return array of objects
     */
    public function get_by_album_id($albumID, $versions=false, $count=false) 
    {
    	$sql = 'SELECT * FROM '.$this->table .' WHERE albumID='.$this->db->pdb($albumID).' AND imageStatus='.$this->db->pdb('active').' ORDER BY '.$this->default_sort_column.' ASC';
    	
    	if($count) {
    		$sql.= ' LIMIT '.$this->db->pdb($count);
    	}
    	
    	//if $count has been set to 1, we are only returning 1 image
    	if($count && $count == 1) {
    		$row = $this->db->get_row($sql);
    		
    		if(is_array($row)) {
            	/* if we have versions is array (otherwise we just return the image objects) */
		        if(is_array($versions) && PerchUtil::count($versions)>0) {
		        	$PerchGallery_ImageVersions = new PerchGallery_ImageVersions();
			        /* loop though versions array */
			        for($n=0;$n<sizeOf($versions);$n++) {
			        	/* add to row the image objects for versions in the array */
				        $Version = $PerchGallery_ImageVersions->get_by_key($row['imageID'], $versions[$n]);
				        $row['image_'.$versions[$n]] = $Version;
			        }
		        }
            	$r = $this->return_instance($row);
                return $r;
            }
            return false;
    	}else{
    	
    		$rows = $this->db->get_rows($sql);
        
            if(is_array($rows)) {
            	/* if we have versions is array (otherwise we just return the image objects) */
		        if(is_array($versions) && PerchUtil::count($versions)>0) {
		        	
			        	$PerchGallery_ImageVersions = new PerchGallery_ImageVersions();
			        	
			        	$PerchGallery_ImageVersions->preload_for_album($albumID);
			        	
			        	/* loop through returned images */
			        	for($i=0;$i<sizeOf($rows);$i++) {
			        		/* loop though versions array */
			        		for($n=0;$n<sizeOf($versions);$n++) {
			        			/* add to row the image objects for versions in the array */
				        		$Version = $PerchGallery_ImageVersions->get_by_key($rows[$i]['imageID'],$versions[$n]);
				        		$rows[$i]['image_'.$versions[$n]] = $Version;
			        		}
			        	}
			        
		        }
            	
            	
                $r = $this->return_instances($rows);
                return $r;
            }
    	}
        
        return false;
    }
    
    
    public function get_custom($albumID, $opts=array(), $Versions=false) 
    {
        if ($albumID===false) {
            $sql = 'SELECT i.* FROM '.$this->table .' i, '.PERCH_DB_PREFIX.'gallery_albums a WHERE i.albumID=a.albumID AND i.imageStatus='.$this->db->pdb('active').' ORDER BY a.albumOrder ASC, i.imageOrder ASC';
        }else{
            $sql = 'SELECT * FROM '.$this->table .' WHERE albumID = '.$this->db->pdb($albumID).' AND imageStatus='.$this->db->pdb('active').' ORDER BY '.$this->default_sort_column.' ASC';
        }
        
        $rows   = $this->db->get_rows($sql);
        $objects = $this->return_instances($rows);
        $content = array();
        if (PerchUtil::count($objects)) {
            foreach($objects as $Object) $content[] = $Object->to_array(false, $Versions);
        }
        
        // find specific _id
	    if (isset($opts['_id'])) {
	        if (PerchUtil::count($content)) {
	            $out = array();
	            foreach($content as $item) {
	                if (isset($item['_id']) && $item['_id']==$opts['_id']) {
	                    $out[] = $item;
	                    break;
	                }
	            }
	            $content = $out;
	        }   
	    }else{
	        // if not picking an _id, check for a filter
	        if (isset($opts['filter']) && isset($opts['value'])) {
	            if (PerchUtil::count($content)) {	                
    	            $out = array();
    	            $key = $opts['filter'];
    	            $val = $opts['value'];
    	            $match = isset($opts['match']) ? $opts['match'] : 'eq';
    	            foreach($content as $item) {
                        if (!isset($item[$key])) $item[$key] = false;
    	                if (isset($item[$key])) {
    	                    switch ($match) {
                                case 'eq': 
                                case 'is': 
                                case 'exact': 
                                    if ($item[$key]==$val) $out[] = $item;
                                    break;
                                case 'neq': 
                                case 'ne': 
                                case 'not': 
                                    if ($item[$key]!=$val) $out[] = $item;
                                    break;
                                case 'gt':
                                    if ($item[$key]>$val) $out[] = $item;
                                    break;
                                case 'gte':
                                    if ($item[$key]>=$val) $out[] = $item;
                                    break;
                                case 'lt':
                                    if ($item[$key]<$val) $out[] = $item;
                                    break;
                                case 'lte':
                                    if ($item[$key]<=$val) $out[] = $item;
                                    break;
                                case 'contains':
                                    $value = str_replace('/', '\/', $val);
                                    if (preg_match('/\b'.$value.'\b/i', $item[$key])) $out[] = $item;
                                    break;
                                case 'regex':
                                case 'regexp':
                                    if (preg_match($val, $item[$key])) $out[] = $item;
                                    break;
                                case 'between':
                                case 'betwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($item[$key]>trim($vals[0]) && $item[$key]<trim($vals[1])) $out[] = $item;
                                    }
                                    break;
                                case 'eqbetween':
                                case 'eqbetwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($item[$key]>=trim($vals[0]) && $item[$key]<=trim($vals[1])) $out[] = $item;
                                    }
                                    break;
                                case 'in':
                                case 'within':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)) {
                                        foreach($vals as $value) {
                                            if ($item[$key]==trim($value)) {
                                                $out[] = $item;
                                                break;
                                            }
                                        }
                                    }
                                    break;

    	                    }
    	                }
    	            }
    	            $content = $out;
    	        }
	        }
	    }
    
	    // sort
	    if (isset($opts['sort'])) {
	        if (isset($opts['sort-order']) && $opts['sort-order']=='DESC') {
	            $desc = true;
	        }else{
	            $desc = false;
	        }
	        $content = PerchUtil::array_sort($content, $opts['sort'], $desc);
	    }
    
	    if (isset($opts['sort-order']) && $opts['sort-order']=='RAND') {
            shuffle($content);
        }
    
        // Pagination
        if (isset($opts['paginate'])) {
            if (isset($opts['pagination-var'])) {
                $Paging = new PerchPaging($opts['pagination-var']);
            }else{
                $Paging = new PerchPaging();
            }
            
            $Paging->set_per_page(isset($opts['count'])?(int)$opts['count']:10);
            
            $opts['count'] = $Paging->per_page();
            $opts['start'] = $Paging->lower_bound()+1;
            
            $Paging->set_total(PerchUtil::count($content));
        }else{
            $Paging = false;
        }
                
        // limit
	    if (isset($opts['count']) || isset($opts['start'])) {

            // count
	        if (isset($opts['count'])) {
	            $count = (int) $opts['count'];
	        }else{
	            $count = PerchUtil::count($content);
	        }
            
	        // start
	        if (isset($opts['start'])) {
	            if ($opts['start'] === 'RAND') {
	                $start = rand(0, PerchUtil::count($content)-1);
	            }else{
	                $start = ((int) $opts['start'])-1; 
	            }
	        }else{
	            $start = 0;
	        }

	        // loop through
	        $out = array();
	        for($i=$start; $i<($start+$count); $i++) {
	            if (isset($content[$i])) {
	                $out[] = $content[$i];
	            }else{
	                break;
	            }
	        }
	        $content = $out;
	    }
        
	    // Paging to template
        if (is_object($Paging) && $Paging->enabled()) {
            $paging_array = $Paging->to_array($opts);
            // merge in paging vars
	        foreach($content as &$item) {
	            foreach($paging_array as $key=>$val) {
	                $item[$key] = $val;
	            }
	        }
        }
        
        return $content;
    }
    
    public function get_image_versions($imageID, $versions=false)
    {
        $sql = 'SELECT * FROM '.$this->table .' WHERE imageID='.$this->db->pdb($imageID);
		$row = $this->db->get_row($sql);
		
		if(is_array($row)) {
        	/* if we have versions is array (otherwise we just return the imageset objects) */
	        if(PerchUtil::count($versions)) {
	        	$PerchGallery_Images = new PerchGallery_Images();
		        /* loop though versions array */
		        for($n=0;$n<sizeOf($versions);$n++) {
		        	/* add to row the image objects for versions in the array */
			        $Image = $PerchGallery_Images->get_by_version($row['imageID'],$versions[$n]);
			        $row['image_'.$versions[$n]] = $Image;
		        }
	        }
        	$r = $this->return_instance($row);
            return $r;
        }
        return false;
    }
    
    public function get_next_and_previous_images($imageID, $versions=false) 
    {
        $sql = 'SELECT albumID FROM '.$this->table .' WHERE imageID='.$this->db->pdb($imageID);
		$albumID = $this->db->get_value($sql);
        
        $sql = 'SELECT imageID FROM '.$this->table .' WHERE albumID='.$this->db->pdb($albumID).'  AND imageStatus='.$this->db->pdb('active').' ORDER BY imageOrder';
        $rows = $this->db->get_rows($sql);
        
        $out = array();
        
        if (PerchUtil::count($rows)) {
            for($i=0; $i<PerchUtil::count($rows); $i++) {
                $row = $rows[$i];
                
                if ($row['imageID']==$imageID) {
                    
                    if ($i!=0) {
                        $out['prev'] = $this->get_image_versions($rows[$i-1]['imageID'], $versions);
                    }
                    
                    if (isset($rows[$i+1])) {
                        $out['next'] = $this->get_image_versions($rows[$i+1]['imageID'], $versions);
                    }
                    break;
                }
            }
        }
        
        return $out;
        
    }
    
    
    public function get_count_for_album($albumID)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->table .' WHERE albumID='.$this->db->pdb($albumID).' AND imageStatus='.$this->db->pdb('active');
        return $this->db->get_count($sql);
    }
    
	/**
     * 
     * Find an imageset using the slug
     * @param string $setSlug
     */
    public function find_by_slug($imageSlug) 
    {
       
        $sql = 'SELECT * FROM '.$this->table .' WHERE imageSlug='.$this->db->pdb($imageSlug).' AND imageStatus='.$this->db->pdb('active');
    
        $row = $this->db->get_row($sql);
    
        if(is_array($row)) {
            $r = $this->return_instance($row);
            return $r;
        }
        
        return false;
    }
    
	/**
     * 
     * Recursive function to check for unique slug
     * @param string $slug
     * @param int $int
     * @return slug | function 
     */
    private function check_slug_unique($slug, $int=0) {
    	
    	$obj = $this->find_by_slug($slug);
    	
    	if(is_object($obj)) {
    		$int++;
    		$newslug = $slug .'-'.$int;
    		return $this->check_slug_unique($newslug, $int);
    	}else{
    		return $slug;
    	}
    	
    }
    
    
    /**
     * overwriting create to add URL slug creation.
     * @see PerchFactory::create()
     * @param array $data
     * @return album object
     */
	public function create($data)
    {
        
        if (isset($data['imageAlt'])) {
            $data['imageSlug'] = PerchUtil::urlify($data['imageAlt']);
            $data['imageSlug'] = $this->check_slug_unique($data['imageSlug']);
        }

        if (!isset($data['imageOrder']) && isset($data['albumID'])) {
            $sql = 'SELECT COUNT(*) FROM '.$this->table.' WHERE albumID='.$this->db->pdb($data['albumID']);
            $count = $this->db->get_value($sql);
            $data['imageOrder'] = $count+1;
        }
        
        if($imageID = $this->db->insert($this->table, $data)) {
			return $this->find($imageID);
		}				
        return false;
	}
    

    public function get_recent_for_dashboard($count=4)
    {
        $sql = 'SELECT * FROM '.$this->table.' 
                WHERE imageStatus='.$this->db->pdb('active').' 
                ORDER BY imageID DESC LIMIT '.$count;

        $rows = $this->db->get_rows($sql);

        if (PerchUtil::count($rows)) {
            $PerchGallery_ImageVersions = new PerchGallery_ImageVersions();
            for($i=0;$i<count($rows);$i++) {
                /* loop though versions array */
                $Version = $PerchGallery_ImageVersions->get_by_key($rows[$i]['imageID'],'admin_preview');
                $rows[$i]['image_admin_preview'] = $Version;
            }
        }
        
        return $this->return_instances($rows);
    }
    
    
    
}
