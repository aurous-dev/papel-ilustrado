<?php

class DHWC_Ajax_Widget_Product_Taxonomy_Filter extends DHWC_Ajax_Widget {
	
	public function __construct(){
		$this->widget_cssclass    = 'woocommerce widget_layered_nav dhwc_ajax_taxonomy_filter';
        $this->widget_description = __('Display a list of custom taxonomy to filter products in your store.', 'dhwc-ajax');
        $this->widget_id          = 'dhwc_ajax_taxonomy_filter';
        $this->widget_name        = __('DHWC Ajax Product Custom Taxonomy Filter', 'dhwc-ajax');
        parent::__construct();
	}
	
	/**
	 * Updates a particular instance of a widget.
	 *
	 * @see WP_Widget->update
	 *
	 * @param array $new_instance New Instance.
	 * @param array $old_instance Old Instance.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$this->init_settings();
		return parent::update( $new_instance, $old_instance );
	}
	
	/**
	 * Outputs the settings update form.
	 *
	 * @see WP_Widget->form
	 *
	 * @param array $instance Instance.
	 */
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
				'std'   => __( 'Custom Taxonomy', 'dhwc-ajax' ),
				'label' => __( 'Title', 'dhwc-ajax' ),
			),
			'taxonomy'            => array(
				'type'    => 'select',
				'std'     => '',
				'label'   => __( 'Taxonomy', 'dhwc-ajax' ),
				'options' => dhwc_ajax_get_product_custom_taxonomies(true),
			),
			'query_type'   => array(
				'type'    => 'select',
				'std'     => 'and',
				'label'   => __( 'Query type', 'dhwc-ajax' ),
				'options' => array(
					'and' => __( 'AND', 'dhwc-ajax' ),
					'or'  => __( 'OR', 'dhwc-ajax' ),
				),
			),
			'orderby'            => array(
				'type'    => 'select',
				'std' => 'name',
				'label'   => __( 'Order by', 'dhwc-ajax' ),
				'options' => array(
					'order' => __( 'Category order', 'dhwc-ajax' ),
					'name'  => __( 'Name', 'dhwc-ajax' ),
				),
			),
			'show_children_only' => array(
				'type'  => 'checkbox',
				'std' => 0,
				'label' => __( 'Only show children of the current category', 'dhwc-ajax' ),
			),
			'show_count'     => array(
				'type'  => 'checkbox',
				'std' => 0,
				'label' => __( 'Show product counts', 'dhwc-ajax' ),
			)
		);
	}
	
	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args Arguments.
	 * @param array $instance Instance.
	 */
	public function widget( $args, $instance ) {
		global $wp_query;
		
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}
		
		$orderby            = isset( $instance['orderby'] ) ? $instance['orderby'] : 'name';
		$show_children_only = isset( $instance['show_children_only'] ) ? $instance['show_children_only'] : 0;
		$show_count 		= isset( $instance['show_count'] ) ? (int) $instance['show_count'] : 0;
		$query_type 		= isset( $instance['query_type'] ) ? $instance['query_type'] : 'and';
		
		$_chosen_attributes = DHWC_Ajax_Query::get_layered_nav_chosen_filters();
		$taxonomy           = isset( $instance['taxonomy'] ) ? $instance['taxonomy']  : null;
		
		if(empty($taxonomy) || !taxonomy_exists($taxonomy) ){
			return;
		}
		
		$get_terms_args = array(
			'hide_empty' 	=> true,
			'menu_order'	=> false
		);

		if ( 'order' === $orderby ) {
			$get_terms_args['orderby']      = 'meta_value_num';
			$get_terms_args['meta_key']     = 'order';
		}
		$current_taxonomy = false;
		if(is_tax( 'product_cat' )){
			$current_taxonomy = $wp_query->queried_object;
			$get_terms_args['include']     = array_merge(array($current_taxonomy->term_id),get_term_children($current_taxonomy->term_id, 'product_cat'));
			
		}
		
		$terms = get_terms( $taxonomy, $get_terms_args );

		if (0 === count( $terms ) ) {
			return;
		}
	
		ob_start();
	
		$this->widget_start( $args, $instance );

		$found = $this->layered_nav_list( $terms, $taxonomy, $query_type, $show_children_only, $current_taxonomy, $show_count );
	
		$this->widget_end( $args );
	
		//Force found when option is selected - do not force found on taxonomy attributes.
		if ( ! is_tax() && is_array( $_chosen_attributes ) && array_key_exists( $taxonomy, $_chosen_attributes ) ) {
			$found = true;
		}
	
		if ( ! $found ) {
			ob_end_clean();
		} else {
			echo ob_get_clean(); // @codingStandardsIgnoreLine
		}
	}
	
	/**
	 * Return the currently viewed taxonomy name.
	 *
	 * @return string
	 */
	protected function get_current_taxonomy() {
		return is_tax() ? get_queried_object()->taxonomy : '';
	}
	
	/**
	 * Return the currently viewed term ID.
	 *
	 * @return int
	 */
	protected function get_current_term_id() {
		return absint( is_tax() ? get_queried_object()->term_id : 0 );
	}
	
	/**
	 * Return the currently viewed term slug.
	 *
	 * @return int
	 */
	protected function get_current_term_slug() {
		return absint( is_tax() ? get_queried_object()->slug : 0 );
	}
	
	
	/**
	 * Count products within certain terms, taking the main WP query into consideration.
	 *
	 * This query allows counts to be generated based on the viewed products, not all products.
	 *
	 * @param  array  $term_ids Term IDs.
	 * @param  string $taxonomy Taxonomy.
	 * @param  string $query_type Query Type.
	 * @return array
	 */
	 protected function get_filtered_term_product_counts( $terms, $taxonomy, $query_type ) {
		global $wpdb;
	
		$term_ids = wp_list_pluck( $terms, 'term_id' );
		
		$tax_query  = WC_Query::get_main_tax_query();
		$meta_query = WC_Query::get_main_meta_query();

		if ( 'or' === $query_type ) {
			foreach ( $tax_query as $key => $query ) {
				if ( is_array( $query ) && $taxonomy === $query['taxonomy'] ) {
					unset( $tax_query[ $key ] );
				}
			}
		}
		
		$meta_query      = new WP_Meta_Query( $meta_query );
		$tax_query       = new WP_Tax_Query( $tax_query );
		$meta_query_sql  = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql   = $tax_query->get_sql( $wpdb->posts, 'ID' );
	
		$price_range_query = DHWC_Ajax_Query::get_product_price_range_query();
		
		// Generate query.
		$query           = array();
		$query['select'] = "SELECT term_relationships.object_id, term_relationships.term_taxonomy_id";
		$query['from']   = "FROM {$wpdb->posts}";
		$query['join']   = "
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			" . $price_range_query['join'] . $tax_query_sql['join'] . $meta_query_sql['join'];
	
		$query['where']   = "
			WHERE {$wpdb->posts}.post_type IN ( '".implode( "','", array_map( 'esc_sql', $this->_get_filtered_product_types() ) )."' )
			AND {$wpdb->posts}.post_status = 'publish'"
			. $price_range_query['where'] . $tax_query_sql['where'] . $meta_query_sql['where'] .
			'AND terms.term_id IN (' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';
	
		if ( $search = WC_Query::get_main_search_query_sql() ) {
			$query['where'] .= ' AND ' . $search;
		}
	
		if($onsale_sql = DHWC_Ajax_Query::get_product_sale_sql()){
			$query['where'] .= ' AND ' . $onsale_sql;
		}
	
		if($featured_sql = DHWC_Ajax_Query::get_product_featured_sql()){
			$query['where'] .= ' AND ' . $featured_sql;
		}
	
		//$query['group_by'] = 'GROUP BY terms.term_id';
		$query             = apply_filters( 'dhwc_ajax_taxonomy_filtered_counts_query', $query );
		$query             = implode( ' ', $query );
		
		// We have a query - let's see if cached results of this query already exist.
		$query_hash    = md5( $query );
		$cached_counts = (array) get_transient( 'dhwc_ajax_taxonomy_filtered_counts' );
	
		if ( ! isset( $cached_counts[ $query_hash ] ) ) {
			
			$term_items  = array();
			$terms_by_id = array();
			$term_ids    = array();
			
			foreach ( (array) $terms as $key => $term ) {
				$terms_by_id[ $term->term_id ]       = & $terms[ $key ];
				$term_ids[ $term->term_taxonomy_id ] = $term->term_id;
			}
			
			$results = $wpdb->get_results( $query ); // @codingStandardsIgnoreLine
			
			foreach ( $results as $row ) {
				$id                                   = $term_ids[ $row->term_taxonomy_id ];
				$term_items[ $id ][ $row->object_id ] = isset( $term_items[ $id ][ $row->object_id ] ) ? ++$term_items[ $id ][ $row->object_id ] : 1;
			}
			
			foreach ( $term_ids as $term_id ) {
				$child     = $term_id;
				$ancestors = array();
				while ( ! empty( $terms_by_id[ $child ] ) && $parent = $terms_by_id[ $child ]->parent ) {
					$ancestors[] = $child;
					if ( ! empty( $term_items[ $term_id ] ) ) {

						foreach ( $term_items[ $term_id ] as $item_id => $touches ) {
								
							$term_items[ $parent ][ $item_id ] = isset( $term_items[ $parent ][ $item_id ] ) ? ++$term_items[ $parent ][ $item_id ] : 1;
						}
					}
					$child = $parent;
			
					if ( in_array( $parent, $ancestors ) ) {
						break;
					}
				}
			}
			$counts = array();
			foreach ( (array) $term_items as $id => $items ) {
				$counts[$id] = count( $items );
			}
			
			$cached_counts[ $query_hash ] = $counts;
			set_transient( 'dhwc_ajax_taxonomy_filtered_counts', $cached_counts, DAY_IN_SECONDS );
		}
	
		return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
	}
	
	/**
	 * Show list based layered nav.
	 *
	 * @param  array  $terms Terms.
	 * @param  string $taxonomy Taxonomy.
	 * @param  string $query_type Query Type.
	 * @return bool   Will nav display?
	 */
	protected function layered_nav_list( $terms, $taxonomy, $query_type, $show_children_only, $current_taxonomy, $show_count ) {
		// List display.
		echo '<ul>';
		$term_counts        = $this->get_filtered_term_product_counts($terms, $taxonomy, $query_type);
		$_chosen_filters 	= DHWC_Ajax_Query::get_layered_nav_chosen_filters();
		$found              = false;
		
		$base_link 			= $this->get_current_page_url();
		$filter_name    	= dhwc_ajax_get_custom_taxonomy_filter_query($taxonomy);
		$link = remove_query_arg( $filter_name, $base_link );
		foreach ( $terms as $term ) {
			$current_values = isset( $_chosen_filters[ $taxonomy ]['terms'] ) ? $_chosen_filters[ $taxonomy ]['terms'] : array();
			$option_is_set  = in_array( $term->slug, $current_values );
			$count          = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;
			
			if(false===$current_taxonomy && $show_children_only && $term->parent){
				continue;
			}
			
			
			if($current_taxonomy && $show_children_only && $term->parent !== $current_taxonomy->term_id){
				continue;
			}
			
			// Skip the term for the current archive.
			if ( $this->get_current_term_id() === $term->term_id ) {
				continue;
			}
	
			// Only show options with count > 0.
			if ( 0 < $count ) {
				$found = true;
			} elseif ( 0 === $count && ! $option_is_set ) {
				continue;
			}
			
			if($option_is_set){
				$chosen = ' class="chosen"';
				$link = remove_query_arg( $filter_name,$base_link );
			}else{
				$chosen = '';
				$link = add_query_arg( $filter_name, $term->slug, $base_link );
			}
			$link = str_replace( '%2C', ',', $link );
			
			if ( $count > 0 || $option_is_set ) {
				$link      = esc_url( apply_filters( 'dhwc_ajax_taxonomy_filter_link', $link, $term, $taxonomy ) );
				$term_html = '<a href="' . $link . '">' .esc_html( $term->name ) . '</a>';
			} else {
				$link      = false;
				$term_html = '<span>' . esc_html( $term->name ) . '</span>';
			}
			
			if($show_count){
				$term_html .= ' ' . apply_filters( 'dhwc_ajax_taxonomy_filter_count', '<span class="count">' . absint( $count ) . '</span>', $count, $term );
			}
			echo '<li' . ( $chosen) . '>';
			echo apply_filters( 'dhwc_ajax_taxonomy_filter_html', $term_html, $term, $link, $count );
			echo '</li>';
		}
	
		echo '</ul>';
	
		return $found;
	}
}
add_action('widgets_init', function(){
	return register_widget("DHWC_Ajax_Widget_Product_Taxonomy_Filter");
});