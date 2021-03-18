<?php

class DHWC_Ajax_Frontend {
	
	private static $_filter_widgets_html = '';
	private static $_has_filter_widgets = false;
	
	public function __construct(){
		add_filter('body_class', array( &$this,'body_class'));
		add_action('template_redirect', array($this,'disable_redirect_single_search_result'),1);
		add_action('template_redirect', array($this,'template_redirect'),50);
		add_action('wp_head', array($this,'custom_css'),100);
	}
	
	public function custom_css(){
		$filter_widget_max_height = intval(dhwc_ajax_get_option('dhwc_ajax_filter_widget_max_height',0));
		if(!empty($filter_widget_max_height)){
			echo '<style type="text/css">.filter-widgets .filter__list{max-height:'.$filter_widget_max_height.'px}</style>';
		}
	}
	
	public static function widgets_filter_overlay(){
		echo '<div class="dhwc_ajax__overlay"></div>';
	}
	
	public function disable_redirect_single_search_result(){
		//Disable redirect single search result
		if(is_search() && dhwc_ajax_is_request())
			add_filter('woocommerce_redirect_single_search_result', '__return_false');
		
	}
	
	public function template_redirect(){
		//Storefront actions
		remove_action( 'woocommerce_after_shop_loop', 'storefront_sorting_wrapper', 9 );
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 30 );
		remove_action( 'woocommerce_after_shop_loop', 'storefront_sorting_wrapper_close', 31 );
		
		remove_action( 'woocommerce_before_shop_loop', 'storefront_sorting_wrapper', 9 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'storefront_woocommerce_pagination', 30 );
		remove_action( 'woocommerce_before_shop_loop', 'storefront_sorting_wrapper_close', 31 );
		
		//Default WooCommerce actions
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
		
		//Toobar
		add_action( 'woocommerce_before_shop_loop',array(__CLASS__,'toolbar'),100);
		
		//Pagination
		add_action( 'woocommerce_after_shop_loop',array(__CLASS__,'pagination'),100);

		//Not found
		add_action('woocommerce_no_products_found', array(__CLASS__,'toolbar'),0);
		
		//woocommerce product loop wrapper
		if(is_shop() || is_product_taxonomy()){
			add_filter('woocommerce_product_loop_start', array(__CLASS__,'woocommerce_product_loop_wrap_open'),0);
			add_filter('woocommerce_product_loop_end', array(__CLASS__,'woocommerce_product_loop_wrap_close'),99);
			
			//Compatible DHWC Product Layout
			add_filter('dhwc_layout_product_loop', array(__CLASS__,'dhwc_layout_product_loop'),10,2);
			add_filter('dhwc_layout_pagination', array(__CLASS__,'dhwc_layout_pagination'),10,3);
		}
		add_action('woocommerce_no_products_found', array(__CLASS__,'woocommerce_product_loop_wrap_open'),0);
		add_action('woocommerce_no_products_found', array(__CLASS__,'woocommerce_product_loop_wrap_close'),99);
		
		//Toolbar
		if('yes'===dhwc_ajax_get_option('dhwc_ajax_categories','yes')){ //
			add_action('dhwc_ajax_toolbar_header', array(__CLASS__,'toobar_categories'),30);
			
			add_action('dhwc_ajax_toobar_filter_buttons', array(__CLASS__,'toolbar_category_button'),10);
			add_action('dhwc_ajax_toolbar_body', array(__CLASS__,'toolbar_category_active'),25);
			
		}else{
			add_action('dhwc_ajax_toolbar_header',array(__CLASS__,'toolbar_wc'),30);
		}
		
		add_action('dhwc_ajax_toolbar_header', array(__CLASS__,'toobar_filter_buttons'),20);
		
		//Searchbox
		if('above'===dhwc_ajax_get_option('dhwc_ajax_search_filter_display','above')){
			add_action('dhwc_ajax_toolbar_body', array(__CLASS__,'toolbar_search_form'),10);
			add_action('dhwc_ajax_toobar_filter_buttons', array(__CLASS__,'toolbar_search_button'),30);
		}
		
		$filter_widget_display = dhwc_ajax_get_option('dhwc_ajax_filter_widget_display','above');
		
		if(in_array($filter_widget_display, array('above','absolute','offcanvas'))){
			
			add_action('dhwc_ajax_toobar_filter_buttons', array(__CLASS__,'toolbar_filter_button'),20);
			
			if('above'===$filter_widget_display || 'absolute'===$filter_widget_display){
				add_action('dhwc_ajax_toolbar_body', array(__CLASS__,'toolbar_filter_widgets'));
			}elseif ('offcanvas'===$filter_widget_display){
				add_action('wp_footer', array(__CLASS__,'toolbar_filter_widgets'));
			}
		}
		
		
		//Active Filter
		if('sidebar'!==$filter_widget_display){
			add_action('dhwc_ajax_toolbar_body', array(__CLASS__,'toobar_filter_active'),30);
		}
		
		
		add_action('before_filter_widgets_offcanvas', array(__CLASS__,'filter_widgets_offcanvas_header'));
		add_action('footer_toobar_filter_widgets_offcanvas', array(__CLASS__,'filter_widgets_offcanvas_backdrop'));
		
		//Pagination
		add_action('dhwc_ajax_pagination', 'woocommerce_pagination',30);
		
		$pagination_type = dhwc_ajax_get_option('dhwc_ajax_pagination_type','number');
		
		if( 'number'!==$pagination_type){
				
			wp_enqueue_script('infinite-scroll.pkgd');
				
			if(wc_get_loop_prop( 'total_pages' ) > 1){
				add_action('dhwc_ajax_pagination', array(__CLASS__,'pagination_infinite'),10);
					
				if('loadmore'===$pagination_type)
					add_action('dhwc_ajax_pagination', array(__CLASS__,'pagination_loadmore'),20);
			}
		}
	}
	
	public function body_class($classes){
		if(is_shop() || is_product_taxonomy()){
			$classes[] = 'dhwc-ajax-archive';
			$classes[] = 'dhwc-ajax-pagination-'.dhwc_ajax_get_option('dhwc_ajax_pagination_type','number');
		}
		return $classes;
	}
	
	protected static function _will_show_toolbar(){
		//
		if(dhwc_ajax_is_request()){
			return true;
		}
		
		$show_on = dhwc_ajax_get_option('dhwc_ajax_toolbox_show_on','all');
		if('shop'===$show_on && is_shop()){
			return true;
		}elseif ('taxonomy'===$show_on && is_product_taxonomy()){
			return true;
		}elseif('all'===$show_on && (is_shop() || is_product_taxonomy())){
			return true;
		}
		return false;
	}
	
	public static function toolbar(){
		if (!self::_will_show_toolbar()) {
			return;
		}
		$widget_display = dhwc_ajax_get_option('dhwc_ajax_filter_widget_display','above');
		$above_absolute = '';
		if('absolute'===$widget_display){
			$above_absolute = ' is-above-absolute';
			add_action('wp_footer', array(__CLASS__,'widgets_filter_overlay'),100);
		}
		self::_init_toolbar_filter_widgets();
		$has_category = 'yes'===dhwc_ajax_get_option('dhwc_ajax_categories','yes') ? ' has-categories':'';
		$is_dot_style = 'above'===$widget_display ? apply_filters('dhwc_ajax_is_dot_style', ' is-dot-style'):'';
		?>
		<div class="dhwc-ajax__clearfix"></div>
		<div class="dhwc-ajax__toolbar<?php echo esc_attr($above_absolute)?><?php echo esc_attr($is_dot_style)?>">
			<?php do_action('dhwc_ajax_toolbar_before')?>
			<div class="toolbar-header<?php echo esc_attr($has_category)?>">
				<?php do_action('dhwc_ajax_toolbar_header');?>
			</div>
			<div class="toolbar-body">
			<?php do_action('dhwc_ajax_toolbar_body');?>
			</div>
			
			<?php do_action('dhwc_ajax_toolbar_after')?>
		</div>
		<div class="dhwc-ajax__clearfix"></div>
		<?php 
	}
	
	public static function toobar_filter_buttons(){
		if(!has_action('dhwc_ajax_toobar_filter_buttons'))
			return;
		?>
		<div class="toolbar__filter-buttons">
			<?php 
			do_action('dhwc_ajax_toobar_filter_buttons');
			?>
		</div>
		<?php	
	}
	
	public static function toolbar_category_button(){
		?>
		<a class="toolbar__filter-category" href="#"><?php esc_html_e('Categories','dhwc-ajax')?></a>
		<?php 
	}
	
	public static function toolbar_category_active(){
		global $wp_query;
		?>
		<div class="toolbar__category-active">
		<?php 
		if(is_product_category()):
			$current_cat = $wp_query->queried_object; 
		?>
			<a class="toolbar__category-active__link" href="<?php echo get_permalink(wc_get_page_id('shop'))?>"><span><?php esc_html_e('Showing: ','dhwc-ajax') ?></span><?php echo esc_html($current_cat->name)?></a>
		
		<?php endif;?>
		</div>
		<?php 
	}
	
	public static function toolbar_filter_button(){
		?>
		<a href="#" class="toolbar__filter-button">
			<svg fill="currentColor" preserveAspectRatio="xMidYMid meet" height="18" width="18" viewBox="0 0 18 18" class="filter-button__icon">
				<g class="filter-button__icon__line is-top"><rect x="0" y="3" width="18" height="1.8" rx="0.5" ry="0.5"></rect></g>
				<g class="filter-button__icon__line is-middle"><rect x="0" y="8" width="18" height="1.8" rx="0.5" ry="0.5"></rect></g>
				<g class="filter-button__icon__line is-bottom"><rect x="0" y="13" width="18" height="1.8" rx="0.5" ry="0.5"></rect></g>
				<g class="filter-button__icon__circle is-top"><g><circle fill="currentColor" cx="4.5" cy="4" r="2.2"></circle><circle fill="#FFFFFF" cx="4.5" cy="4" r="0.8"></circle></g></g>
				<g class="filter-button__icon__circle is-middle"><g><circle fill="currentColor" cx="13.5" cy="9" r="2.2"></circle> <circle fill="#FFFFFF" cx="13.5" cy="9" r="0.8"></circle></g></g>
				<g class="filter-button__icon__circle is-bottom"><g><circle fill="currentColor" cx="9" cy="14" r="2.2"></circle> <circle fill="#FFFFFF" cx="9" cy="14" r="0.8"></circle></g></g> 
			</svg>
		<?php esc_html_e('Filter','dhwc-ajax')?>
		</a>
		<?php	
	}
	
	public static function toolbar_search_button(){
		?>
		<a href="#" class="toolbar__search-button">
			<svg class="filter-search__icon" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" width="18px" height="19px" viewBox="0 0 18 19">
				<path d="M11.2396189,0 C7.51539856,0 4.49582904,3.05484655 4.49582904,6.82264882 C4.49582904,8.39927085 5.0290999,9.84622245 5.91708754,11.0015036 L0.24730999,16.7376828 C0.0826776519,16.9042801 0,17.1220041 0,17.340896 C0,17.5592132 0.0826776519,17.7775489 0.24730999,17.9441092 C0.4113862,18.1107436 0.627089598,18.1937364 0.842959835,18.1937364 C1.05883007,18.1937364 1.27458908,18.1107436 1.43862822,17.9441462 L7.10909166,12.2074109 C8.25098867,13.1057796 9.68125644,13.6453162 11.2396375,13.6453162 C14.9638579,13.6453162 17.9834274,10.5904696 17.9834274,6.82266735 C17.9834274,3.05486509 14.9638579,1.85375901e-05 11.2396375,1.85375901e-05 L11.2396189,0 Z M11.2396189,11.939654 C8.45056437,11.939654 6.18176725,9.64438517 6.18176725,6.82266735 C6.18176725,4.00094954 8.45054583,1.70568074 11.2396189,1.70568074 C14.0286735,1.70568074 16.2974706,4.00094954 16.2974706,6.82266735 C16.2974706,9.64438517 14.0286921,11.939654 11.2396189,11.939654 Z"></path>
			</svg>
		<?php esc_html_e('Search','dhwc-ajax')?>
		</a>
		<?php 	
	}
	
	
	public static function toolbar_search_form($inline=false){
		?>
		<div class="toolbar__search-form<?php echo true===$inline ? ' is-inline': ''?>" data-current_url="<?php echo dhwc_ajax_get_current_page_url()?>">
			<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) )?>">
				<input type="search" class="search-form__input" autocomplete="off" value="" name="s" placeholder="<?php echo esc_attr__('Filter within search results','dhwc-ajax')?>">
				<input type="hidden" name="post_type" value="product">
				<a class="search-form__clear" href="#"></a>
				<svg class="filter-search__icon" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" width="18px" height="19px" viewBox="0 0 18 19">
					<path d="M11.2396189,0 C7.51539856,0 4.49582904,3.05484655 4.49582904,6.82264882 C4.49582904,8.39927085 5.0290999,9.84622245 5.91708754,11.0015036 L0.24730999,16.7376828 C0.0826776519,16.9042801 0,17.1220041 0,17.340896 C0,17.5592132 0.0826776519,17.7775489 0.24730999,17.9441092 C0.4113862,18.1107436 0.627089598,18.1937364 0.842959835,18.1937364 C1.05883007,18.1937364 1.27458908,18.1107436 1.43862822,17.9441462 L7.10909166,12.2074109 C8.25098867,13.1057796 9.68125644,13.6453162 11.2396375,13.6453162 C14.9638579,13.6453162 17.9834274,10.5904696 17.9834274,6.82266735 C17.9834274,3.05486509 14.9638579,1.85375901e-05 11.2396375,1.85375901e-05 L11.2396189,0 Z M11.2396189,11.939654 C8.45056437,11.939654 6.18176725,9.64438517 6.18176725,6.82266735 C6.18176725,4.00094954 8.45054583,1.70568074 11.2396189,1.70568074 C14.0286735,1.70568074 16.2974706,4.00094954 16.2974706,6.82266735 C16.2974706,9.64438517 14.0286921,11.939654 11.2396189,11.939654 Z"></path>
				</svg>
			</form>
		</div>
		<?php 	
	}
	
	public static function toolbar_wc(){
		?>
		<div class="toolbar-wc">
			<?php 
			if('yes'===dhwc_ajax_get_option('dhwc_ajax_display_result_count','yes')){
				woocommerce_result_count();
			}
			
			if('yes'===dhwc_ajax_get_option('dhwc_ajax_display_ordering','yes')){
				woocommerce_catalog_ordering();
			}
			?>
		</div>
		<?php 	
	}
	
	private static function _init_toolbar_filter_widgets(){
		$widget_html = false;
		ob_start();
		$filter_widget_display = dhwc_ajax_get_option('dhwc_ajax_filter_widget_display','above');
		?>
		<div class="toolbar__filter-widgets display-<?php echo esc_attr($filter_widget_display)?>">
			<?php 
			do_action("before_filter_widgets_$filter_widget_display");
			
			if(!is_active_sidebar('dhwc-ajax')){
				echo __('Please add Product Filter Widget type to sidebar:','dhwc-ajax').'<strong>'.__('DHWC Ajax','dhwc-ajax').'</strong>';
			}else{
				$sidebars_widgets = wp_get_sidebars_widgets();
				$count = count( $sidebars_widgets[ 'dhwc-ajax' ] );
				$class= $count <= 6 ? 'widgets-'.$count :'cell-widgets';
				?>
				<div class="filter-widgets <?php echo esc_attr($class)?>">
					<?php 
					ob_start();
					dynamic_sidebar('dhwc-ajax');
					$widget_html = ob_get_clean();
					echo $widget_html;
					?>
				</div>
				<?php 
			}
			
			do_action("after_filter_widgets_$filter_widget_display");?>
		</div>
		<?php do_action("footer_toobar_filter_widgets_$filter_widget_display");?>
		<?php 
		if($widget_html){
			self::$_has_filter_widgets = true;
		}
		self::$_filter_widgets_html = ob_get_clean();
	}
	
	public static function toolbar_filter_widgets(){
		echo self::$_filter_widgets_html;
	}
	
	public static function filter_widgets_offcanvas_header(){
		?>
		<div class="filter-widgets__header">
			<h3><?php esc_html_e('Filter & Refine','dhwc-ajax')?><a class="filter-button__close" href="#" title="<?php echo esc_attr__('Close','dhwc-ajax')?>"><span class="dhwc-ajax-sr-only"><?php echo esc_html__('Close','dhwc-ajax')?></span><svg xmlns="http://www.w3.org/2000/svg" version="1.1" x="0" y="0" width="12" height="12" viewBox="1.1 1.1 12 12" enable-background="new 1.1 1.1 12 12" xml:space="preserve"><path d="M8.3 7.1l4.6-4.6c0.3-0.3 0.3-0.8 0-1.2 -0.3-0.3-0.8-0.3-1.2 0L7.1 5.9 2.5 1.3c-0.3-0.3-0.8-0.3-1.2 0 -0.3 0.3-0.3 0.8 0 1.2L5.9 7.1l-4.6 4.6c-0.3 0.3-0.3 0.8 0 1.2s0.8 0.3 1.2 0L7.1 8.3l4.6 4.6c0.3 0.3 0.8 0.3 1.2 0 0.3-0.3 0.3-0.8 0-1.2L8.3 7.1z"></path></svg></a></h3>
		</div>
		<?php 
	}
	
	public static function filter_widgets_offcanvas_backdrop(){
		?>
		<div class="filter-widgets__backdrop"></div>
		<?php 
	}
	
	
	public static function toobar_filter_active(){
		?>
		<div class="toolbar__filter-active">
			<?php the_widget( 'DHWC_Ajax_Widget_Layered_Nav_Filter',apply_filters('dhwc_ajax_filter_active_instance', array('title'=>''))); ?>
		</div>
		<?php 	
	}
	
	public static function toobar_categories(){
		global $wp_query;
		
		$shop_url			 	=  get_permalink(wc_get_page_id('shop'));
		$shop_all_title         = apply_filters('dhwc_ajax_shop_all_title',__('All','dhwc-ajax'));
		$shop_all_link			= '';
		$hide_empty 			= true;
		$orderby 				= 'slug'; 
		$order 	        		= 'asc';
		
		$is_cat					= false;
		$has_children 			= false; //Check children
		$current_cat			= false;
		$current_cat_link       = '';
		$current_parent_id		= 0;
		$current_parent			= false;
		$current_parent_link    = '';
		
		$cat_selected 			= false;
		
		
		$query_args = array(
			'orderby'		=> $orderby, // Note: 'name' sorts by product category "menu/sort order"
			'order'			=> $order,
			'hide_empty'	=> $hide_empty,
			'hierarchical'	=> false,
			'taxonomy'		=> 'product_cat'
		);
		
		if ( apply_filters('dhwc_ajax_toobar_categories_sort_order', false) ) {
			$query_args['orderby']      = 'meta_value_num';
			$query_args['meta_key']     = 'order';
		}
		
		if( is_tax( 'product_cat' )){
			$is_cat = true;
			$current_cat   		= $wp_query->queried_object;
			$current_parent_id 	= $wp_query->queried_object->parent;
			$current_parent     = $current_parent_id ? get_term($current_parent_id)  : false;
			
			
			// Direct children.
			$include = array_merge(
				get_terms(
					'product_cat',
					array(
						'fields'       => 'ids',
						'parent'       => $current_cat->term_id,
						'hierarchical' => false,
						'hide_empty'   => $hide_empty,
					)
				)
			);
			if(!empty($include)){
				$query_args['include'] = implode(',', $include);
				$has_children = true;
				$current_cat_link 	= self::_create_category_link(get_term_link($current_cat->term_id,'product_cat'), $current_cat->name,' js-active', false);
			}elseif($current_parent_id){
				$has_children = false;
				$query_args['parent'] = $current_parent_id;
			}else{
				$query_args['parent'] = 0;
			}
		}else{
			$query_args['parent'] = 0;
		}
		
		$categories = get_categories( apply_filters('dhwc_ajax_toobar_categories_query_args', $query_args) );
		if(!empty($categories)):
		?>
		<div class="toobar__categories">
			<ul class="categories__list">
				<?php 
				$categories_list_link = '';
				
				foreach ($categories as $category):
					$css_class = '';
					if($current_cat && (int)$category->term_id === (int) $current_cat->term_id){
						$cat_selected = true;
						$css_class= ' js-active';
					}
					$categories_list_link .= self::_create_category_link(get_term_link($category->term_id,'product_cat'), $category->name, $css_class, false);
				endforeach;
				
				if($current_parent){
					if($has_children){
						/*TODO: Always display parent*/
						$current_parent_link = self::_create_category_link(get_term_link($current_parent->term_id,'product_cat'), $current_parent->name, ' is-parent-item', false);
					}else {
						$current_parent_link = self::_create_category_link(get_term_link($current_parent->term_id,'product_cat'), $current_parent->name, '', false);
						$parent_ancestors = get_ancestors($current_parent->term_id,'product_cat');
					
						if(empty($parent_ancestors)){
							$shop_all_link = self::_create_category_link($shop_url, $shop_all_title, ' is-shop-link is-parent-item', false);
						}else {
							$parents = get_term(current($parent_ancestors),'product_cat');
							$shop_all_link = self::_create_category_link(get_term_link($parents->term_id,'product_cat'), $parents->name, ' is-parent-item', false);
						}
							
					}
				}else{
					$shop_all_link_class = ' is-shop-link';
					if(is_shop())
						$shop_all_link_class .= ' js-active';
					
					if($is_cat && $has_children)
						$shop_all_link_class .= ' is-parent-item';
					
					$shop_all_link = self::_create_category_link($shop_url, $shop_all_title, $shop_all_link_class, false);
				}
				echo $shop_all_link.$current_parent_link.$current_cat_link.$categories_list_link;
				?>
			</ul>
		</div>
		<?php 	
		endif;
	}
	
	private static function _create_category_link($link, $name, $css_class='',$echo = true){
		ob_start();
		?>
		<li class="category__item<?php echo esc_attr($css_class)?>">
			<a class="category__item-link" href="<?php echo esc_url($link) ?>"><?php echo esc_html($name)?></a>
		</li>
		<?php
		if($echo){
			echo ob_get_clean();
		}else{
			return ob_get_clean();
		}
	}
	
	public static function pagination(){
		if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
			return;
		}
		$pagination_type = dhwc_ajax_get_option('dhwc_ajax_pagination_type','number');
		$enable_ajax = apply_filters('dhwc_ajax_pagination_number_ajax_enable', true);
		?>
		<div class="dhwc-ajax__pagination is-<?php echo esc_attr($pagination_type) ?><?php echo $enable_ajax ? ' is-ajax-pagination':''?>">
			<?php do_action('dhwc_ajax_pagination');?>
		</div>
		<?php 
	}
	
	public static function pagination_infinite(){
		?>
		<div class="pagination__loading"><i></i><i></i><i></i><i></i></div>
		<?php
	}
	
	public static function pagination_loadmore(){
		?>
		<a href="#" class="pagination__button-loadmore"><span><?php echo esc_html__('Load more','dhwc-ajax')?></span></a>
		<?php 	
		
	}
	
	public static function dhwc_layout_product_loop($html, $atts){
		if('main'!== $atts['query_type']){
			return $html;	
		}
		return self::woocommerce_product_loop_wrap_open($html).self::woocommerce_product_loop_wrap_close();
	}
	
	public static function dhwc_layout_pagination($html, $query, $atts){
		if('main'!== $atts['query_type']){
			return $html;
		}
		return self::pagination();
	}
	
	public static function woocommerce_product_loop_wrap_open($html=''){
		$result_count = '';
		$has_filter_widget = self::$_has_filter_widgets ? 'yes':'no';
		if(is_search()){
			$result_count = sprintf(__('Search result for %s','dhwc-ajax'),' &ldquo;'.get_search_query().'&rdquo;');
		}
		echo '<div class="woocommerce-products-loop-wrapper" data-result_count="'.esc_attr($result_count).'" data-has_filter_widget="'.$has_filter_widget.'" data-found_posts="'. $GLOBALS['wp_query']->found_posts.'">'.$html;
	}
	
	public static function woocommerce_product_loop_wrap_close($html=''){
		echo $html.'</div>';	
	}
	
}
new DHWC_Ajax_Frontend();