<?php

    $Alert->set('info', $Lang->get('Drag and drop the albums to reorder them.'));

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all albums'),
        'button'  => [
            'text' => $Lang->get('Add album'),
            'link' => $API->app_nav().'/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_gallery.album.create',
        ],
    ], $CurrentUser);

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => false,
        'title' => $Lang->get('Albums'),
        'link'  => $API->app_nav(),
        'icon'  => 'assets/o-photo'
    ]);

    $Smartbar->add_item([
        'active' => true,
        'title' => $Lang->get('Reorder'),
        'link'  => $API->app_nav().'/reorder/',
        'icon'  => 'core/menu',
        'position' => 'end',
    ]);

    echo $Smartbar->render();
    
    if (PerchUtil::count($albums)) {

        echo '<div class="inner">';

    echo $Form->form_start('reorder', 'reorder');

    echo '<ol class="album-list basic-sortable sortable-tree">';

    foreach($albums as $Album) {
        
        echo '<li><div>';
            echo '<input type="text" name="a-'.$Album->id().'" value="'.$Album->albumOrder().'" />';
            echo PerchUI::icon('assets/o-photo');
            echo $HTML->encode($Album->albumTitle()).'</div>';
        echo '</li>';
        
    }

    echo '</ol>';

        echo $Form->hidden('orders', '');
        echo $Form->submit_field('reorder', 'Save Changes', $API->app_path());

        echo $Form->form_end();
        echo '</div>';

    }


