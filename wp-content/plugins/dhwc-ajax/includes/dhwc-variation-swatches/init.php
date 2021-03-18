<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if(!defined('DHWC_VARIATION_SWATCHES_DIR'))
	define( 'DHWC_VARIATION_SWATCHES_DIR', trailingslashit(DHWC_AJAX_DIR.'/includes/dhwc-variation-swatches') );
if(!defined('DHWC_VARIATION_SWATCHES_URL'))
	define( 'DHWC_VARIATION_SWATCHES_URL', trailingslashit(DHWC_AJAX_URL.'/includes/dhwc-variation-swatches'));

final class DHWC_Variation_Swatches {
	/**
	 * The single instance of the class
	 *
	 * @var DHWC_Variation_Swatches
	 */
	protected static $instance = null;

	/**
	 * Extra attribute types
	 *
	 * @var array
	 */
	public $types = array();

	/**
	 * Main instance
	 *
	 * @return DHWC_Variation_Swatches
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->types = array(
			'color' => __( 'Color', 'dhwc-ajax' ),
			'image' => __( 'Image', 'dhwc-ajax' ),
			'label' => __( 'Label', 'dhwc-ajax' ),
		);
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		require_once 'includes/class-admin.php';
		require_once 'includes/class-frontend.php';
	}

	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		
		add_filter( 'product_attributes_type_selector', array( $this, 'add_attribute_types' ) );

		if ( is_admin() ) {
			add_action( 'init', array( 'DHWC_Variation_Swatches_Admin', 'instance' ) );
		} else {
			add_action( 'init', array( 'DHWC_Variation_Swatches_Frontend', 'instance' ) );
		}
	}
	

	/**
	 * Add extra attribute types
	 * Add color, image and label type
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function add_attribute_types( $types ) {
		$types = array_merge( $types, $this->types );

		return $types;
	}

	/**
	 * Get attribute's properties
	 *
	 * @param string $taxonomy
	 *
	 * @return object
	 */
	public function get_tax_attribute( $taxonomy ) {
		global $wpdb;

		$attr = substr( $taxonomy, 3 );
		$attr = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attr'" );

		return $attr;
	}

	/**
	 * Instance of admin
	 *
	 * @return DHWC_Variation_Swatches_Admin
	 */
	public function admin() {
		return DHWC_Variation_Swatches_Admin::instance();
	}

	/**
	 * Instance of frontend
	 *
	 * @return DHWC_Variation_Swatches_Frontend
	 */
	public function frontend() {
		return DHWC_Variation_Swatches_Frontend::instance();
	}
}
new DHWC_Variation_Swatches();

function DHWC_Variation_Swatches(){
	return DHWC_Variation_Swatches::instance();
}

