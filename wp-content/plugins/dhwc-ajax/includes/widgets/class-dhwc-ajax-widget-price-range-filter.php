<?php
/**
 * Class DHWC_Ajax_Widget_Price_Range_Filter
 */
class DHWC_Ajax_Widget_Price_Range_Filter extends DHWC_Ajax_Widget {
	
	public function __construct(){
		$this->widget_cssclass    = 'woocommerce widget_layered_nav dhwc_ajax_price_filter';
		$this->widget_description = __('Display a price range to filter products in your store', 'dhwc-ajax');
		$this->widget_id          = 'dhwc_ajax_price_filter';
		$this->widget_name        = __('DHWC Ajax Price Range Filter', 'dhwc-ajax');
		$this->init_settings();
		parent::__construct();
	}
	
	public function update( $new_instance, $old_instance ) {
		return parent::update( $new_instance, $old_instance );
	}
	
	public function form( $instance ) {
		parent::form( $instance );
	}
	
	/**
	 * Init settings after post types are registered.
	 */
	public function init_settings() {
		$this->settings = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Filter by', 'dhwc-ajax' ),
				'label' => __( 'Title', 'dhwc-ajax' )
			),
			'price_range_size' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 20,
				'label' => __( 'Price range size', 'dhwc-ajax' )
			),
			'max_price_ranges' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 10,
				'label' => __( 'Max price ranges', 'dhwc-ajax' )
			),
			'hide_empty_ranges' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Hide empty price ranges', 'dhwc-ajax' )
			),
			'show_count'     => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show product counts', 'dhwc-ajax' ),
			)
		);
	}
	
	public function widget( $args, $instance ) {
		global $wp;
		
		// Requires lookup table added in 3.6.
		if ( version_compare( get_option( 'woocommerce_db_version', null ), '3.6', '<' ) ) {
			return;
		}
		
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}
		
		// If there are not posts and we're not filtering, hide the widget.
		if ( ! WC()->query->get_main_query()->post_count && ! isset( $_GET['min_price'] ) && ! isset( $_GET['max_price'] ) ) { // WPCS: input var ok, CSRF ok.
			return;
		}
		
		$show_count = isset( $instance['show_count'] ) ? (int) $instance['show_count'] : $this->settings['show_count']['std'];
		
		$hide_empty_ranges = isset( $instance['hide_empty_ranges'] ) ? (int) $instance['hide_empty_ranges'] : $this->settings['hide_empty_ranges']['std'];
		
		$step = 1;
		
		// Find min and max price in current result set.
		$prices    = $this->get_filtered_price();
		$min_price = $prices->min_price;
		$max_price = $prices->max_price;
		
		// Check to see if we should add taxes to the prices if store are excl tax but display incl.
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		
		if ( wc_tax_enabled() && ! wc_prices_include_tax() && 'incl' === $tax_display_mode ) {
			$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
			$tax_rates = WC_Tax::get_rates( $tax_class );
			
			if ( $tax_rates ) {
				$min_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $min_price, $tax_rates ) );
				$max_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max_price, $tax_rates ) );
			}
		}
		
		$min_price = floor( $min_price / $step ) * $step;
		$max_price = ceil( $max_price / $step ) * $step ;
		
		if ( $min_price === $max_price ) {
			return;
		}
		
		$current_min_price = isset( $_GET['min_price'] ) ? floor( floatval( wp_unslash( $_GET['min_price'] ) ) / $step ) * $step : $min_price; // WPCS: input var ok, CSRF ok.
		$current_max_price = isset( $_GET['max_price'] ) ? ceil( floatval( wp_unslash( $_GET['max_price'] ) ) / $step ) * $step : $max_price; // WPCS: input var ok, CSRF ok.
		
		
		$this->widget_start( $args, $instance );
		
		$link = $this->get_current_page_url();
		
		$is_all_link = null !== $current_min_price ? true : false;
		
		$range_size = absint( $instance['price_range_size'] );
		$max_ranges = absint( $instance['max_price_ranges'] ) ; //( absint( $instance['max_price_ranges'] ) - 1 );
		
		echo '<ul>';
		
		if(apply_filters('dhwc_ajax_price_filter_show_all', true)){
			if ( $is_all_link ) {
				$url = remove_query_arg( array( 'min_price','max_price'), $link );
				echo '<li><a href="' . esc_url( $url ) . '">' . esc_html__( 'All', 'dhwc-ajax' ) . '</a></li>';
			} else {
				echo '<li><a href="#">' . esc_html__( 'All', 'dhwc-ajax' ) . '</a></li>';
			} 
		}
		
		$custom_ranges = apply_filters('dhwc_ajax_price_filter_custom_ranges',false);
		
		if(is_array($custom_ranges) && !empty($custom_ranges)){
			foreach ($custom_ranges as $custom_range){
				$custom_range_price = explode('-', $custom_range);
				$custom_range_price_min = floatval($custom_range_price[0]);
				$custom_range_price_max = isset($custom_range_price[1]) ? floatval($custom_range_price[1]) : false;
				$chosen = '';
				
				$product_count = $this->get_filtered_price_range_count($custom_range_price_min, $custom_range_price_max );
				
				if ( $custom_range_price_min == $current_min_price && $custom_range_price_max == $current_max_price ) {
					$url = remove_query_arg( array( 'min_price','max_price'), $link );
					$chosen = ' class="chosen"';
				} else {
					$url = add_query_arg( array( 'min_price' => $custom_range_price_min, 'max_price' => $custom_range_price_max ), $link );
				}
				
				$price_output = $custom_range_price_min . (false !== $custom_range_price_max ? ' - ' . $custom_range_price_max : '') ;
				
				$price_output = apply_filters('dhwc_ajax_price_filter_range', $price_output, $custom_range_price_min, $custom_range_price_max);
					
				echo '<li'.$chosen.'><a href="' . esc_url( $url ) . '">' . $price_output . '</a>'.($show_count ? $this->get_product_count_html($product_count) :'').'</li>';
				
			}
		}else{
		
			$range_min = 0;
			if($range_min < $min_price){
				$range_min = $min_price;
			}
			
			for($range = 0; $range <  $max_ranges; $range ++ ){
				
				$range_max = $range_min + $range_size;
				
				$current_range_min = $range_min;
				
				$range_min += $range_size;
				
				$current_range_max = $range_max;
				
				if($range_max > $max_price){
					$range_max = $max_price;
					$current_range_max = false;
				}
				
				$product_count = $this->get_filtered_price_range_count($current_range_min, $current_range_max );
				
				if($hide_empty_ranges && $product_count <= 0){
					continue;
				}
				
				$chosen = '';
				$range_min_price = wc_price( $current_range_min );
				$range_max_price = wc_price( $range_max );
				
				$price_output = $range_min_price . ' - ' . $range_max_price;
				
				$price_output = apply_filters('dhwc_ajax_price_filter_range', $price_output, $range_min_price, $range_max_price);
				
				if ( $current_range_min == $current_min_price && $range_max == $current_max_price ) {
					$url = remove_query_arg( array( 'min_price','max_price'), $link );
					$chosen = ' class="chosen"';
				} else {
					$url = add_query_arg( array( 'min_price' => $current_range_min, 'max_price' => $range_max ), $link );
				}
				
				echo '<li'.$chosen.'><a href="' . esc_url( $url ) . '">' . $price_output . '</a>'.($show_count ? $this->get_product_count_html($product_count) :'').'</li>';
				
				if($range_max == $max_price){
					break;
				}
				
			}
			
			if($range_max < $max_price ){
				
				$product_count = $this->get_filtered_price_range_count($range_max);
				
				if($product_count > 0){
					
					$url	 			= add_query_arg( array( 'min_price' => $range_max ), remove_query_arg( array( 'min_price','max_price'), $link ) );
					
					echo '<li'.( $current_min_price == $range_max  ? ' class="chosen"' : '').'><a href="' . esc_url( $url ) . '">' . apply_filters('dhwc_ajax_price_filter_range_last', wc_price($range_max) . ' + ')  . '</a>'.($show_count ? $this->get_product_count_html($product_count) : '').'</li>';
				}
			}
		}
		
		echo '</ul>';
		
		$this->widget_end( $args );
	}
	
	protected function get_product_count_html($product_count){
		return apply_filters('dhwc_ajax_price_filter_count', '<span class="count">'.$product_count.'</span>',$product_count);
	}
	
	protected function get_filtered_price_range_count($min_price, $max_price = false) {
		global $wpdb;
		
		$args       = WC()->query->get_main_query()->query_vars;
		$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
		$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();
		
		if ( ! is_post_type_archive( 'product' ) && ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
			$tax_query[] = WC()->query->get_main_tax_query();
		}
		
		foreach ( $meta_query + $tax_query as $key => $query ) {
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}
		
		$meta_query = new WP_Meta_Query( $meta_query );
		$tax_query  = new WP_Tax_Query( $tax_query );
		$search     = WC_Query::get_main_search_query_sql();
		$onsale 	= DHWC_Ajax_Query::get_product_sale_sql();
		$featured = DHWC_Ajax_Query::get_product_featured_sql();
		
		$meta_query_sql   = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql    = $tax_query->get_sql( $wpdb->posts, 'ID' );
		$search_query_sql = $search ? ' AND ' . $search : '';
		$onsale_sql       = $onsale ? ' AND ' . $onsale : '';
		$featured_sql	  = $featured ? ' AND ' . $featured : '';
		
		
		$sql = "
			SELECT COUNT( DISTINCT product_id )
			FROM {$wpdb->wc_product_meta_lookup}
			WHERE product_id IN (
				SELECT ID FROM {$wpdb->posts}
				" . $tax_query_sql['join'] . $meta_query_sql['join'] . "
				WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', $this->_get_filtered_product_types() ) ) . "')
				AND {$wpdb->posts}.post_status = 'publish'
				" . $tax_query_sql['where'] . $meta_query_sql['where'] . $search_query_sql .$onsale_sql. $featured_sql.'
		)';
		
		if(false === $max_price){
			$sql .= $wpdb->prepare(
				" AND {$wpdb->wc_product_meta_lookup}.min_price >= %f",
				$min_price
			);
		}else{
			$sql .= $wpdb->prepare(
				" AND {$wpdb->wc_product_meta_lookup}.min_price >= %f AND {$wpdb->wc_product_meta_lookup}.max_price <= %f ",
				$min_price,
				$max_price
			);
		}
		
		$sql = apply_filters( 'dhwc_ajax_widget_filtered_price_range_count_sql', $sql, $meta_query_sql, $tax_query_sql );
		
		return absint( $wpdb->get_var( $sql ) ); // WPCS: unprepared SQL ok.
	}
	
	/**
	 * Get filtered min price for current products.
	 *
	 * @return int
	 */
	protected function get_filtered_price() {
		global $wpdb;
		
		$args       = WC()->query->get_main_query()->query_vars;
		$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
		$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();
		
		if ( ! is_post_type_archive( 'product' ) && ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
			$tax_query[] = WC()->query->get_main_tax_query();
		}
		
		foreach ( $meta_query + $tax_query as $key => $query ) {
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}
		
		$meta_query = new WP_Meta_Query( $meta_query );
		$tax_query  = new WP_Tax_Query( $tax_query );
		$search     = WC_Query::get_main_search_query_sql();
		$onsale 	= DHWC_Ajax_Query::get_product_sale_sql();
		$featured 	= DHWC_Ajax_Query::get_product_featured_sql();
		
		$meta_query_sql   = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql    = $tax_query->get_sql( $wpdb->posts, 'ID' );
		$search_query_sql = $search ? ' AND ' . $search : '';
		$onsale_sql       = $onsale ? ' AND ' . $onsale : '';
		$featured_sql	  = $featured ? ' AND ' . $featured : '';

		$sql = "
			SELECT min( min_price ) as min_price, MAX( max_price ) as max_price
			FROM {$wpdb->wc_product_meta_lookup}
			WHERE product_id IN (
				SELECT ID FROM {$wpdb->posts}
				" . $tax_query_sql['join'] . $meta_query_sql['join'] . "
				WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', $this->_get_filtered_product_types() ) ) . "')
				AND {$wpdb->posts}.post_status = 'publish'
				" . $tax_query_sql['where'] . $meta_query_sql['where'] . $search_query_sql .$onsale_sql. $featured_sql.'
		)';
		
		$sql = apply_filters( 'dhwc_ajax_widget_price_range_filter_sql', $sql, $meta_query_sql, $tax_query_sql );
		
		return $wpdb->get_row( $sql ); // WPCS: unprepared SQL ok.
	}

}

add_action('widgets_init', function(){
	return register_widget("DHWC_Ajax_Widget_Price_Range_Filter");
});