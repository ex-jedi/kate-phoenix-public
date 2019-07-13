<?php include('../perch/runtime.php'); ?>
<?php perch_layout('gallery-album-header'); ?>
<main id="main-content" class="main-content album-main-content">
    <?php
    if(perch_get('s')) {
      perch_gallery_album_images(perch_get('s'), array(
               'template'   =>'gallery-page.html'
            ));
    } ?>
</main>
<?php perch_layout('gallery-footer'); ?>
