<?php
/**
 * The template for displaying all single posts and attachments 
 * Template Name: default posts
 * @package WordPress
 * @subpackage Gosala 
 * @since Gosala 1.0
 */
 get_header(); ?>

<div class="collumn main">

<?php 
if(have_posts()) {
	while(have_posts()) { 
		the_post();	
?>								
				<div class="p_excerpt">
					<?php 
						the_title(); 
						the_excerpt(); 
					 ?>					
				</div>				
		   <br>		   
		<?php 
	}
	echo paginate_links();		
}
?>

</div> <!-- end collumn main -->
<div class="collumn side">
<?php get_sidebar(); ?>
</div>

</div> <!-- end row -->
</div> <!-- end content -->

<?php get_footer(); ?>


