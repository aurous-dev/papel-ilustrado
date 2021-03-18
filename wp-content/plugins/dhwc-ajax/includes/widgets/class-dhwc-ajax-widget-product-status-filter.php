<?php
/**
 * Class DHWC_Ajax_Widget_Product_Status_Filter
 */
class DHWC_Ajax_Widget_Product_Status_Filter extends DHWC_Ajax_Widget {
	
	public function __construct(){
		$this->widget_cssclass    = 'woocommerce widget_layered_nav dhwc_ajax_product_status_filter';
		$this->widget_description = __('Display product filter with status: Featured, Sale...', 'dhwc-ajax');
		$this->widget_id          = 'dhwc_ajax_product_status_filter';
		$this->widget_name        = __('DHWC Ajax Product Status Filter', 'dhwc-ajax');
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
				'std'   => __( 'Filter Status', 'dhwc-ajax' ),
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
		
		$link 			= $this->get_current_page_url();
		$filter_name    = dhwc_ajax_get_product_status_filter_query();
		$current_status = isset( $_REQUEST[$filter_name] ) ? wc_clean( wp_unslash( $_REQUEST[$filter_name] ) ) : false; // WPCS: input var ok, sanitization ok.
		$statuses       = dhwc_ajax_get_product_status_options();
		
		echo '<ul>';
			foreach ($statuses as $status => $label){
				$count = absint($this->get_filtered_product_status($status));
				
				if(!$count){
					continue;
				}
				
				$found = true;
				
				$count_html = '';
				if($show_count)
					$count_html = apply_filters( 'dhwc_ajax_product_status_filter_count', '<span class="count">' . $count . '</span>', $count, $status );
				if($current_status === $status){
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
	
	protected function get_filtered_product_status($status){
		global $wpdb;
		
		$tax_query  = WC_Query::get_main_tax_query();
		$meta_query = WC_Query::get_main_meta_query();
	
		$meta_query     = new WP_Meta_Query( $meta_query );
		$tax_query      = new WP_Tax_Query( $tax_query );
		
		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );
		
		$price_range_query = DHWC_Ajax_Query::get_product_price_range_query();
		
		$sql  = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) FROM {$wpdb->posts} ";
		$sql .= $price_range_query['join'] ;
		$sql .= $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " WHERE {$wpdb->posts}.post_type IN ( '".implode( "','", array_map( 'esc_sql', $this->_get_filtered_product_types() ) )."' ) AND {$wpdb->posts}.post_status = 'publish' ";
		$sql .= $price_range_query['where'] . $tax_query_sql['where'] . $meta_query_sql['where'];
		
		$search = WC_Query::get_main_search_query_sql();
		if ( $search ) {
			$sql .= ' AND ' . $search;
		}
		
		if('onsale'===$status && $product_sales = wc_get_product_ids_on_sale()){
			$sql .= " AND $wpdb->posts.ID IN ( " . implode( ',', $product_sales ) . ") ";
		}
		if('featured'===$status && $product_featured = wc_get_featured_product_ids()){
			$sql .= " AND $wpdb->posts.ID IN ( " . implode( ',', $product_featured ) . ") ";
		}
		
		return absint( $wpdb->get_var( $sql ) ); // WPCS: unprepared SQL ok.
	}
	
}
add_action('widgets_init', function(){
	return register_widget("DHWC_Ajax_Widget_Product_Status_Filter");
});