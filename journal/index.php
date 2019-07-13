<?php include('../perch/runtime.php'); ?>
<?php perch_layout('blog-header'); ?>
  <main id="main-content" class="blog-main-content blog-index-main-content">
    <?php perch_blog_recent_posts(5); ?>
  </main>
<?php perch_layout('blog-footer'); ?>
