<?php
    echo $HTML->title_panel([
            'heading' => $Lang->get($heading1),
        ], $CurrentUser);
   
    
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
            ],
            [
                'title' => $Lang->get('Image'),
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
        $template_help_html = $Template->find_help();
        if ($template_help_html) {
            echo $HTML->heading2('Help');
            echo '<div id="template-help">' . $template_help_html . '</div>';
        }
    
        echo $HTML->heading2('Image details');
    
        echo $Form->form_start('content-edit');

        echo $Form->text_field('imageAlt', 'Title', isset($details['imageAlt'])?$details['imageAlt']:false);

		
		echo $Form->fields_from_template($Template, $details, $Images->static_fields);
	
        if(is_object($Image)) {
            //display admin thumb
            $PreviewVersion = $Image->get_version('admin_preview');
            $OriginalVersion = $Image->get_version('original');
            if(is_object($PreviewVersion)) {
                echo '<div class="field-wrap">
                        <a class="preview" href="'.$OriginalVersion->path().'">
                        <img src="'.$PreviewVersion->path().'" alt="'.$Image->imageAlt().'" height="'.$PreviewVersion->versionHeight().'" width="'.$PreviewVersion->versionWidth().'" />
                        </a>
                     </div>';
            }
        }

        echo $Form->image_field('upload', 'Image', false);

        echo $Form->text_field('imageOrder', 'Position in album', isset($details['imageOrder'])?$details['imageOrder']:false, 'order s input-simple');

        echo $Form->hidden('albumID', isset($details['albumID'])?$details['albumID']:false);
		echo $Form->hidden('imageID', isset($details['imageID'])?$details['imageID']:false);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path().'/images/?id='.$albumID);

    echo $Form->form_end();

