<?php
if ( class_exists( 'DHWC_Ajax_Admin_Settings', false ) ) {
	return new DHWC_Ajax_Admin_Settings();
}
class DHWC_Ajax_Admin_Settings extends WC_Settings_Page {
	
	/**
	 * Constructor.
	 */
	public function __construct(){
		$this->id = 'dhwc-ajax';
		$this->label = __('DHWC Ajax Toolbox','dhwc-ajax');
		parent::__construct();
	}
	
	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = array(
			array(
				'title' => __( 'General', 'dhwc-ajax' ),
				'type'  => 'title',
				'id'    => 'dhwc_ajax_general',
			),
			array(
				'title'    => __( 'Show Ajax toolbox on', 'dhwc-ajax' ),
				'id'       => 'dhwc_ajax_toolbox_show_on',
				'default'  => 'all',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'options'  => array(
					'all'       => __( 'All', 'dhwc-ajax' ),
					'shop' 		=> __( 'Only Shop page', 'dhwc-ajax' ),
					'taxonomy'  => __( 'Product taxonomy archive', 'dhwc-ajax' ),
					'hide'		=> __( 'Hide','dhwc-ajax')
				),
			),
			array(
				'title'           => __( 'Header', 'dhwc-ajax' ),
				'desc'            => __( 'Display Categories Menu', 'dhwc-ajax' ),
				'id'              => 'dhwc_ajax_categories',
				'default'         => 'yes',
				'type'            => 'checkbox',
				'checkboxgroup'   => 'start',
				'hide_if_checked' => 'option',
			),
			array(
				'title'           => __( 'Result count', 'dhwc-ajax' ),
				'desc'            => __( 'Display result count', 'dhwc-ajax' ),
				'id'              => 'dhwc_ajax_display_result_count',
				'default'         => 'no',
				'type'            => 'checkbox',
				'checkboxgroup'   => '',
				'hide_if_checked' => 'yes',
				'autoload'        => false,
			),
			array(
				'title'           => __( 'Ordering', 'dhwc-ajax' ),
				'desc'            => __( 'Display ordering', 'dhwc-ajax' ),
				'id'              => 'dhwc_ajax_display_ordering',
				'default'         => 'no',
				'type'            => 'checkbox',
				'checkboxgroup'   => 'end',
				'hide_if_checked' => 'yes',
				'autoload'        => false,
			),
			array(
				'title'           => __( 'Ajax Single Add to cart', 'dhwc-ajax' ),
				'desc'            => __( 'Enable Ajax Single Add to cart', 'dhwc-ajax' ),
				'id'              => 'dhwc_ajax_single_add_to_cart',
				'default'         => 'yes',
				'type'            => 'checkbox',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'dhwc_ajax_general',
			),
			array(
				'title' => __( 'Filter', 'dhwc-ajax' ),
				'type'  => 'title',
				'id'    => 'dhwc_ajax_filter_widget',
			),
			array(
				'title'    => __( 'Widgets filter display', 'dhwc-ajax' ),
				'id'       => 'dhwc_ajax_filter_widget_display',
				'default'  => 'above',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'desc'	   => __('If use as Sidebar, please add DHWC Ajax Filter widgets type for your sidebar'),
				'desc_tip' => true,
				'options'  => array(
					'above'         => __( 'Above product list', 'dhwc-ajax' ),
					'absolute'      => __( 'Absolute', 'dhwc-ajax' ),
					'sidebar' 		=> __( 'Sidebar', 'dhwc-ajax' ),
					'offcanvas'   	=> __( 'Off Canvas', 'dhwc-ajax' ),
					'hide'			=> __('Hide','dhwc-ajax')
				),
			),
			array(
				'title'           => __( 'Filter widget max height (px)', 'dhwc-ajax' ),
				'desc'            => __( 'Will display scrollbar for filter widgets have content longer. Default fit content', 'dhwc-ajax' ),
				'desc_tip' 		  => true,
				'placeholder'     => '100',
				'id'              => 'dhwc_ajax_filter_widget_max_height',
				'type'            => 'text',
			),
			array(
				'title'    => __( 'Search filter display', 'dhwc-ajax' ),
				'id'       => 'dhwc_ajax_search_filter_display',
				'default'  => 'above',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'desc'	   => __('If use as Sidebar, please add DHWC Ajax Filter widgets type for your sidebar'),
				'desc_tip' => true,
				'options'  => array(
					'above'         => __( 'Show', 'dhwc-ajax' ),
					'hide'			=> __('Hide','dhwc-ajax')
				),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'dhwc_ajax_filter_widget',
			),
			array(
				'title' => __( 'Pagination', 'dhwc-ajax' ),
				'type'  => 'title',
				'id'    => 'dhwc_ajax_pagination',
			),
			array(
				'title'    => __( 'Type', 'dhwc-ajax' ),
				'id'       => 'dhwc_ajax_pagination_type',
				'default'  => 'number',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'options'  => array(
					'number'        => __( 'Number', 'dhwc-ajax' ),
					'infinite' 		=> __( 'Infinite Scroll', 'dhwc-ajax' ),
					'loadmore'   	=> __( 'Load More button', 'dhwc-ajax' ),
				),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'dhwc_ajax_pagination',
			),
			array(
				'title' => __( 'Javascript elements selector', 'dhwc-ajax' ),
				'type'  => 'title',
				'id'    => 'dhwc_ajax_elements',
			),
			array(
				'title'           => __( 'Product list', 'dhwc-ajax' ),
				'desc'            => __( 'Enter product list class, default is: ul.products', 'dhwc-ajax' ),
				'desc_tip' 		  => true,
				'placeholder'     => 'ul.products',
				'id'              => 'dhwc_ajax_elment_product_list',
				'type'            => 'text',
			),
			array(
				'title'           => __( 'Product item', 'dhwc-ajax' ),
				'desc'            => __( 'Enter product list item class, default is: li.product', 'dhwc-ajax' ),
				'desc_tip' 		  => true,
				'placeholder'     => 'li.product',
				'id'              => 'dhwc_ajax_elment_product_item',
				'type'            => 'text',
			),
			array(
				'title'           => __( 'Result count', 'dhwc-ajax' ),
				'desc'            => __( 'Enter result count class, default is: .woocommerce-result-count', 'dhwc-ajax' ),
				'desc_tip' 		  => true,
				'placeholder'     => '.woocommerce-result-count',
				'id'              => 'dhwc_ajax_elment_result_count',
				'type'            => 'text',
			),
			array(
				'title'           => __( 'Pagination wrapper', 'dhwc-ajax' ),
				'desc'            => __( 'Enter pagination wrapper class, default is: .woocommerce-pagination', 'dhwc-ajax' ),
				'desc_tip' 		  => true,
				'placeholder'     => '.woocommerce-pagination',
				'id'              => 'dhwc_ajax_elment_pagination_wrapper',
				'type'            => 'text',
			),
			array(
				'title'           => __( 'Pagination next', 'dhwc-ajax' ),
				'desc'            => __( 'Enter pagination next class, default is: .next', 'dhwc-ajax' ),
				'desc_tip' 		  => true,
				'placeholder'     => '.next',
				'id'              => 'dhwc_ajax_elment_pagination',
				'type'            => 'text',
			),
			array(
				'title'           => __( 'Sidebar', 'dhwc-ajax' ),
				'desc'            => __( 'Enter sidebar class, default is: .widget-area', 'dhwc-ajax' ),
				'desc_tip' 		  => true,
				'placeholder'     => '.widget-area',
				'id'              => 'dhwc_ajax_elment_sidebar_filter',
				'type'            => 'text',
			),
			array(
				'title'           => __( 'Page title', 'dhwc-ajax' ),
				'desc'            => __( 'Enter page title class, default is: .entry-title', 'dhwc-ajax' ),
				'desc_tip' 		  => true,
				'placeholder'     => '.entry-title',
				'id'              => 'dhwc_ajax_elment_page_title',
				'type'            => 'text',
			),
			array(
				'title'           => __( 'Page breadcrumb', 'dhwc-ajax' ),
				'desc'            => __( 'Enter page breadcrumbs class, default is: .woocommerce-breadcrumb', 'dhwc-ajax' ),
				'desc_tip' 		  => true,
				'placeholder'     => '.woocommerce-breadcrumb',
				'id'              => 'dhwc_ajax_elment_page_breadcrumb',
				'type'            => 'text',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'dhwc_ajax_elements',
			),
		);
		
		return apply_filters( 'dhwc_ajax_get_settings_' . $this->id, $settings );
	}
	
	/**
	 * Output the settings.
	 */
	public function output() {
		$settings = $this->get_settings();

		WC_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Save settings.
	 */
	public function save() {
		$settings = $this->get_settings();

		WC_Admin_Settings::save_fields( $settings );
	}
}

return new DHWC_Ajax_Admin_Settings();