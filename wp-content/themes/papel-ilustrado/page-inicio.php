<?php

/* Template Name: Home Page */

get_header();

?>

<!-- FIRST SECTION  -->
<section class="container-fluid hero">
   <div class="container">
      <div class="hero__container">
         <div class="hero__slider">
            <?php if (have_rows('slider')) : ?>
               <?php get_template_part('components/group/hero-slider'); ?>
            <?php endif; ?>
         </div>
         <div class="row hero__card">
            <?php if (have_rows('3_cta')) : ?>
               <?php while (have_rows('3_cta')) : the_row();
                  $rowNumber = get_row_index() * 3; ?>
                  <div class="col-md-4 col-12 hero__card--container" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="<?php echo $rowNumber; ?>00" style="background-image: url('<?php the_sub_field('imagen_de_fondo'); ?>');">
                     <?php if (have_rows('boton')) : ?>
                        <?php while (have_rows('boton')) : the_row(); ?>
                           <?php get_template_part('components/single/principal-btn'); ?>
                        <?php endwhile; ?>
                     <?php endif; ?>
                  </div>
               <?php endwhile; ?>
            <?php endif; ?>
         </div>
      </div>
   </div>
</section>
<!-- FIRST SECTION  -->
<!-- SECOND SECTION  -->
<section class="container-fluid paiting">
   <?php if (have_rows('titulo_de_seccion_series')) : ?>
      <?php while (have_rows('titulo_de_seccion_series')) : the_row(); ?>
         <?php get_template_part('components/single/title'); ?>
      <?php endwhile; ?>
   <?php endif; ?>
   <div class="container">
      <div class="paiting__slider">
         <?php get_template_part('components/group/card-paiting'); ?>
      </div>
   </div>
</section>
<!-- SECOND SECTION  -->
<!-- THIRD SECTION  -->
<!-- <section class="container-fluid composition">
   <?php if (have_rows('titulo_de_seccion_composiciones')) : ?>
      <?php while (have_rows('titulo_de_seccion_composiciones')) : the_row(); ?>
         <?php get_template_part('components/single/title'); ?>
      <?php endwhile; ?>
   <?php endif; ?>
   <div class="container">
      <div class="composition__slider">
         <?php if (have_rows('composiciones_recomendadas')) : ?>

            <?php while (have_rows('composiciones_recomendadas')) : the_row(); 
            $rowNumber = get_row_index() * 3; ?>
               <a href="<?php the_sub_field('url_destino'); ?>" class="big-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="<?php echo $rowNumber; ?>00">
                  <div>
                     <div class="big-card__container">
                        <div class="big-card__container--img" style="background-image: url('<?php the_sub_field('imagen_de_fondo'); ?>');"></div>
                        <?php get_template_part('components/single/card-special'); ?>
                     </div>
                  </div>
               </a>
            <?php endwhile; ?>
         <?php endif; ?>
      </div>
   </div>
</section> -->
<section class="container-fluid composition">
	<?php
	$title = 'Composiciones';
	set_query_var('title', $title);
	get_template_part('components/single/title'); ?>
	<div class="container">
		<div class="composition__slider">
			<?php
			$value = get_query_var('value');

			$args = array(
				'post_type' => 'product',
				'posts_per_page' => 10,
				'meta_key'      => 'tipo_de_producto',
				'meta_value'   => 'composicion'
			);
			$loop = new WP_Query($args);

			while ($loop->have_posts()) : $loop->the_post(); ?>
				<a href="<?php the_permalink(); ?>" class="big-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="<?php echo $rowNumber; ?>00">
					<div>
						<div class="big-card__container">
							<div class="big-card__container--img" style="background-image: url('<?php global $product; echo wp_get_attachment_url( $product->get_image_id() ); ?>');"></div>
							<?php
							$title = get_the_title();
							$button = 'Ver más';
							set_query_var('title', $title);
							set_query_var('button', $button);
							get_template_part('components/single/card-special'); ?>
						</div>
					</div>
				</a>
			<?php endwhile;
			wp_reset_query(); // Remember to reset 
			?>
		</div>
	</div>
</section>
<!-- THIRD SECTION  -->
<!-- FOURTH SECTION  -->
<section class="container-fluid picture">
   <?php if (have_rows('titulo_de_seccion_individuales')) : ?>
      <?php while (have_rows('titulo_de_seccion_individuales')) : the_row(); ?>
         <?php get_template_part('components/single/title'); ?>
      <?php endwhile; ?>
   <?php endif; ?>
   <div class="container">
      <div class="picture__slider">
         <?php get_template_part('components/group/card-picture'); ?>
      </div>
   </div>
</section>
<!-- FOURTH SECTION  -->
<!-- FIIFTH SECTION  -->
<section class="instagram">
   <?php if (have_rows('titulo_de_seccion_ig')) : ?>
      <?php while (have_rows('titulo_de_seccion_ig')) : the_row(); ?>
         <?php get_template_part('components/single/title-Instagram'); ?>
      <?php endwhile; ?>
   <?php endif; ?>
   <div class="container">
      <div class="instagram__slider">
         <?php if (have_rows('instagram_shop')) : ?>
            <?php while (have_rows('instagram_shop')) : the_row(); ?>
               <?php get_template_part('components/group/card-instagram'); ?>
            <?php endwhile; ?>
         <?php endif; ?>
      </div>
   </div>
</section>
<!-- FIIFTH SECTION  -->

<?php get_footer(); ?>