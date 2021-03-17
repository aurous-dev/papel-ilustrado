<?php
if(!$title) {
   $title = get_sub_field('titulo');
} else {
   $title = get_query_var('title');
}
?>
<div class="container">
   <div class="title">
      <?php if ($title) : ?>
         <h2>
            <?php echo $title; ?>
         </h2>
      <?php endif; ?>
      <?php if (have_rows('boton')) : ?>
         <?php while (have_rows('boton')) : the_row(); ?>
            <?php get_template_part('components/single/title-btn'); ?>
         <?php endwhile; ?>
      <?php endif; ?>
   </div>
</div>