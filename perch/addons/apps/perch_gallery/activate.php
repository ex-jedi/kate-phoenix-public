<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    // Let's go
    $sql = "
    CREATE TABLE `__PREFIX__gallery_albums` (
      `albumID` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `albumTitle` varchar(255) NOT NULL DEFAULT '',
      `albumSlug` varchar(255) NOT NULL DEFAULT '',
      `albumOrder` int(11) NOT NULL DEFAULT '0',
      `albumDynamicFields` text,
      `imageCount` int(10) unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`albumID`),
      KEY `idx_slug` (`albumSlug`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
    
    CREATE TABLE `__PREFIX__gallery_images` (
      `imageID` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `imageAlt` varchar(255) NOT NULL DEFAULT '',
      `imageSlug` varchar(255) NOT NULL DEFAULT '',
      `albumID` int(10) unsigned NOT NULL DEFAULT '0',
      `imageOrder` int(10) unsigned NOT NULL DEFAULT '0',
      `imageDynamicFields` text,
      `imageStatus` enum('active','uploading','failed') NOT NULL DEFAULT 'active',
      `imageBucket` varchar(255) NOT NULL DEFAULT 'default',
      PRIMARY KEY (`imageID`),
      KEY `idx_albumID` (`albumID`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
    
    CREATE TABLE `__PREFIX__gallery_image_versions` (
      `versionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `versionPath` varchar(255) NOT NULL DEFAULT '',
      `versionKey` varchar(50) NOT NULL DEFAULT '',
      `versionWidth` int(10) unsigned NOT NULL DEFAULT '0',
      `versionHeight` int(10) unsigned NOT NULL DEFAULT '0',
      `imageID` int(10) unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`versionID`),
      KEY `idx_imageID` (`imageID`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;";
    
    $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);
    
    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $this->db->execute($statement);
    }
        
    $API = new PerchAPI(1.0, 'perch_gallery');
    $UserPrivileges = $API->get('UserPrivileges');
    $UserPrivileges->create_privilege('perch_gallery', 'Access the gallery');
    $UserPrivileges->create_privilege('perch_gallery.album.create', 'Create albums');
    $UserPrivileges->create_privilege('perch_gallery.image.upload', 'Upload images');


    $sql = 'SHOW TABLES LIKE "'.$this->table.'"';
    $result = $this->db->get_value($sql);
    
    return $result;

