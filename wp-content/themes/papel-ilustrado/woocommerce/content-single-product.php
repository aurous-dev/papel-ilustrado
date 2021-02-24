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

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div class="container">
	<div class="row simple-product__container">
		<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
	
			<?php
			/**
			 * Hook: woocommerce_before_single_product_summary.
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
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
				do_action( 'woocommerce_single_product_summary' );
				?>
			</div>
		</div>
	</div>
</div>
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
<section class="container-fluid composition">
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


<?php do_action( 'woocommerce_after_single_product' ); ?>
