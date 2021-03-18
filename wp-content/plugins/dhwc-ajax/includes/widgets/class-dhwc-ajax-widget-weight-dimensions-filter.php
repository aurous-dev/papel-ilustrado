<?php
/**
 * Class DHWC_Ajax_Widget_Weight_Dimensions_Filter
 */
class DHWC_Ajax_Widget_Weight_Dimensions_Filter extends DHWC_Ajax_Widget {
	
	protected $weight_dimensions;
	
	public function __construct(){
		$this->widget_cssclass    = 'woocommerce widget_layered_nav dhwc_ajax_weight_dimensions_filter';
		$this->widget_description = __('Display product filter with Weight - Dimensions', 'dhwc-ajax');
		$this->widget_id          = 'dhwc_ajax_weight_dimensions_filter';
		$this->widget_name        = __('DHWC Ajax Product Weight - Dimensions Filter', 'dhwc-ajax');
		
		$suffix                   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'wc-jquery-ui-touchpunch', WC()->plugin_url() . '/assets/js/jquery-ui-touch-punch/jquery-ui-touch-punch' . $suffix . '.js', array( 'jquery-ui-slider' ), DHWC_AJAX_VERSION, true );
		wp_register_script( 'dhwc-ajax-slider', DHWC_AJAX_URL . '/assets/js/slider' . $suffix . '.js', array( 'jquery-ui-slider', 'wc-jquery-ui-touchpunch'), DHWC_AJAX_VERSION, true );
		wp_localize_script('dhwc-ajax-slider', 'dhwc_ajax_slider_params', array(
			'weight_unit'		=>dhwc_ajax_get_option( 'woocommerce_weight_unit' ),
			'dimension_unit'	=>dhwc_ajax_get_option( 'woocommerce_dimension_unit' )
		));
		parent::__construct();
	}
	
	public function update( $new_instance, $old_instance ) {
		$this->init_settings();
		return parent::update( $new_instance, $old_instance );
	}
	
	public function form( $instance ) {
		$this->init_settings();
		parent::form( $instance );
	}
	
	/**
	 * Init settings after post types are registered.
	 */
	public function init_settings() {
		$this->settings = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Filter Weight - Dimensions', 'dhwc-ajax' ),
				'label' => __( 'Title', 'dhwc-ajax' )
			),
			'filter_type' => array(
				'type'  	=> 'select',
				'label' 	=> __( 'Type', 'dhwc-ajax' ),
				'std'		=> 'weight',
				'options' 	=> dhwc_ajax_get_weight_dimensions_options()
			),
		);
	}
	
	public function widget( $args, $instance ) {
		global $wp;
		
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}
		
		$filter_type        = ! empty( $instance['filter_type'] ) ? sanitize_title( $instance['filter_type'] ) : '_weight';
		
		if(!wc_product_weight_enabled() && !wc_product_dimensions_enabled() ){
			return;
		}
		
		$product_count = $this->get_filtered_product($filter_type);
		$min    = floor( $product_count->min_value );
		$max    = ceil( $product_count->max_value );
		
		if ( $min === $max ) {
			return;
		}
		$filter_name = $filter_type.'_filter';
		$filtered = !empty($_GET[$filter_name]) ? array_map( 'absint', explode( '-', wp_unslash( $_GET[$filter_name] ) ) ) : array();
		$min_value = !empty($filtered) ? $filtered[0] : $min;
		$max_value = !empty($filtered) ? $filtered[1] : $max;
		
		$weight_dimensions_options = dhwc_ajax_get_weight_dimensions_options();
		
		$label = $weight_dimensions_options[$filter_type];
		
		$link = $this->get_current_page_url();
		$current_url = remove_query_arg($filter_name,$link);
		wp_enqueue_script('dhwc-ajax-slider');
		$this->widget_start($args, $instance);
		echo '<div class="dhwc-ajax__slider-filter is-filter-'.$filter_type.'">
				<div class="slider-filter__control" data-current_from="'.$min_value.'" data-current_to="'.$max_value.'" data-from="'.$min.'" data-to="'.$max.'" style="display:none;"></div>
				<div class="slider-filter__footer">
					<a class="button slider-filter__button" data-current_url="'.$current_url.'" data-filter_name="'.$filter_name.'" href="'.$link.'">'.__('Filter','dhwc-ajax').'</a>
					<div class="slider-filter__label" style="display:none;">
						' . $label . ': <span class="from"></span> &mdash; <span class="to"></span>
					</div>
					<div class="clear"></div>
				</div>
			</div>';
		$this->widget_end($args);
	}
	
	protected function get_filtered_product($type){
		global $wpdb;
		$prefix 	= dhwc_ajax_get_weight_dimensions_meta_prefix();
		$type 		= $prefix.$type;
		$tax_query  = WC_Query::get_main_tax_query();
		$meta_query = WC_Query::get_main_meta_query();
	
		$meta_query     = new WP_Meta_Query( $meta_query );
		$tax_query      = new WP_Tax_Query( $tax_query );
		
		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		$price_range_query = DHWC_Ajax_Query::get_product_price_range_query();
		
		$sql  = "SELECT min( FLOOR( postmeta.meta_value ) ) as min_value, max( CEILING( postmeta.meta_value ) ) as max_value FROM {$wpdb->posts} ";
		$sql .= " LEFT JOIN {$wpdb->postmeta} as postmeta ON {$wpdb->posts}.ID = postmeta.post_id " . $price_range_query['join'] . $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " WHERE {$wpdb->posts}.post_type IN ( '".implode( "','", array_map( 'esc_sql', $this->_get_filtered_product_types() ) )."' ) 
			AND {$wpdb->posts}.post_status = 'publish'
			AND postmeta.meta_key IN ('".$type."')
			AND postmeta.meta_value > ''";
		$sql .= $price_range_query['where'] . $tax_query_sql['where'] . $meta_query_sql['where'];

		$search = WC_Query::get_main_search_query_sql();
		if ( $search ) {
			$sql .= ' AND ' . $search;
		}
		
		if($onsale_sql = DHWC_Ajax_Query::get_product_sale_sql()){
			$sql .= ' AND ' . $onsale_sql;
		}
		
		if($featured_sql = DHWC_Ajax_Query::get_product_featured_sql()){
			$sql .= ' AND ' . $featured_sql;
		}

		$sql = apply_filters( 'dhwc_ajax_weight_dimensions_filter_sql', $sql, $meta_query_sql, $tax_query_sql );

		return $wpdb->get_row( $sql ); // @codingStandardsIgnoreLine
	}
	
}
add_action('widgets_init', function(){
	return register_widget("DHWC_Ajax_Widget_Weight_Dimensions_Filter");
});