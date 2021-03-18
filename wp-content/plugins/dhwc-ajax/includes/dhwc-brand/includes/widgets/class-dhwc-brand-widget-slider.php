<?php

class DHWC_Brand_Widget_Slider extends WC_Widget {
	public function __construct(){
		$this->widget_cssclass    = 'woocommerce dhwc_brand_widget_slider';
		$this->widget_description = __( 'Shows product brans with slider.', DHWC_BRAND_TEXT_DOMAIN );
		$this->widget_id          = 'dhwc_brand_widget_slider';
		$this->widget_name        = __( 'DHWC Brands Slider', DHWC_BRAND_TEXT_DOMAIN );
		$this->init_settings();
		
		if(!is_admin())
			wp_enqueue_style('dhwc-brand-slider',DHWC_BRAND_URL.'/assets/css/brand-slider.css');
		
		parent::__construct();
	}
	
	public function init_settings(){
		$this->settings = array(
			'title'              => array(
				'type'  => 'text',
				'std'   => __( 'Brands Slider', DHWC_BRAND_TEXT_DOMAIN ),
				'label' => __( 'Title', DHWC_BRAND_TEXT_DOMAIN ),
			),
			'orderby'            => array(
				'type'    => 'select',
				'std'     => 'name',
				'label'   => __( 'Order by', DHWC_BRAND_TEXT_DOMAIN ),
				'options' => array(
					'order' => __( 'Brand order', DHWC_BRAND_TEXT_DOMAIN ),
					'name'  => __( 'Name', DHWC_BRAND_TEXT_DOMAIN ),
				),
			),
			'hide_empty'         => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide empty brands', DHWC_BRAND_TEXT_DOMAIN ),
			),
		);
	}
	
	public function widget($args, $instance){
		
		$hide_empty         = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : $this->settings['hide_empty']['std'];
		$orderby            = isset( $instance['orderby'] ) ? $instance['orderby'] : $this->settings['orderby']['std'];
		if ($orderby === 'name'){
			$order = 'ASC';
		}else{
			$order = 'DESC';
		}
		$brands = get_terms(
			'product_brand',
			array(
				'hide_empty' => $hide_empty,
				'orderby' =>$orderby,
				'order' => $order )
		);
		
		if(empty($brands))
			return;
		
		$this->widget_start($args, $instance);
		wp_enqueue_script('flexslider',plugins_url('assets/js/flexslider/jquery.flexslider.min.js',WC_PLUGIN_FILE),array('jquery'),'2.7.2',true);
		wc_enqueue_js("
			var dhwc_brand_init_slider = function(){
				jQuery('.product-brand-slider').flexslider({
					animation: 'slide',
					controlNav: false,
					selector: '.product-brand-slider__slides > li',
					slideshow: false,
					nextText: '".'<svg aria-hidden="true" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512"><path fill="currentColor" d="M166.9 264.5l-117.8 116c-4.7 4.7-12.3 4.7-17 0l-7.1-7.1c-4.7-4.7-4.7-12.3 0-17L127.3 256 25.1 155.6c-4.7-4.7-4.7-12.3 0-17l7.1-7.1c4.7-4.7 12.3-4.7 17 0l117.8 116c4.6 4.7 4.6 12.3-.1 17z" class=""></path></svg>'."',
					prevText: '".'<svg aria-hidden="true" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512"><path fill="currentColor" d="M25.1 247.5l117.8-116c4.7-4.7 12.3-4.7 17 0l7.1 7.1c4.7 4.7 4.7 12.3 0 17L64.7 256l102.2 100.4c4.7 4.7 4.7 12.3 0 17l-7.1 7.1c-4.7 4.7-12.3 4.7-17 0L25 264.5c-4.6-4.7-4.6-12.3.1-17z"></path></svg>'."'
				});
			}
			dhwc_brand_init_slider();
		");
		?>
		<div class="product-brand-slider">
			<ul class="product-brand-slider__slides">
				<?php foreach ($brands as $brand){?>
				<li class="product-brand-slider__slide__item">
					<a title="<?php echo $brand->name; ?>" href="<?php echo get_term_link($brand->slug,'product_brand'); ?>">
						<?php woocommerce_subcategory_thumbnail( $brand) ?>
					</a>
					<h3><a title="<?php echo $brand->name; ?>" href="<?php echo get_term_link($brand->slug,'product_brand'); ?>"><?php echo $brand->name; ?></a></h3>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php 
		
		$this->widget_end($args);
	}
}
add_action('widgets_init', function(){
	return register_widget("DHWC_Brand_Widget_Slider");
});