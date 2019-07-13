<?php
   
    $Responses = new PerchForms_Responses($API);
    $Forms = new PerchForms_Forms($API);

	
	$message = false;
	
	if (isset($_GET['id']) && $_GET['id']!='') {
        $Response = $Responses->find($_GET['id']);
	    $ResponseForm = $Forms->find($Response->formID());
	}else{
	    PerchUtil::redirect($API->app_path());
	}
    
    
    if (isset($_GET['file']) && $_GET['file']!='') {
        $files = $Response->files();
        $gf = $_GET['file'];
        $file = $files->$gf;
        
        if (file_exists($file->path)) {
            header('Content-Type: '.$file->mime);
            header('Content-Length: '.filesize($file->path));
            header('Content-Disposition: attachment;filename="'.$file->name.'"');
            $fp=fopen($file->path,'r');
            fpassthru($fp);
            fclose($fp);
            exit;
        }
    }
    
    
    $Form = $API->get('Form');
	if ($Form->submitted()) {
        if ($Response->responseSpam()) {
            $Response->mark_not_spam();
            $message = $HTML->success_message('Successfully marked as not being spam.');
        }else{
            $Response->mark_as_spam();
            $message = $HTML->success_message('Successfully marked as spam.');
        }
        
    }
