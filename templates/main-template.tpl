<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo $this->base_template_data['page_title'];?></title>
<meta name="Viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="preload" as="image" href="custom-images/t-logo-lg.png" >
<link rel="preload" as="style" href="<?php echo CONST_THEMES_CSS_PATH;?>bootstrap.min.css" >
<link rel="manifest" href="https://www.10X AIF.com/manifest.json">
<meta name="theme-color" content="#ff795c">
<!-- Start Favicon -->
<meta name="description" content="<?php echo array_key_exists('page_description', $this->base_template_data)?$this->base_template_data['page_description']:'';?>" />

<!-- <link rel="apple-touch-icon" sizes="180x180" href="<?php echo CONST_THEMES_CUSTOM_IMAGES_PATH; ?>favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo CONST_THEMES_CUSTOM_IMAGES_PATH; ?>favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo CONST_THEMES_CUSTOM_IMAGES_PATH; ?>favicon/favicon-16x16.png"> -->
<link rel="icon" type="image/jpeg"  href="<?php echo CONST_THEMES_CUSTOM_IMAGES_PATH; ?>favicon/fav.png">
<!--  End Favicon -->

<base href="<?php echo $this->base_template_data['base_url'];?>/"/>
<link href="<?php echo CONST_THEMES_CSS_PATH;?>bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo CONST_THEMES_CSS_PATH;?>jquery-ui.min.css" fetchpriority="low" />
<!-- <link rel="stylesheet" href="<?php echo CONST_THEMES_CSS_PATH;?>select2-4.0.5/select2.min.css" fetchpriority="low" /> -->
<link href="<?php echo CONST_THEMES_CUSTOM_CSS_PATH . 'custom.' . RESOURCE_VERSION . '.css';?>" rel="stylesheet" />
<!--   Including CSS in head  -->
<?php
	$mt_h_css_count=count($this->css_files);
	for($mt_h_css_i=0; $mt_h_css_i<$mt_h_css_count; $mt_h_css_i++){
		echo "<link rel='stylesheet' href=\"".$this->css_files[$mt_h_css_i]."\" />\n\t\t";
	}
?>
<!--  End of CSS Including in head -->

<!--   Including Javascripts before slash head  -->
<?php
	$mt_bsh_js_count=count($this->javascript_files_before_slash_head);
	for($mt_bsh_js_i=0; $mt_bsh_js_i<$mt_bsh_js_count; $mt_bsh_js_i++){
		echo "<script src=\"".$this->javascript_files_before_slash_head[$mt_bsh_js_i]."\" defer ></script>\n";
	}
?>
<!--  End of including before slash head Javascript  -->


<!-- Tracking codes  -->
<?php
	if(CONST_3RD_PARTY_TRACKING['gtm']===true){
?>
		<!-- Google tag (gtag.js) -->
			<script async src="https://www.googletagmanager.com/gtag/js?id=G-8WTD797QJP"></script>
			<script>
			  window.dataLayer = window.dataLayer || [];
			  function gtag(){dataLayer.push(arguments);}
			  gtag('js', new Date());

			  gtag('config', 'G-8WTD797QJP');
			</script>
		<!-- End of Google tag -->	
<?php		
	}	
?>	
<!-- End of tracking code -->

</head>
<?php
	$copyright_year_start = '2023';
	$copyright_year_end = date('Y');
	// $copyright_yr_text = $copyright_year_start . (($copyright_year_start === $copyright_year_end) ? '' : (' - ' . $copyright_year_end));
	$copyright_yr_text = $copyright_year_end;
?>
<body class="navbar-fixed">
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'navbar.tpl'; ?>
		<div class="main-container" id="main-container">
			<?php
				if($this->base_template_data['template_type']=='login' || $this->base_template_data['template_type']=='login_header'){
			?>
						<div class="login-layout" >
								<?php require_once $this->body_template; ?>
								<div class="copyright-ebizindia">&copy; Copyright: <?php echo $copyright_yr_text; ?> <a href='https://www.ebizindia.com/' target="_blank"  rel="noopener"  > Ebizindia Consulting</a></div>
						</div>
			<?php
				}else{
					// full template
			?>
					<div id="wrapper" class="main-container-inner">

						<div id="sidebar-wrapper">
							<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'sidebar.tpl'; ?>

						</div>

						<style>
							@media screen and (min-width:320px){
								.sponsor_add .dsk_img{
									display: none;
								}
								.sponsor_add .mob_img{
									display: block;
								}
								
							}

							@media screen and (min-width:768px){
								.sponsor_add .dsk_img{
									display: block;
								}
								.sponsor_add .mob_img{
									display: none;
								}
								
							}

						</style>	

						<div class="main-content container-fluid">
							<?php 
								if($this->base_template_data['show_sponsor_ad']){
							?>	
								<a href="https://www.ctracker.in" rel="noopener" target="_blank" class="sponsor_add_link">
								<div class="sponsor_add">
									<div >
									<img src="" class="img-responsive dsk_img" style="max-width:100%; height: auto;" alt="Sponsor ad here" width="" />
									<img src="" class="img-responsive mob_img" style="max-width:100%; height: auto;" alt="Sponsor ad here" width="" />

								</div>
								</div>
								</a>
							<?php 
								}
								require_once $this->body_template; 
							?>
						</div>

						<a href="#" id="btn-scroll-up" class="btn btn-sm btn-inverse d-none">
							<i class="fa fa-angle-double-up"></i>
						</a>


					</div>

			<?php

				}
			?>


		</div>

		<script src='<?php echo CONST_JAVASCRIPT_DIR;?>jquery-3.6.0.min.js'></script>
		<script defer src='<?php echo CONST_JAVASCRIPT_DIR;?>jquery-ui.js'></script>
		<script defer src="<?php echo CONST_JAVASCRIPT_DIR;?>moment.min.js" ></script>
		<script defer src="<?php echo CONST_JAVASCRIPT_DIR;?>jquery.hashchange-min.js" ></script>
		<script src="<?php echo CONST_JAVASCRIPT_DIR;?>bootstrap.min.js" ></script>
		
		<!--   Including Javascripts before slash body  -->
		<?php
			$mt_bsb_js_count=count($this->javascript_files_before_slash_body);
			for($mt_bsb_js_i=0; $mt_bsb_js_i<$mt_bsb_js_count; $mt_bsb_js_i++){
				echo "<script src=\"".$this->javascript_files_before_slash_body[$mt_bsb_js_i]."\" defer></script>\n";
			}
		?>
		<!--  End of including before slash body Javascript -->

		<iframe name="form_post_submit_target_window"  style="margin-left:300px;width:900px; height:200px; border:1px solid;display:none;">
		</iframe>

		<!-- Menu Toggle Script -->
	    <script>
		    $("#menu-toggle").click(function(e) {
		        e.preventDefault();
		        $("#wrapper").toggleClass("toggled");
		    });
	    </script>
	    <!-- Print Block-->
		<div id="print_view" class="print_window">

		</div>
		<!-- Print Block-->

		<div class="modal-backdrop fade show d-none"  id="common-processing-overlay"  ><span class="loading-text"><img src="images/preloader.gif" alt="Preloader"><br> <div class="loading-prefix-text"></div> Please wait ...</span></div>

		<!-- Js code to be executed on page load -->
		<script type='text/javascript' >
			<?php echo $this->base_template_data['other_js_code']; ?>

			$(document).ready(function(){
				<?php
				if(!is_array($this->base_template_data['dom_ready_code']))
					echo $this->base_template_data['dom_ready_code'];
				else
					echo implode("\n", $this->base_template_data['dom_ready_code']);
				?>

			});
		</script>
		<!-- End of page load code -->
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('https://www.10X AIF.com/service-worker.js')
      .then(reg => console.log('Service Worker registered:', reg.scope))
      .catch(err => console.error('Service Worker error:', err));
  }
</script>
</body>
</html>