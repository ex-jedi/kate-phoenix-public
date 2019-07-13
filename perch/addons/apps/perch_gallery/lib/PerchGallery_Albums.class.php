<?php

class PerchGallery_Albums extends PerchAPI_Factory
{
    protected $table     = 'gallery_albums';
	protected $pk        = 'albumID';
	protected $singular_classname = 'PerchGallery_Album';
	
	protected $default_sort_column = 'albumOrder';
	
	public $static_fields   = array('albumTitle', 'albumSlug', 'albumOrder');
    
    function __construct($api=false) 
    {
        $this->cache = array();
        parent::__construct($api);
    }
    

    /**
     * Return a list of all albums
     * @return array of Album objects
     */
    public function return_all()
    {
        $sql = 'SELECT * FROM '.$this->table.' ORDER BY '.$this->default_sort_column .' ASC';
        
        $rows   = $this->db->get_rows($sql);
              
        return $this->return_instances($rows);
    }
    
    
    public function get_custom($opts=array(), $as_objects=false)
    {
        $sql = 'SELECT * FROM '.$this->table.' ORDER BY '.$this->default_sort_column .' ASC';
        $rows   = $this->db->get_rows($sql);
        $objects = $this->return_instances($rows);
        $content = array();
        if (PerchUtil::count($objects)) {
            foreach($objects as $Object) $content[] = $Object->to_array();
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

        if ($as_objects) {
            return $this->return_instances($content);
        }
        
        return $content;
    }
	
    
    /**
     * 
     * Find an album using the slug
     * @param string $albumSlug
     */
    public function find_by_slug($albumSlug) 
    {  
        $sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'gallery_albums WHERE albumSlug= '.$this->db->pdb($albumSlug);
    
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
    private function check_slug_unique($slug,$int=0) 
    {	
    	$obj = $this->find_by_slug($slug);
    	
    	if(is_object($obj)) {
    		$int++;
    		$newslug = $slug .'-'.$int;
    		return $this->check_slug_unique($newslug,$int);
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
        if (isset($data['albumTitle'])) {
            $data['albumSlug'] = PerchUtil::urlify($data['albumTitle']);
            $data['albumSlug'] = $this->check_slug_unique($data['albumSlug']);
        }
        
        
        if (!isset($data['albumOrder']) || $data['albumOrder']=='') {
            $sql = 'SELECT COUNT(*) FROM '.$this->table;
            $count = $this->db->get_value($sql);
            $data['albumOrder'] = $count+1;
        }
        
        if($albumID = $this->db->insert($this->table, $data)) {
			return $this->find($albumID);
		}				
        return false;
	}
    
    
    public function update_image_counts()
    {
        $albums = $this->all();

        if (PerchUtil::count($albums)) {
            foreach($albums as $Album) {
                $Album->update_image_count();
            }
        }
    }
    
    
}

?>