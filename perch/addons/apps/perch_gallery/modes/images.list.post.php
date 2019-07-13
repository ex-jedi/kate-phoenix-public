<?php  

    echo $HTML->title_panel([
        'heading' => $Lang->get('Editing ‘%s’', $HTML->encode($Album->albumTitle())),
        'button'  => [
            'text' => $Lang->get('Add image'),
            'link' => $API->app_nav().'/images/upload/?album_id='.$albumID,
            'icon' => 'core/plus',
            'priv' => 'perch_gallery.image.upload',
        ],
    ], $CurrentUser);

    if (!PerchUtil::count($images)) {
        echo $HTML->warning_message('Start by %sadding some images%s to your album', '<a href="'.$HTML->encode($API->app_path().'/images/upload/?album_id='.$albumID).'" class="notification-link">', '</a>');
    }
    
    if ($message) echo $message; 
    

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => true,
        'type' => 'breadcrumb',
        'links' => [
            [
                'title' => $Lang->get('Albums'),
                'link'  => $API->app_nav(),
            ],
            [
                'title' => $Album->albumTitle(),
                'link'  => $API->app_nav().'/images/?id='.$Album->id(),
            ]
        ]
        
    ]);

    $Smartbar->add_item([
        'active' => false,
        'title' => $Lang->get('Options'),
        'link'  => $API->app_nav().'/edit/?id='.$Album->id(),
        'icon'  => 'core/o-toggles',
    ]);

    echo $Smartbar->render();


    
    if (PerchUtil::count($images)) {

    echo $HTML->heading2('Album images');

    echo $Form->form_start();

    echo '<ul class="image-list reorder" data-albumid="'.$albumID.'">';

    foreach($images as $Image) {

        if (is_object($Image)) {
            $admin_thumb = $Image->image_admin_thumb();
            if (is_object($admin_thumb)) {
?>
    <li data-id="<?php echo $Image->id(); ?>">
	    <a href="<?php echo $HTML->encode($API->app_path()); ?>/images/edit/?album_id=<?php echo $HTML->encode(urlencode($albumID)); ?>&amp;id=<?php echo $HTML->encode(urlencode($Image->id())); ?>" class="img">
    	    <?php 
    	        if (is_object($Image)) {
                    echo '<img src="'.$admin_thumb->path().'" alt="'.$HTML->encode($Image->imageAlt()).'" width="120" height="120" />';
    	        }
    	    ?>
    	</a>
    	<input type="checkbox" name="batch[]" value="<?php echo $Image->id(); ?>" />
    </li>
<?php   
            } // is object admin thumb
        } // is object Image

    }
    echo '</ul>';
        
        
        $opts = array();
        $opts[] = array('label'=>$Lang->get('With selected'), 'value'=>'');
        $opts[] = array('label'=>$Lang->get('Delete'), 'value'=>'delete');



        echo '<div class="controls" id="gallery-controls">';

        echo $Form->submit_field('btnSubmit', 'Submit', false, 'button button-small action-info', $Form->select('action', $opts, '', ''));

        echo '</div>';


        #echo $Form->field_start('action');
        #echo $Form->label('action', 'With selected images');
        #echo $Form->select('action', $opts, false);
        #echo $Form->submit('btnSubmit', 'Submit', 'delete');
        #echo $Form->field_end('action');
        
        echo $Form->form_end();

        
    }
    
