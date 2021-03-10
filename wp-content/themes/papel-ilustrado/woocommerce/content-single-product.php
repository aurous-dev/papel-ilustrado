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
<!-- Marco tester -->
<?php if (get_field('mostrar_marco_tester') == 'show') : ?>
	<div id="sigle-product-vue">
		<div class="obra-container__title col-12">
			<h3>Test de marcos</h3>
		</div>
		<div v-if="loading">
			<div style="text-align:center;margin:0 auto;">Cargando...</div>
		</div>
		<div v-else>
			<div class="row obra-container__price" :style="cssVars">
				<div class="obra-container__price__info col-lg-5 left">
					<div class="obra-container__price__info__cost">
						<h5 v-if="!selectedMarco.nombre_de_marco">Selecciona un marco clickeando en algún ícono de abajo.</h5>
						<h5 v-else>Tu marco seleccionado es: {{selectedMarco?.nombre_de_marco}}</h5>
					</div>
					<div v-if="selectedMarco.descripcion" class="obra-container__price__info__des">
						Descripción: {{selectedMarco?.descripcion}}
					</div>
					<div class="obra-container__price__info__frame">
						<div class="frame">
							<div>Selecciona un marco</div>
							<div class="frame-option">
								<div v-for="option in obra.marcos" :key="option.id">
									<button @click="changeFrame(option)">
										<img :src="option.icono" />
									</button>
								</div>
								<div>
									<button @click="changeFrame({})">
										<!-- Sin Marco -->
										<img src="<?php echo get_template_directory_uri(); ?>/img/png/no_marco.png" alt="">
									</button>
								</div>
							</div>
						</div>
					</div>
					<div class="obra-container__price__info__des">
						Ahora que probaste el marco que deseas seleccionalo antes de agregar al carrito.
					</div>
					<div class="obra-container__price__info__des">
						<a href="#" style="text-decoration:underline;">Ya probé el marco que quiero, ir a comprar</a>
					</div>
				</div>
				<div class="obra-container__price__img col-lg-7 right">
					<div v-if="obra.images" class="box__image">
						<div class="frame-picture" :style="cssVars" :class="Object.keys(selectedMarco).length === 0 ? 'none' : 'withFrame'">
							<img :src="obra.images[0].src" alt="">
							<div class="inner"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
<!-- Marco tester -->

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

			img1.src = "http://dev.trono.host/galerista/wp-content/uploads/2020/11/marcodorado-min2-min.png";
			img2.src = "http://dev.trono.host/galerista/wp-content/uploads/2020/11/Marco-negro-conpaspartu-min.png";
			img3.src = "http://dev.trono.host/galerista/wp-content/uploads/2020/11/marco-encajonado1-min.png";
			img4.src = "http://dev.trono.host/galerista/wp-content/uploads/2020/11/maco-negro-min.png";
			img5.src = "http://dev.trono.host/galerista/wp-content/uploads/2020/11/marco-madera-min.png";
			img6.src = "http://dev.trono.host/galerista/wp-content/uploads/2020/11/Marco-oroplata-conpaspartu-min.png";
			img7.src = "http://dev.trono.host/galerista/wp-content/uploads/2020/11/marco-oroplata-min.png";
			img8.src = "http://dev.trono.host/galerista/wp-content/uploads/2020/11/Marco-madera-conpaspartu-min.png";
		}
	</script>
</div>


<?php do_action('woocommerce_after_single_product'); ?>