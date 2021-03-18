<?php
/**
 * Class DHWC_Ajax_Widget_Stock_Filter
 */
class DHWC_Ajax_Widget_Stock_Filter extends DHWC_Ajax_Widget {
	
	public function __construct(){
		$this->widget_cssclass    = 'woocommerce widget_layered_nav dhwc_ajax_stock_filter';
		$this->widget_description = __('Display product filter with stock status in your store', 'dhwc-ajax');
		$this->widget_id          = 'dhwc_ajax_stock_filter';
		$this->widget_name        = __('DHWC Ajax Stock Filter', 'dhwc-ajax');
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
				'std'   => __( 'Filter Stock Status', 'dhwc-ajax' ),
				'label' => __( 'Title', 'dhwc-ajax' )
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
		
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}
		$show_count = isset( $instance['show_count'] ) ? (int) $instance['show_count'] : $this->settings['show_count']['std'];
		
		ob_start();
		$found = false;
		
		$this->widget_start( $args, $instance );
		$link = $this->get_current_page_url();
		$filter_name    	= dhwc_ajax_get_stock_status_filter_query();
		$current_stock_status = isset( $_REQUEST[$filter_name] ) ? wc_clean( wp_unslash( $_REQUEST[$filter_name] ) ) : false; // WPCS: input var ok, sanitization ok.
		$stock_statuses       = wc_get_product_stock_status_options();
		echo '<ul>';
			foreach ($stock_statuses as $status => $label){
				$count = absint($this->get_filtered_product_stock_status($status));
				
				if(!$count){
					continue;
				}
				
				$found = true;
				$count_html = '';
				if($show_count)
					$count_html = apply_filters( 'dhwc_ajax_stock_filter_count', '<span class="count">' . $count . '</span>', $count, $status );
				
				if($current_stock_status === $status){
					$link = remove_query_arg($filter_name,$link);
					echo '<li class="chosen"><a href="'.$link.'">'.$label.'</a>'.$count_html.'</li>';
				}else{
					$link = add_query_arg(array($filter_name=>$status),$link);
					echo '<li><a href="'.$link.'">'.$label.'</a>'.$count_html.'</li>';
				}
			}
		echo '</ul>';
		
		$this->widget_end( $args );
		
		if ( ! $found ) {
			ob_end_clean();
		} else {
			echo ob_get_clean(); // @codingStandardsIgnoreLine
		}
	}
	
	
	protected function get_filtered_product_stock_status($status){
		global $wpdb;
		$tax_query  = WC_Query::get_main_tax_query();
		$meta_query = WC_Query::get_main_meta_query();
	
		// Unset current stock filter.
		foreach ( $meta_query as $key => $query ) {
			if ( ! empty( $query['stock_status_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}
		
		// Set new stock filter.
		$meta_query['stock_status_filter'] = array(
			'key'   => '_stock_status',
			'value' => $status,
		);
		
		$meta_query     = new WP_Meta_Query( $meta_query );
		$tax_query      = new WP_Tax_Query( $tax_query );
		
		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );
		
		$price_range_query = DHWC_Ajax_Query::get_product_price_range_query();
		
		$sql  = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) FROM {$wpdb->posts} ";
		$sql .= $price_range_query['join'] . $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " WHERE {$wpdb->posts}.post_type IN ( '".implode( "','", array_map( 'esc_sql', $this->_get_filtered_product_types() ) )."' ) AND {$wpdb->posts}.post_status = 'publish' ";
		$sql .= $price_range_query['where'] . $tax_query_sql['where'] . $meta_query_sql['where'];
		

		if($onsale_sql = DHWC_Ajax_Query::get_product_sale_sql()){
			$sql .= ' AND ' . $onsale_sql;
		}
		
		if($featured_sql = DHWC_Ajax_Query::get_product_featured_sql()){
			$sql .= ' AND ' . $featured_sql;
		}
		
		$search = WC_Query::get_main_search_query_sql();
		if ( $search ) {
			$sql .= ' AND ' . $search;
		}
		
		return absint( $wpdb->get_var( $sql ) ); // WPCS: unprepared SQL ok.
	}
	
}
add_action('widgets_init', function(){
	return register_widget("DHWC_Ajax_Widget_Stock_Filter");
});