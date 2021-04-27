<?php

/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div class="container">
	<div class="row simple-product__container">
		<div id="product-<?php the_ID(); ?>" <?php wc_product_class('', $product); ?>>

			<?php
			/**
			 * Hook: woocommerce_before_single_product_summary.
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action('woocommerce_before_single_product_summary');
			?>

			<div class="summary entry-summary">
				<?php
				/**
				 * Hook: woocommerce_single_product_summary.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 * @hooked WC_Structured_Data::generate_product_data() - 60
				 */
				do_action('woocommerce_single_product_summary');
				?>
			</div>
		</div>
	</div>
</div>



<!-- SECOND SECTION  -->
<section class="container-fluid paiting">
	<?php
	$title = 'Series';
	set_query_var('title', $title);
	get_template_part('components/single/title'); ?>
	<div class="container">
		<div class="paiting__slider">
			<?php get_template_part('components/group/card-paiting'); ?>
		</div>
	</div>
</section>

<!-- SECOND SECTION  -->
<!-- THIRD SECTION  -->
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
	<?php
	$title = 'Novedades';
	set_query_var('title', $title);
	get_template_part('components/single/title'); ?>
	<div class="container">
		<div class="picture__slider">
			<?php get_template_part('components/group/card-picture'); ?>
		</div>
	</div>
</section>
<!-- FOURTH SECTION  -->


<div class="hidden" style="display:none;">
	<script type="text/javascript">
		if (document.images) {
			img1 = new Image();
			img2 = new Image();
			img3 = new Image();
			img4 = new Image();
			img5 = new Image();
			img6 = new Image();
			img7 = new Image();
			img8 = new Image();
			img9 = new Image();
			img10 = new Image();

			img1.src = "http://aurouslabs.cl/papelilustrado/wp-content/uploads/2021/03/plateado-simple-min.png";
			img2.src = "http://aurouslabs.cl/papelilustrado/wp-content/uploads/2021/03/plateado-encajonado-min.png";
			img3.src = "http://aurouslabs.cl/papelilustrado/wp-content/uploads/2021/03/negro-simple-min.png";
			img4.src = "http://aurouslabs.cl/papelilustrado/wp-content/uploads/2021/03/negro-encajonado-min.png";
			img5.src = "http://aurouslabs.cl/papelilustrado/wp-content/uploads/2021/03/madera-simple-min.png";
			img6.src = "http://aurouslabs.cl/papelilustrado/wp-content/uploads/2021/03/madera-encajonado-min.png";
			img7.src = "http://aurouslabs.cl/papelilustrado/wp-content/uploads/2021/03/dorado-simple-min.png";
			img8.src = "http://aurouslabs.cl/papelilustrado/wp-content/uploads/2021/03/dorado-encajonado-min.png";
			img9.src = "http://aurouslabs.cl/papelilustrado/wp-content/uploads/2021/03/blanco-simple-min.png";
			img10.src = "http://aurouslabs.cl/papelilustrado/wp-content/uploads/2021/03/blanco-encajonado-min.png";
		}
	</script>
</div>


<?php do_action('woocommerce_after_single_product'); ?>