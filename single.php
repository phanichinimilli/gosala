<?php get_header(); ?>

<?php 
if($is_chrome) {
	echo "browser is chrome";
	if ( is_user_logged_in() ) { ?>
    <a href="<?php echo wp_logout_url(); ?>">Logout</a>
<?php }       
} 	

if(have_posts()) {
	?>
 	<div class="collumn main">
 	<?php
	while(have_posts()) { 
		the_post();		
		if(!is_page()) {
				?>								
				<div class="p_excerpt">
					<?php the_title(); the_content();  ?>					
				</div>
				<?php	
			}
		?> 
		   <br>		 

		<?php 
		
	}
	previous_post_link();
	next_post_link();
	?>
	
 	<?php
 
}
?>

</div> <!-- end collumn main -->
<div class="collumn side">
<?php get_sidebar(); ?>
</div>

</div> <!-- end row -->
</div> <!-- end content -->

<?php get_footer(); ?>


