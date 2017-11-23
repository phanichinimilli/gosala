<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
		<title>Gosala</title>
<?php wp_head(); ?>
</head>
<div class="container">
<div  class ="log_in_out" style="display: inline; float: right;padding: 10px 17px;">
		<?php 
		wp_loginout(); 
		?>					
		</div>
<body <?php body_class(); ?> >
	<div class="site-header">
		
		<div class="title">
		<a href="<?php echo home_url(); ?>">
			<h1 style="text-align: center;">
				<!-- <a href="<?php /*echo home_url();*/ ?>">Gosala</a> -->
				Sri Sadguru Samartha Narayana Maharaj Ashram
			</h1>
			<h2 style="text-align: center;">
				PuranaPul,Jiyaguda, Hyderabad-500006,Telangana
			</h2>
			</a>
			
		</div>
		
		<?php if(has_nav_menu('primary')) { ?>	
			<nav class="site-nav">
				<?php wp_nav_menu(array('theme_location' => 'primary')) ?>				
			</nav>
			
		<?php } ?>
	</div>
		<div class="row">
			<div class="content">
