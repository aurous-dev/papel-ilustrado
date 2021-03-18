<?php

class DHWC_Ajax_Widget_Layered_Nav_Filter extends DHWC_Ajax_Widget  {
/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_layered_nav_filters dhwc_ajax_widget_layered_nav_filters';
		$this->widget_description = __( 'Display a list of active product filters.', 'dhwc-ajax' );
		$this->widget_id          = 'dhwc_ajax_widget_layered_nav_filters';
		$this->widget_name        = __( 'DHWC Ajax Active Product Filters', 'dhwc-ajax' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Active filters', 'dhwc-ajax' ),
				'label' => __( 'Title', 'dhwc-ajax' ),
			),
		);

		parent::__construct();
	}
	/**
	 * Format current filter name
	 */
	private function _fromat_name($label, $name, $only_name=false){
		if($only_name)
			return $name;
		return $label.':&nbsp;' .$name;
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}

		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		$min_price          = isset( $_GET['min_price'] ) ? wc_clean( $_GET['min_price'] ) : 0;
		$max_price          = isset( $_GET['max_price'] ) ? wc_clean( $_GET['max_price'] ) : 0;
		$rating_filter      = isset( $_GET['rating_filter'] ) ? array_filter( array_map( 'absint', explode( ',', $_GET['rating_filter'] ) ) ) : array();
		
		$category_filter_query  = dhwc_ajax_get_category_filter_query();
		$category_filters   = isset( $_GET[$category_filter_query] ) ? wc_clean( $_GET[$category_filter_query] ) : false;
		
		$brand_filter_query = dhwc_ajax_get_brand_filter_query();
		$brand_filters   	= isset( $_GET[$brand_filter_query] ) ? wc_clean( $_GET[$brand_filter_query] ) : false;
		
		$tag_filter_query = dhwc_ajax_get_tag_filter_query();
		$tag_filters    	= isset( $_GET[$tag_filter_query] ) ? wc_clean( $_GET[$tag_filter_query] ) : false;
		
		$product_status_filter_query = dhwc_ajax_get_product_status_filter_query();
		$product_status_filter 		= isset( $_GET[$product_status_filter_query] ) ? wc_clean( $_GET[$product_status_filter_query] ) : false;
		
		$stock_filter_query = dhwc_ajax_get_stock_status_filter_query();
		$stock_filter 		= isset( $_GET[$stock_filter_query] ) ? wc_clean( $_GET[$stock_filter_query] ) : false;
		
		
		$page_url   		= $this->_get_page_url();
		$base_link          = $this->get_current_page_url();
		$base_link 			= remove_query_arg('acf',$base_link);
		
		//Acf
		$acf_filtered_html = '';
		if ( defined('ACF') && ! empty( $_GET['acf'] ) ) {
			$acf_prefix = dhwc_ajax_get_acf_field_filter_query_prefix();
			$acf_filter_count = 0;
			$has_acf_filter = false;
			foreach ( $_GET as $key => $value ) { // WPCS: input var ok, CSRF ok.
				if ( 0 === strpos( $key, $acf_prefix) ) {
					$field_name = str_replace( $acf_prefix, '', $key );
					$field = acf_get_field($field_name);
					
					if ( empty( $field ) ){
						continue;
					}
					
					$acf_filter_count++;
					if($acf_filter_count > 0 && !$has_acf_filter){
						$base_link = add_query_arg('acf','1',$base_link);
						$has_acf_filter=true;
					}
					$choices = (array) $field['choices'];
					$selected_label = isset($choices[$value]) ? $choices[$value] : $value;
					$link = remove_query_arg($key,$base_link);
					$acf_filtered_html .= '<li class="chosen"><a title="'.esc_attr__('Remove filter','dhwc-ajax').'" aria-label="' . esc_attr__( 'Remove filter', 'dhwc-ajax' ) . '" href="' . esc_url( $link ) . '">';
					$acf_filtered_html .= $this->_fromat_name($field['label'],$selected_label);
					$acf_filtered_html .= '</a></li>';
					
				}
			}
			
		
		}
		
		//Weight Dimensions Filter
		$weight_dimensions_filtered = '';
		$weight_dimensions_options = dhwc_ajax_get_weight_dimensions_options();
		foreach ($weight_dimensions_options as $key=>$value){
			$filter_name = $key.'_filter';
			if(!empty($_GET[$filter_name])){
				$link = remove_query_arg($filter_name, $base_link );
				
				$filtered = array_map( 'absint', explode( '-', wp_unslash( $_GET[$filter_name] ) ) );
				$min_value = $filtered[0];
				$max_value = $filtered[1];
				
				if('weight'===$key){
					$unit = dhwc_ajax_get_option( 'woocommerce_weight_unit' );
				}else{
					$unit = dhwc_ajax_get_option( 'woocommerce_dimension_unit' );
				}
				
				$label = $min_value.$unit.' - '.$max_value.$unit;
				
				$weight_dimensions_filtered .= '<li class="chosen"><a title="'.esc_attr__('Remove filter','dhwc-ajax').'" aria-label="' . esc_attr__( 'Remove filter', 'dhwc-ajax' ) . '" href="' . esc_url( $link ) . '">';
				$weight_dimensions_filtered .= $this->_fromat_name($weight_dimensions_options[$key],$label);
				$weight_dimensions_filtered .= '</a></li>';
			}
		}
		
		//Custom tax filter
		$custom_tag_filter_html = '';
		foreach (dhwc_ajax_get_product_custom_taxonomies() as $custom_tax=>$label){
			$custom_tax_filter_query = dhwc_ajax_get_custom_taxonomy_filter_query($custom_tax);
			$custom_tax_filters    	= isset( $_GET[$custom_tax_filter_query] ) ? wc_clean( $_GET[$custom_tax_filter_query] ) : false;
			if($custom_tax_filters){
				$custom_tax_filters = explode(',', $custom_tax_filters);
				$custom_tax_filters = array_map( 'sanitize_title', $custom_tax_filters );
				foreach ($custom_tax_filters as $custom_tax_slug){
					if($tax = get_term_by('slug',$custom_tax_slug, $custom_tax)){
						$new_filter      = array_diff( $custom_tax_filters, array( $custom_tax_slug ) );
						$link = remove_query_arg( array( 'add-to-cart', $custom_tax_filter_query ), $base_link );
						
						if ( sizeof( $new_filter ) > 0 ) {
							$link = add_query_arg( $custom_tax_filter_query, implode( ',', $new_filter ), $base_link );
						}
						$custom_tag_filter_html .= '<li class="chosen"><a title="'.esc_attr__('Remove filter','dhwc-ajax').'" aria-label="' . esc_attr__( 'Remove filter', 'dhwc-ajax' ) . '" href="' . esc_url( $link ) . '">'.$this->_fromat_name($label , $tax->name ) . '</a></li>';
					}
				}
			}
		}
		
		if ( 0 < count( $_chosen_attributes ) || $category_filters || $brand_filters || $tag_filters || 0 < $min_price || 0 < $max_price || ! empty( $rating_filter ) ||  $stock_filter || $product_status_filter || ''!==$weight_dimensions_filtered || '' !==$acf_filtered_html || '' !== $custom_tag_filter_html ) {

			$this->widget_start( $args, $instance );
			echo '<ul>';
			//Categories
			if($category_filters){
				$category_filters = explode(',', $category_filters);
				$category_filters = array_map( 'sanitize_title', $category_filters );
				foreach ($category_filters as $category_filter_slug){
					if($category = get_term_by('slug',$category_filter_slug,'product_cat')){
						$link = remove_query_arg( array( 'add-to-cart', $category_filter_query ), $base_link );
						
						echo '<li class="chosen"><a title="'.esc_attr__('Remove filter','dhwc-ajax').'" aria-label="' . esc_attr__( 'Remove filter', 'dhwc-ajax' ) . '" href="' . esc_url( $link ) . '">'.$this->_fromat_name(esc_html__('Category','dhwc-ajax'),$category->name) . '</a></li>';
					}
				}
			}
			//Brands
			if($brand_filters){
				$brand_filters = explode(',', $brand_filters);
				$brand_filters = array_map( 'sanitize_title', $brand_filters );
				foreach ($brand_filters as $brand_filter_slug){
					if($brand = get_term_by('slug',$brand_filter_slug,'product_brand')){
						$new_filter      = array_diff( $brand_filters, array( $brand_filter_slug ) );
						$link = remove_query_arg( array( 'add-to-cart', $brand_filter_query ), $base_link );
						
						if ( sizeof( $new_filter ) > 0 ) {
							$link = add_query_arg( $brand_filter_query, implode( ',', $new_filter ), $base_link );
						}
						echo '<li class="chosen"><a title="'.esc_attr__('Remove filter','dhwc-ajax').'" aria-label="' . esc_attr__( 'Remove filter', 'dhwc-ajax' ) . '" href="' . esc_url( $link ) . '">'.$this->_fromat_name(esc_html__('Brand','dhwc-ajax') , $brand->name ) . '</a></li>';
					}
				}
			}
			// Tags
			if($tag_filters){
				$tag_filters = explode(',', $tag_filters);
				$tag_filters = array_map( 'sanitize_title', $tag_filters );
				foreach ($tag_filters as $tag_filter_slug){
					if($tag = get_term_by('slug',$tag_filter_slug,'product_tag')){
						$new_filter      = array_diff( $tag_filters, array( $tag_filter_slug ) );
						$link = remove_query_arg( array( 'add-to-cart', $tag_filter_query ), $base_link );
						
						if ( sizeof( $new_filter ) > 0 ) {
							$link = add_query_arg( $tag_filter_query, implode( ',', $new_filter ), $link );
						}
						
						echo '<li class="chosen"><a title="'.esc_attr__('Remove filter','dhwc-ajax').'" aria-label="' . esc_attr__( 'Remove filter', 'dhwc-ajax' ) . '" href="' . esc_url( $link ) . '">' . $this->_fromat_name( esc_html__('Tag','dhwc-ajax'), $tag->name ) . '</a></li>';
					}
				}
			}
			
			//
			echo $custom_tag_filter_html;
			
			// Attributes
			if ( ! empty( $_chosen_attributes ) ) {

				$taxonomies = wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_label', 'attribute_name');
				
				foreach ( $_chosen_attributes as $taxonomy => $data ) {
					$taxonomy_name       = str_replace( 'pa_', '', wc_sanitize_taxonomy_name( $taxonomy ) );
					foreach ( $data['terms'] as $term_slug ) {
						if ( ! $term = get_term_by( 'slug', $term_slug, $taxonomy ) ) {
							continue;
						}

						$filter_name    = 'filter_' . sanitize_title( str_replace( 'pa_', '', $taxonomy ) );
						$current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( $_GET[ $filter_name ] ) ) : array();
						$current_filter = array_map( 'sanitize_title', $current_filter );
						$new_filter      = array_diff( $current_filter, array( $term_slug ) );

						$link = remove_query_arg( array( 'add-to-cart', $filter_name ), $base_link );

						if ( sizeof( $new_filter ) > 0 ) {
							$link = add_query_arg( $filter_name, implode( ',', $new_filter ), $link );
						}
						$tax_name = (isset($taxonomies[$taxonomy_name]) ? $taxonomies[$taxonomy_name].': ':'');
						echo '<li class="chosen"><a title="'.esc_attr__('Remove filter','dhwc-ajax').'" aria-label="' . esc_attr__( 'Remove filter', 'dhwc-ajax' ) . '" href="' . esc_url( $link ) . '">' .$this->_fromat_name( $tax_name, $term->name ) . '</a></li>';
					}
				}
			}
			
			//Min/Max
			if ( $min_price || $max_price) {
				$link = remove_query_arg(array('min_price','max_price'), $base_link );
				echo '<li class="chosen"><a title="'.esc_attr__('Remove filter','dhwc-ajax').'" aria-label="' . esc_attr__( 'Remove filter', 'dhwc-ajax' ) . '" href="' . esc_url( $link ) . '">';
				
				if($min_price && $max_price){
					$label = __( 'Price', 'dhwc-ajax' );
					$name = wc_price( $min_price ).' - '.wc_price( $max_price );
				}elseif ($min_price){
					$label =__( 'Price min', 'dhwc-ajax' );
					$name = wc_price( $min_price ) ;
				}elseif ($max_price){
					$label =__( 'Price max', 'dhwc-ajax' );
					$name = wc_price( $max_price );
				}
				echo $this->_fromat_name($label, $name);
				echo '</a></li>';
			}
			//Rating
			if ( ! empty( $rating_filter ) ) {
				foreach ( $rating_filter as $rating ) {
					$link_ratings = implode( ',', array_diff( $rating_filter, array( $rating ) ) );
					$link         = $link_ratings ? add_query_arg( 'rating_filter', $link_ratings ) : remove_query_arg( 'rating_filter', $base_link );
					echo '<li class="chosen"><a title="'.esc_attr__('Remove filter','dhwc-ajax').'" aria-label="' . esc_attr__( 'Remove filter', 'dhwc-ajax' ) . '" href="' . esc_url( $link ) . '">' ;
					echo $this->_fromat_name('',sprintf( esc_html__( 'Rated %s out of 5', 'dhwc-ajax' ),$rating ), true);
					echo '</a></li>';
				}
			}
			//Stock status 
			if($stock_filter){
				$stock_statuses     = wc_get_product_stock_status_options();
				$link = remove_query_arg($stock_filter_query, $base_link );
				echo '<li class="chosen"><a title="'.esc_attr__('Remove filter','dhwc-ajax').'" aria-label="' . esc_attr__( 'Remove filter', 'dhwc-ajax' ) . '" href="' . esc_url( $link ) . '">';
				echo $this->_fromat_name(esc_html__('Stock status','dhwc-ajax'),$stock_statuses[$stock_filter]);
				echo '</a></li>';
			}
			//Product status
			if($product_status_filter){
				$product_status_options     = dhwc_ajax_get_product_status_options();
				$link = remove_query_arg($product_status_filter_query, $base_link );
				echo '<li class="chosen"><a title="'.esc_attr__('Remove filter','dhwc-ajax').'" aria-label="' . esc_attr__( 'Remove filter', 'dhwc-ajax' ) . '" href="' . esc_url( $link ) . '">';
				echo $this->_fromat_name(esc_html__('Product status','dhwc-ajax'),$product_status_options[$product_status_filter]);
				echo '</a></li>';
			}
			
			echo $weight_dimensions_filtered;
			
			echo $acf_filtered_html;
			
			
			echo '</ul>';

			$this->widget_end( $args );
		}
	}
		
}
add_action('widgets_init', function(){
	return register_widget("DHWC_Ajax_Widget_Layered_Nav_Filter");
});
