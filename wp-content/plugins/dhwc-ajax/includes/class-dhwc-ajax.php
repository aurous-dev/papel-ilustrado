<?php

class DHWC_Ajax{
	
	private static $_instance = null;
	
	private static $_single_ajax_add_to_cart_message = '';
	
	public function init(){
		add_action( 'plugins_loaded', array($this,'plugins_loaded'));
	}

	public function plugins_loaded(){
		
		if(!function_exists('is_plugin_active')){
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require plugin.php to use is_plugin_active() below
		}
		
		if ( !is_plugin_active( 'woocommerce/woocommerce.php' )) {
			add_action('admin_notices', array( $this,'woocommerce_notice' ) );
			return ;
		}
		
		load_plugin_textdomain( 'dhwc-ajax', false, plugin_basename(DHWC_AJAX_DIR) . '/languages' );
	
		$this->_includes();

		//Register sidebar
		add_action('after_setup_theme', array($this,'register_sidebar'),1);
		
		//Define ajax controller
		add_action( 'init', array( __CLASS__, 'init_single_add_to_cart_action' ), 1 );
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 1 );
		add_action( 'template_redirect', array( __CLASS__, 'do_ajax' ), 1 );
		
		//Ajax
		add_action('dhwc_ajax_add_to_cart', array(__CLASS__,'ajax_single_add_to_cart'));
		
		//Remove all transients
		add_action('woocommerce_new_product', array(__CLASS__,'delete_transients'));
		add_action('woocommerce_update_product', array(__CLASS__,'delete_transients'));
		add_action('woocommerce_delete_product_transients', array(__CLASS__,'delete_transients'));

	}
	
	public static function init_single_add_to_cart_action(){
		if('yes' === dhwc_ajax_get_option('dhwc_ajax_single_add_to_cart','yes')){
			//remove default WC Add to cart action
			remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );
			//Ajax add to cart params
			add_action('woocommerce_after_add_to_cart_button', array(__CLASS__,'ajax_single_product_add_to_cart_params'));
			add_filter( 'woocommerce_add_to_cart_fragments',array(__CLASS__,'add_to_cart_fragments') );
		}
		
	}
	
	public static function define_ajax(){
		if ( ! empty( $_GET['dhwc-ajax'] ) ) {
			if(!defined('DOING_AJAX'))
				define('DOING_AJAX', true);
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
			}
			$GLOBALS['wpdb']->hide_errors();
		}
	}
	
	private static function _ajax_headers(){
		send_origin_headers();
		@header( 'Content-Type: text/html; charset=' . dhwc_ajax_get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		wc_nocache_headers();
		status_header( 200 );
	}
	
	public static function do_ajax(){
		global $wp_query;
		
		if ( ! empty( $_GET['dhwc-ajax'] ) ) {
			$wp_query->set( 'dhwc-ajax', sanitize_text_field( wp_unslash( $_GET['dhwc-ajax'] ) ) );
		}
		
		$action = $wp_query->get( 'dhwc-ajax' );
		
		if ( $action ) {
			self::_ajax_headers();
			$action = sanitize_text_field( $action );
			do_action( 'dhwc_ajax_' . $action );
			wp_die();
		}
	}
	
	public function woocommerce_notice(){
		$plugin = get_plugin_data(__FILE__);
		echo '<div class="updated"><p>' . sprintf(__('<strong>%s</strong> requires <strong><a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a></strong> plugin to be installed and activated on your site.', 'dhwc-ajax'), $plugin['Name']) . '</p></div>';
	}
	
	private function _includes(){
		//Functions
		include_once DHWC_AJAX_DIR.'/includes/functions.php';
		
		//Query
		include_once DHWC_AJAX_DIR.'/includes/class-dhwc-ajax-query.php';
		
		//Assets
		include_once DHWC_AJAX_DIR.'/includes/class-dhwc-ajax-assets.php';
		
		//Products Brand
		include_once DHWC_AJAX_DIR.'/includes/dhwc-brand/dhwc-brand.php';
		
		//Widgets
		include_once DHWC_AJAX_DIR.'/includes/class-dhwc-ajax-widget.php';
		
		include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-layered-nav-filters.php';
		include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-product-taxonomy-filter.php';
		include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-product-category-filter.php';
		include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-product-tag-filter.php';
		include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-product-brand-filter.php';
		include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-weight-dimensions-filter.php';
		include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-product-sorting.php';
		include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-price-range-filter.php';
		include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-stock-filter.php';
		include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-product-status-filter.php';
		
		
		if(defined('ACF')){
			include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-acf-filter.php';
		}
		
		//Variation Swatches
		if(!defined('DHWC_VARIATION_SWATCHES_DIR')){
			include_once DHWC_AJAX_DIR.'/includes/dhwc-variation-swatches/init.php';
			include_once DHWC_AJAX_DIR.'/includes/widgets/class-dhwc-ajax-widget-attribute-filter.php';
		}
		
		if(is_admin()){
			include_once DHWC_AJAX_DIR.'/includes/class-dhwc-ajax-admin.php';
		}else{ 
			include_once DHWC_AJAX_DIR.'/includes/class-dhwc-ajax-frontend.php';
		}
		
	}
	
	public function register_sidebar(){
		//Filter Sidebar
		$is_closed = apply_filters('dhwc_ajax_offcanvas_widget_closed', false) ? ' is-closed':'';
		
		register_sidebar(
			apply_filters('dhwc_ajax_register_sidebar_args', array(
				'name' 			=> __( 'DHWC Ajax', 'dhwc-ajax' ),
				'description'	=> __( 'Add widgets here to appear in your sidebar.', 'dhwc-ajax' ),
				'id' 			=> 'dhwc-ajax',
				'before_widget' => '<div id="%1$s" class="widget'.$is_closed.' %2$s">',
				'after_widget' 	=> '</div></div></div>',
				'before_title' 	=> '<h4 class="widgettitle"><span>',
				'after_title' 	=> '</span><span class="dhwc-ajax-toggle-indicator"></span></h4><div class="widgetcontent"><div class="filter__list">'
			))
		);
	}
	
	public static function add_to_cart_fragments($fragments){
		$fragments['dhwc_ajax_add_to_cart_message'] = apply_filters('dhwc_ajax_add_to_cart_message', self::$_single_ajax_add_to_cart_message);
		return $fragments;
	}
	
	public static function ajax_single_product_add_to_cart_params(){
		global $product;
		if('external' === $product->get_type()){
			return;
		}
		?>
		<input type="hidden" name="dhwc_ajax_product_id" id="dhwc_ajax_product_id" value="<?php echo esc_attr($product->get_id())?>">
		<input type="hidden" name="dhwc_ajax_product_type" id="dhwc_ajax_product_type" value="<?php echo esc_attr($product->get_type())?>">
		<?php 
	}
	
	public static function ajax_single_add_to_cart(){
		$product_id = isset($_POST['dhwc_ajax_product_id']) ? (int) $_POST['dhwc_ajax_product_id'] : 0;
		
		if(empty($product_id)){
			return;
		}
		
		wc_nocache_headers();
		
		add_filter( 'woocommerce_cart_redirect_after_add', '__return_false' );
		
		if(!isset($_REQUEST['add-to-cart'])){
			$_REQUEST['add-to-cart'] = $product_id;
		}
		
		WC_Form_Handler::add_to_cart_action(false);
		
		ob_start();
		
		wc_print_notices();
		
		self::$_single_ajax_add_to_cart_message = apply_filters('dhwc_ajax_single_ajax_add_to_cart_message', ob_get_clean());
		
		WC_AJAX::get_refreshed_fragments();
	}
	
	public static function delete_transients(){
		
		delete_transient('dhwc_ajax_brand_filtered_counts');
		delete_transient('dhwc_ajax_category_filtered_counts');
		delete_transient('dhwc_ajax_tag_filtered_counts');
		delete_transient('dhwc_ajax_attribute_filtered_counts');
		delete_transient('dhwc_ajax_taxonomy_filtered_counts');
	}
	
	/**
	 *
	 * @return DHWC_Ajax
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

DHWC_Ajax::get_instance()->init();