<?php

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
        'active' => true,
        'title' => $Lang->get('Albums'),
        'link'  => $API->app_nav(),
        'icon'  => 'assets/o-photo'
    ]);

    $Smartbar->add_item([
        'active' => false,
        'title' => $Lang->get('Reorder'),
        'link'  => $API->app_nav().'/reorder/',
        'icon'  => 'core/menu',
        'position' => 'end',
    ]);

    echo $Smartbar->render();

    
    if (PerchUtil::count($albums)) {

        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

        $Listing->add_col([
                'title'     => $Lang->get('Album'),
                'value'     => 'albumTitle',
                'sort'      => 'albumTitle',
                'edit_link' => 'images',
            ]);

        $Listing->add_col([
                'title'     => $Lang->get('Slug'),
                'value'     => 'albumSlug',
                'sort'      => 'albumSlug',
            ]);

        $Listing->add_col([
                'title'     => $Lang->get('Images'),
                'value'     => 'imageCount',
                'sort'      => 'imageCount',
            ]);

        $Listing->add_delete_action([
                'priv'   => 'perch_gallery.album.delete',
                'inline' => true,
                'path'   => 'delete',
            ]);

        echo $Listing->render($albums);

    }
