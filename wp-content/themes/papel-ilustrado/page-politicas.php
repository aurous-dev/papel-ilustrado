<?php

/* Template Name: Politica */

get_header();

?>
<div class="container-fluid policies">
   <?php
   $title = get_the_title();
   set_query_var('title', $title);
   get_template_part('components/single/title'); ?>
   <div class="container">
      <div class="row policies__container">
         <?php if (have_rows('politicas')) : ?>
            <?php while (have_rows('politicas')) : the_row(); ?>
               <a href="<?php the_sub_field('url_destino'); ?>" class="policies__link col-6">
                  <div class="policies__link--div">
                     <?php the_sub_field('titulo'); ?>
                  </div>
               </a>
         <?php endwhile;
         endif; ?>
      </div>
   </div>
</div>

<?php get_footer(); ?>