<?php

	return; 

	
    $API   = new PerchAPI(1.0, 'perch_gallery');
    $Lang  = $API->get('Lang');
    $Albums = new PerchGallery_Albums($API);

    $albums = $Albums->return_all();

    $Images = new PerchGallery_Images($API);

    $images = $Images->get_recent_for_dashboard(5);
?>
<div class="widget">
	<h2>
		<?php echo $Lang->get('Gallery'); ?>
		<a href="<?php echo PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_gallery/edit/'); ?>" class="add button"><?php echo $Lang->get('Add Album'); ?></a>
	</h2>
	<div class="bd">
		<?php
			if (PerchUtil::count($images)) {
				echo '<div class="fig">';
				foreach($images as $Image) {
					if (is_object($Image)) {
            			$admin_thumb = $Image->image_admin_preview();
            			if (is_object($admin_thumb)) {
							echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_gallery/images/edit/?album_id='.$Image->albumID().'&id='.$Image->id()).'">';
							echo '<img src="'.$admin_thumb->path().'" alt="'.PerchUtil::html($Image->imageAlt()).'" />';
							echo '</a>';
						}
					}
				}
				echo '</div>';
			}

			if (PerchUtil::count($albums)) {
				echo '<ul>';
				foreach($albums as $Album) {
					echo '<li>';
						echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_gallery/images/?id='.$Album->id()).'">';
							echo PerchUtil::html($Album->albumTitle());
						echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			}

			
		?>
	</div>
</div>