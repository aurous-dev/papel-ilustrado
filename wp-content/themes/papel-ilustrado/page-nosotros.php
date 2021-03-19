<?php

/* Template Name: Nosotros */

get_header();

?>
<div class="container-fluid about">
   <?php get_template_part('components/single/title'); ?>
   <div class="container">
      <div class="about__hero">
         <div class="about__hero--img">
            <img src="<?php the_field('imagen_principal'); ?>" alt="Papel Ilustrado">
         </div>
         <div class="about__hero--desc">
            <?php the_field('descripcion_inicial'); ?>
         </div>
      </div>
      <?php if (have_rows('secciones')) : ?>
         <?php while (have_rows('secciones')) : the_row(); ?>
            <div class="row about__container">
               <div class="col-md-6 about__container--img">
                  <img src="<?php the_sub_field('imagen'); ?>" alt="">
               </div>
               <div class="col-md-6 about__container--info">
                  <h3><?php the_sub_field('titulo'); ?></h3>
                  <div>
                     <?php the_sub_field('descripcion'); ?>
                  </div>
               </div>
            </div>
      <?php endwhile;
      endif; ?>
   </div>
</div>

<?php get_footer(); ?>