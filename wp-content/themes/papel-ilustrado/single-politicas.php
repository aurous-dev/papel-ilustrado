<?php

/* Template Name: Politica Single */

get_header();

?>
<div class="container-fluid policies">
   <?php
   $title = get_the_title();
   set_query_var('title', $title);
   get_template_part('components/single/title'); ?>
   <div class="container">
      <div class="row policies__container">
         <?php if (have_rows('politica')) : ?>
            <?php while (have_rows('politica')) : the_row();
               $rowNumber = get_row_index(); ?>
               <div class="col-md-6 policies__info">
                  <div class="policies__info--container">
                     <div class="policies__info--title">
                        <span class="number"><?php echo $rowNumber; ?>.</span>
                        <h2>
                           <?php the_sub_field('titulo'); ?>
                        </h2>
                     </div>
                     <div class="policies__info--desc">
                        <?php the_sub_field('descripcion'); ?>
                     </div>
                  </div>
               </div>
         <?php endwhile;
         endif; ?>
      </div>
   </div>
</div>

<?php get_footer(); ?>