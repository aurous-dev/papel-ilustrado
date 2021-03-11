<div class="card__container--search">
   <div class="search__class">
      <?php
      if (get_field('tipo_de_producto') == 'simple' || !get_field('tipo_de_producto')) {
         echo 'Producto';
      } else {
         echo the_field('tipo_de_producto');
         $product = wc_get_product($post->ID);
         $children = $product->get_children();
         echo ' de ' .count($children);
      }; ?>
   </div>
   <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
   <!-- Esto es solo si es composición o serie -->
   <?php
   if (get_field('tipo_de_producto') != 'simple' || !get_field('tipo_de_producto')) : ?>
      <div class="search__buy">Puedes comprar indivual</div>
   <?php endif; ?>
   <!-- Esto es solo si es composición o serie -->
   <?php get_template_part('components/single/card-btn'); ?>
</div>