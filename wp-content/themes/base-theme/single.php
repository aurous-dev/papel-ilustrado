<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage base_theme
 * @since Base theme 1.0
 */
  
get_header(); ?>


<?php while ( have_posts() ) : the_post(); ?>
<div class="body">
	<div class="container">
		<div class="clear"></div>
		<div class="main">
			<div class="post content">
				<h1 class="page-title"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h1>

				<div class="content">
					<?php the_content(); ?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php endwhile; ?>

<?php get_footer();?>