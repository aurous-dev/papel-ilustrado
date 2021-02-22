<div class="container">
   <div class="title">
      <?php if (get_sub_field('titulo')) : ?>
         <h2>
            <?php the_sub_field('titulo'); ?>
         </h2>
      <?php endif; ?>
      <?php if (have_rows('boton')) : ?>
         <?php while (have_rows('boton')) : the_row(); ?>
            <?php get_template_part('components/single/title-btn'); ?>
         <?php endwhile; ?>
      <?php endif; ?>
   </div>
</div>