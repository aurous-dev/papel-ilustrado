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
      <!-- <div class="row about__container">
         <div class="col-md-6 about__container--img">
            <img src="<?php echo get_template_directory_uri(); ?>/img/png/nosotros.png" alt="">
         </div>
         <div class="col-md-6 about__container--info">
            <h3>¿A Dónde deseas enviar?</h3>
            <div>
               Puedes modifcar el tamaño y marco de cada cuadro o eliminar cuadros. Recuerda que esta personalización cambiará el look de cada cuadro y puede no ser similar al diseño original de la serie. Puedes modifcar el tamaño y marco de cada cuadro o eliminar cuadros. Recuerda que esta personalización cambiará el look de cada cuadro y puede no ser similar al diseño original de la serie.
               <br><br>
               Puedes modifcar el tamaño y marco de cada cuadro o eliminar cuadros. Recuerda que esta personalización cambiará el look de cada cuadro y puede no ser similar al diseño original de la serie.
            </div>
         </div>
      </div> -->
   </div>
</div>

<?php get_footer(); ?>