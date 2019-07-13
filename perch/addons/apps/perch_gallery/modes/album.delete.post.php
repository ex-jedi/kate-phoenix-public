<?php
   
    echo $HTML->title_panel([
        'heading' => $Lang->get('Deleting an album'),
    ], $CurrentUser);


    echo $Form->form_start();
    
    if ($message) {
        echo $message;
    }else{
        echo $HTML->warning_message('Are you sure you wish to delete “%s”? This will also remove all images contained within it.', $details['albumTitle']);
        echo $Form->form_start();
        echo $Form->hidden('albumID', $details['albumID']);
		echo $Form->submit_field('btnSubmit', 'Delete', $API->app_path());


        echo $Form->form_end();
    }
    
