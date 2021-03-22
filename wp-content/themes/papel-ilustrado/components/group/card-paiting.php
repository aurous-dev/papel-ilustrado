<?php
$value = get_query_var('value');

$args = array(
   'post_type' => 'product',
   'posts_per_page' => 10,
   'meta_key'      => 'tipo_de_producto',
   'meta_value'   => 'serie'
);
$loop = new WP_Query($args);

while ($loop->have_posts()) : $loop->the_post(); ?>
   <a href="<?php the_permalink(); ?>" class="cards">
      <div class="card__container" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="<?php echo $value; ?>00">
         <?php get_template_part('components/single/card-img'); ?>
         <?php get_template_part('components/single/card-info'); ?>
      </div>
   </a>
<?php endwhile;
wp_reset_query(); // Remember to reset 
?>