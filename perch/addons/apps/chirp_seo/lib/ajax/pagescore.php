<?php
  include('../../../../../core/inc/api.php');

  if (isset($_GET['url'])) {
    $url = urldecode($_GET['url']);
    $Seo_PageInsights = new ChirpSeo_PageInsights($url);

    $error = $Seo_PageInsights->errorMessage ?: '';
?>

<?php if ($Seo_PageInsights->get_score() != 0) { ?>
  <div class="pulse" data-status="<?php echo $Seo_PageInsights->get_traffic_light(); ?>">
    <div class="status">
    </div>
  </div>
  <h2><?php echo $Seo_PageInsights->get_title(); ?></h2>
  <p><?php echo $Seo_PageInsights->get_description(); ?></p>
<?php } else { ?>
  <div class="notification notification-warning"><?php echo PerchLang::get('%s. Please make sure a valid website URL in %ssettings%s.', $error, '<a href="' . PERCH_LOGINPATH . '/core/settings">', '</a>'); ?></div>
<?php } ?>

<?php
  }
?>