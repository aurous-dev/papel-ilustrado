<?php

class DHWC_Brand_Widget extends WC_Widget{
	
	public function __construct(){
		$this->widget_cssclass    = 'woocommerce widget_product_categories dhwc_brand_widget';
		$this->widget_description = __( 'A list or dropdown of product brands.', DHWC_BRAND_TEXT_DOMAIN );
		$this->widget_id          = 'dhwc_brand_widget';
		$this->widget_name        = __( 'DHWC Brands', DHWC_BRAND_TEXT_DOMAIN );
		$this->init_settings();
		parent::__construct();
	}
	
	public function init_settings(){
		$this->settings = array(
			'title'              => array(
				'type'  => 'text',
				'std'   => __( 'Product brands', DHWC_BRAND_TEXT_DOMAIN ),
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
			'dropdown'           => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show as dropdown', DHWC_BRAND_TEXT_DOMAIN ),
			),
			'count'              => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show product counts', DHWC_BRAND_TEXT_DOMAIN ),
			),
			'hierarchical'       => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Show hierarchy', DHWC_BRAND_TEXT_DOMAIN ),
			),
			'show_children_only' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Only show children of the current brand', DHWC_BRAND_TEXT_DOMAIN ),
			),
			'hide_empty'         => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide empty brands', DHWC_BRAND_TEXT_DOMAIN ),
			),
			'max_depth'          => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Maximum depth', DHWC_BRAND_TEXT_DOMAIN ),
			),
		);
	}
	
	public function widget($args, $instance){
		global $wp_query, $post;
		
		$count              = isset( $instance['count'] ) ? $instance['count'] : $this->settings['count']['std'];
		$hierarchical       = isset( $instance['hierarchical'] ) ? $instance['hierarchical'] : $this->settings['hierarchical']['std'];
		$show_children_only = isset( $instance['show_children_only'] ) ? $instance['show_children_only'] : $this->settings['show_children_only']['std'];
		$dropdown           = isset( $instance['dropdown'] ) ? $instance['dropdown'] : $this->settings['dropdown']['std'];
		$orderby            = isset( $instance['orderby'] ) ? $instance['orderby'] : $this->settings['orderby']['std'];
		$hide_empty         = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : $this->settings['hide_empty']['std'];
		$dropdown_args      = array(
			'hide_empty' => $hide_empty,
		);
		$list_args          = array(
			'show_count'   => $count,
			'hierarchical' => $hierarchical,
			'taxonomy'     => 'product_brand',
			'hide_empty'   => $hide_empty,
		);
		$max_depth          = absint( isset( $instance['max_depth'] ) ? $instance['max_depth'] : $this->settings['max_depth']['std'] );
		
		$list_args['menu_order'] = false;
		$dropdown_args['depth']  = $max_depth;
		$list_args['depth']      = $max_depth;
		
		if ( 'order' === $orderby ) {
			$list_args['orderby']      = 'meta_value_num';
			$dropdown_args['orderby']  = 'meta_value_num';
			$list_args['meta_key']     = 'order';
			$dropdown_args['meta_key'] = 'order';
		}
		
		$this->current_brand   = false;
		$this->brand_ancestors = array();
		
		if ( is_tax( 'product_brand' ) ) {
			$this->current_brand   = $wp_query->queried_object;
			$this->brand_ancestors = get_ancestors( $this->current_brand->term_id, 'product_brand' );
		
		} elseif ( is_singular( 'product' ) ) {
			$terms = wc_get_product_terms(
				$post->ID,
				'product_brand',
				apply_filters(
					'dhwc_brand_widget_product_terms_args',
					array(
						'orderby' => 'parent',
						'order'   => 'DESC',
					)
				)
			);
		
			if ( $terms ) {
				$main_term           = apply_filters( 'dhwc_brand_widget_main_term', $terms[0], $terms );
				$this->current_brand   = $main_term;
				$this->brand_ancestors = get_ancestors( $main_term->term_id, 'product_brand' );
			}
		}
		
		// Show Siblings and Children Only.
		if ( $show_children_only && $this->current_brand ) {
			if ( $hierarchical ) {
				$include = array_merge(
					$this->brand_ancestors,
					array( $this->current_brand->term_id ),
					get_terms(
						'product_brand',
						array(
							'fields'       => 'ids',
							'parent'       => 0,
							'hierarchical' => true,
							'hide_empty'   => false,
						)
					),
					get_terms(
						'product_brand',
						array(
							'fields'       => 'ids',
							'parent'       => $this->current_brand->term_id,
							'hierarchical' => true,
							'hide_empty'   => false,
						)
					)
				);
				// Gather siblings of ancestors.
				if ( $this->brand_ancestors ) {
					foreach ( $this->brand_ancestors as $ancestor ) {
						$include = array_merge(
							$include,
							get_terms(
								'product_brand',
								array(
									'fields'       => 'ids',
									'parent'       => $ancestor,
									'hierarchical' => false,
									'hide_empty'   => false,
								)
							)
						);
					}
				}
			} else {
				// Direct children.
				$include = get_terms(
					'product_brand',
					array(
						'fields'       => 'ids',
						'parent'       => $this->current_brand->term_id,
						'hierarchical' => true,
						'hide_empty'   => false,
					)
				);
			}
		
			$list_args['include']     = implode( ',', $include );
			$dropdown_args['include'] = $list_args['include'];
		
			if ( empty( $include ) ) {
				return;
			}
		} elseif ( $show_children_only ) {
			$dropdown_args['depth']        = 1;
			$dropdown_args['child_of']     = 0;
			$dropdown_args['hierarchical'] = 1;
			$list_args['depth']            = 1;
			$list_args['child_of']         = 0;
			$list_args['hierarchical']     = 1;
		}
		
		$this->widget_start( $args, $instance );
		
		if ( $dropdown ) {
			wc_product_dropdown_categories(
				apply_filters(
					'dhwc_brand_widget_dropdown_args',
					wp_parse_args(
						$dropdown_args,
						array(
							'taxonomy'           => 'product_brand',
							'name'               => 'product_brand',
							'class'              => 'dropdown_product_brand',
							'show_option_none'   => __( 'Select a brand', DHWC_BRAND_TEXT_DOMAIN ),
							'show_count'         => $count,
							'hierarchical'       => $hierarchical,
							'show_uncategorized' => 0,
							'selected'           => $this->current_brand ? $this->current_brand->slug : '',
						)
					)
				)
			);
		
			wp_enqueue_script( 'selectWoo' );
			wp_enqueue_style( 'select2' );
		
			wc_enqueue_js(
				"
				jQuery( '.dropdown_product_brand' ).change( function() {
					if ( jQuery(this).val() != '' ) {
						var this_page = '';
						var home_url  = '" . esc_js( home_url( '/' ) ) . "';
						if ( home_url.indexOf( '?' ) > 0 ) {
							this_page = home_url + '&product_brand=' + jQuery(this).val();
						} else {
							this_page = home_url + '?product_brand=' + jQuery(this).val();
						}
						location.href = this_page;
					} else {
						location.href = '" . esc_js( wc_get_page_permalink( 'shop' ) ) . "';
					}
				});
		
				if ( jQuery().selectWoo ) {
					var wc_product_brand_select = function() {
						jQuery( '.dropdown_product_brand' ).selectWoo( {
							placeholder: '" . esc_js( __( 'Select a brand',DHWC_BRAND_TEXT_DOMAIN) ) . "',
							minimumResultsForSearch: 5,
							width: '100%',
							allowClear: true,
							language: {
								noResults: function() {
									return '" . esc_js( _x( 'No matches found', 'enhanced select', DHWC_BRAND_TEXT_DOMAIN ) ) . "';
								}
							}
						} );
					};
					wc_product_brand_select();
				}
			"
			);
		} else {
			include_once DHWC_BRAND_DIR.'/includes/class-dhwc-brand-list-walker.php';
			
			$list_args['walker']                     = new DHWC_Brand_List_Walker();
			$list_args['title_li']                   = '';
			$list_args['pad_counts']                 = 1;
			$list_args['show_option_none']           = __( 'No product categories exist.', 'dhwc-ajax' );
			$list_args['current_brand']           	 = ( $this->current_brand ) ? $this->current_brand->term_id : '';
			$list_args['current_brand_ancestors'] 	 = $this->brand_ancestors;
			$list_args['max_depth']                  = $max_depth;
		
			echo '<ul class="product-brands">';
		
			wp_list_categories( apply_filters( 'dhwc_brand_widget_args', $list_args ) );
		
			echo '</ul>';
		}
		
		$this->widget_end( $args );
	}
}
add_action('widgets_init', function(){
	return register_widget("DHWC_Brand_Widget");
});