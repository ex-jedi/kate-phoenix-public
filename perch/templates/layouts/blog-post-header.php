<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="/css/universal.css">
		<link rel="stylesheet" href="/css/blog.css">
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
	 	<?php perch_blog_post_meta(perch_get('s'));

		perch_page_attributes(array(
			'template' => 'favicons.html'
		));

		?>
		<!-- Google Analytics -->
		<?php perch_content('Analytics'); ?>
		<!-- Pinterest Save Button -->
		<script async defer data-pin-hover="true" src="//assets.pinterest.com/js/pinit.js"></script>

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
	  <div class="wrapper blog-post-wrapper">
	    <header class="main-header blog-header">
			<a class="show-on-focus"  href="#main-content">Skip to main content</a>
				<div class="header-inner">
					<?php perch_content('Header Title'); ?>
					<nav class="main-nav">
						<?php perch_pages_navigation(array(
									'hide-extensions' => true,
							)); ?>
					</nav>
				</div>
				<?php perch_content('Blog Header Image'); ?>
	    </header>
