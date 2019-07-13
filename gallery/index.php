<?php include('../perch/runtime.php'); ?>
<?php perch_layout('gallery-header'); ?>
<main id="main-content" class="main-content gallery-main-content">
		<article class="gallery-article">
		<?php
			perch_gallery_albums(array(
					'template'=>'gallery-index.html',
					'image'=>true,
					'count'=>5,
					'paginate'=>true
			));
		?>
	</article>
</main>
<?php perch_layout('gallery-footer'); ?>
