<!DOCTYPE html>
<html lang="en-GB">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="/css/universal.css">
	<link rel="stylesheet" href="/css/gallery.css">
	<!-- Typekit -->
	<script>
	  (function(d) {
	    var config = {
	      kitId: 'kbq2qtf',
	      scriptTimeout: 3000,
	      async: true
	    },
	    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
	  })(document);
	</script>
	<!-- Perch Meta -->
	<?php
$domain        = 'https://'.$_SERVER["HTTP_HOST"];
$hard_gallery_domain = 'https://katephoenix.uk/gallery/';
$mainurl           = perch_page_url(array(
									    'hide-extensions'    => true,
									    'hide-default-doc'   => true,
									    'add-trailing-slash' => false,
									    'include-domain'     => true,
									), true);
$slug = perch_gallery_album_field(perch_get('s'), 'albumSlug', true);
$mainsitename  = "Kate Phoenix Art";
$pagetitlename = " - Kate Phoenix Art";
$sharing_image =  perch_gallery_album_images(perch_get('s'), array(
																'template' =>'sharing-image.html',
																'count' => 1,
															), true);
$gallery_title = perch_gallery_album_field(perch_get('s'), 'albumTitle', true);
$gallery_description = perch_gallery_album_field(perch_get('s'), 'description', true);
$twitterName = perch_gallery_album_field(perch_get('s'), 'twitterName', true);
$albumAvailability = perch_gallery_album_field(perch_get('s'), 'albumAvailability', true);
$albumCurrency = perch_gallery_album_field(perch_get('s'), 'albumCurrency', true);
$albumPrice = perch_gallery_album_field(perch_get('s'), 'albumPrice', true);
$ogType = perch_gallery_album_field(perch_get('s'), 'ogType', true);


PerchSystem::set_var('domain',$domain);
PerchSystem::set_var('hard_gallery_domain',$hard_gallery_domain);
PerchSystem::set_var('mainsitename',$mainsitename);
PerchSystem::set_var('mainurl',$mainurl);
PerchSystem::set_var('pagetitlename',$pagetitlename);
PerchSystem::set_var('sharing_image',$sharing_image);
PerchSystem::set_var('gallery_title',$gallery_title);
PerchSystem::set_var('gallery_description',$gallery_description);
PerchSystem::set_var('slug',$slug);
PerchSystem::set_var('twitterName',$twitterName);
PerchSystem::set_var('albumPrice',$albumPrice);
PerchSystem::set_var('albumCurrency',$albumCurrency);
PerchSystem::set_var('albumAvailability',$albumAvailability);
PerchSystem::set_var('ogType',$ogType);




perch_page_attributes(array(
	'template' => 'gallery/gallery.html'
));
?>
<?php perch_content('Analytics'); ?>

<!-- Cookie Consent -->
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js"></script>
	<script>
	window.addEventListener("load", function(){
	window.cookieconsent.initialise({
		"palette": {
			"popup": {
				"background": "#000"
			},
			"button": {
				"background": "transparent",
				"text": "#fff",
				"border": "#fff"
			}
		},
		"content": {
			"href": "/privacy-and-cookie-policy"
		}
	})});
	</script>
</head>
<body>
	<?php perch_content('Google Noscript Tag'); ?>
  <div class="wrapper gallery-wrapper">
    <header class="main-header gallery-header">
		<a class="show-on-focus"  href="#main-content">Skip to main content</a>
			<div class="header-inner">
				<?php perch_content('Header Title'); ?>
				<nav class="main-nav">
					<?php perch_pages_navigation(array(
								'hide-extensions' => true,
						)); ?>
				</nav>
			</div>
		</header>
