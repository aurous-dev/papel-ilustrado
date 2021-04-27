<div class="card__container--img">
   <!-- <img src="<?php echo get_template_directory_uri(); ?>/img/png/picture1.png" alt=""> -->
   <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $loop->post->ID ), 'single-post-thumbnail' );?>
    <img src="<?php  echo $image[0]; ?>" data-id="<?php echo $loop->post->ID; ?>">
   <?php get_template_part('components/single/card-btn');?>
</div>