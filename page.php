<?php 								
// We'll be outputting a PDF
/*ob_start();
header("Content-Type: application/pdf");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");    	
header("Content-Disposition:attachment;filename='downloaded.pdf'");
readfile("give.pdf");   				
ob_end_flush();*/
get_header();
?>

<div class="collumn main">

<?php 
if(have_posts()) {
	while(have_posts()) { 
		the_post();	
?>								
	<div class="p_excerpt">
	<?php the_content(); ?>
							
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


