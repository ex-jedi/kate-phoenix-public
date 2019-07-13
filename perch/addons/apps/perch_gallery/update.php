<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    $API = new PerchAPI(1.0, 'perch_gallery');

    $Settings = $API->get('Settings');

    if ($Settings->get('perch_gallery_update')->val()!='2.8.5') {
       
        $db = $API->get('DB');

        $sql = "ALTER TABLE `".PERCH_DB_PREFIX."gallery_images` ADD `imageStatus` ENUM('active', 'uploading', 'failed')  NOT NULL  DEFAULT 'active'  AFTER `imageDynamicFields`";
        $db->execute($sql);

        $sql = "ALTER TABLE `".PERCH_DB_PREFIX."gallery_images` ADD `imageBucket` VARCHAR(255)  NOT NULL  DEFAULT 'default'  AFTER `imageStatus`";
        $db->execute($sql);

        $sql = "ALTER TABLE `".PERCH_DB_PREFIX."gallery_albums` ADD `imageCount` INT(10)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `albumDynamicFields`";
        $db->execute($sql);

        $GalleryAlbums->update_image_counts();
            
        $UserPrivileges = $API->get('UserPrivileges');
        $UserPrivileges->create_privilege('perch_gallery', 'Access the gallery');
        $UserPrivileges->create_privilege('perch_gallery.album.create', 'Create albums');
        $UserPrivileges->create_privilege('perch_gallery.image.upload', 'Upload images');


        $Settings->set('perch_gallery_update', '2.8.5');

    }

?>