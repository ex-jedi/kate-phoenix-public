<?php
    
    echo $HTML->title_panel([
        'heading' => $Lang->get('Uploading images'),
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

    echo $HTML->heading2('Upload images');
    
    $className = 'sectioned';

    if ($Settings->get('perch_gallery_basicUpload')->settingValue()) $className .= ' basic';

    echo $Form->form_start('imageupload', $className);
        $id = 'upload';
        $label = 'Image';
        
        echo $Form->image_field('upload', 'Image', false);
        echo $Form->hidden('albumID', $albumID);
        echo $Form->hidden('success', $API->app_path().'/images/?id='.$albumID.'&uploaded=true');
        echo $Form->submit_field('btnSubmit', 'Upload', $API->app_path().'/');
    echo $Form->form_end();

