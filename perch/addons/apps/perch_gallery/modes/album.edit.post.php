<?php  

    if ($albumID) {

        echo $HTML->title_panel([
            'heading' => $Lang->get($heading1),
            'button'  => [
                'text' => $Lang->get('Add image'),
                'link' => $API->app_nav().'/images/upload/?album_id='.$albumID,
                'icon' => 'core/plus',
            ],
        ], $CurrentUser);

    } else {

        echo $HTML->title_panel([
            'heading' => $Lang->get($heading1),
        ], $CurrentUser);


    }
     
    if (isset($message)) echo $message;

        $filter = 'options';

if (is_object($Album)) {        


    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => false,
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
        'active' => true,
        'title' => $Lang->get('Options'),
        'link'  => $API->app_nav().'/edit/?id='.$Album->id(),
        'icon'  => 'core/o-toggles',
    ]);

    echo $Smartbar->render();

}

        $template_help_html = $Template->find_help();
        if ($template_help_html) {
            echo $HTML->heading2('Help');
            echo '<div id="template-help">' . $template_help_html . '</div>';
        }

        echo $HTML->heading2('Album details');
        echo $Form->form_start('content-edit');
            echo $Form->text_field('albumTitle', 'Title', isset($details['albumTitle'])?$details['albumTitle']:false);
    		echo $Form->fields_from_template($Template, $details, $GalleryAlbums->static_fields);
            echo $Form->text_field('albumOrder', 'Position in album list', isset($details['albumOrder'])?$details['albumOrder']:'1', 'order s input-simple');
            echo $Form->hidden('albumID', isset($details['albumID'])?$details['albumID']:false);
            echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());
        echo $Form->form_end();
