<?php while (have_rows('slider')) : the_row(); ?>
   <div class="hero__slider--container btn-<?php the_sub_field('alineacion'); ?>">
      <div class="hero__principal" data-aos="fade" data-aos-duration="1000" style="background-image: url('<?php the_sub_field('imagen_de_fondo'); ?>');">
         <div class="hero__principal--desc">
            <?php if (get_sub_field('titulo')) : ?>
               <h2>
                  <?php the_sub_field('titulo'); ?>
               </h2>
            <?php endif; ?>

            <?php if (get_sub_field('boton')) : ?>
               <?php while (have_rows('boton')) : the_row(); ?>
                  <?php get_template_part('components/single/principal-btn'); ?>
               <?php endwhile; ?>
            <?php endif; ?>
         </div>
      </div>
   </div>
<?php endwhile; ?>