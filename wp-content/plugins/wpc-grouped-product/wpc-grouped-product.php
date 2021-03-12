<?php
/*
Plugin Name: WPC Grouped Product for WooCommerce
Plugin URI: https://wpclever.net/
Description: WPC Grouped Product helps you made up standalone products that are presented as a group.
Version: 2.3.1
Author: WPClever.net
Author URI: https://wpclever.net
Text Domain: wpc-grouped-product
Domain Path: /languages/
Requires at least: 4.0
Tested up to: 5.6.0
WC requires at least: 3.0
WC tested up to: 4.9.0
*/

defined('ABSPATH') || exit;

!defined('WOOSG_VERSION') && define('WOOSG_VERSION', '2.3.1');
!defined('WOOSG_URI') && define('WOOSG_URI', plugin_dir_url(__FILE__));
!defined('WOOSG_SUPPORT') && define('WOOSG_SUPPORT', 'https://wpclever.net/support?utm_source=support&utm_medium=woosg&utm_campaign=wporg');
!defined('WOOSG_REVIEWS') && define('WOOSG_REVIEWS', 'https://wordpress.org/support/plugin/wpc-grouped-product/reviews/?filter=5');
!defined('WOOSG_CHANGELOG') && define('WOOSG_CHANGELOG', 'https://wordpress.org/plugins/wpc-grouped-product/#developers');
!defined('WOOSG_DISCUSSION') && define('WOOSG_DISCUSSION', 'https://wordpress.org/support/plugin/wpc-grouped-product');
!defined('WPC_URI') && define('WPC_URI', WOOSG_URI);

include 'includes/wpc-dashboard.php';
include 'includes/wpc-menu.php';
include 'includes/wpc-kit.php';

if (!function_exists('woosg_init')) {
	add_action('plugins_loaded', 'woosg_init', 11);

	function woosg_init()
	{
		// load text-domain
		load_plugin_textdomain('wpc-grouped-product', false, basename(__DIR__) . '/languages/');

		if (!function_exists('WC') || !version_compare(WC()->version, '3.0.0', '>=')) {
			add_action('admin_notices', 'woosg_notice_wc');

			return;
		}

		if (!class_exists('WC_Product_Woosg') && class_exists('WC_Product')) {
			class WC_Product_Woosg extends WC_Product
			{
				public function __construct($product = 0)
				{
					parent::__construct($product);
				}

				public function get_type()
				{
					return 'woosg';
				}

				public function add_to_cart_url()
				{
					$product_id = $this->get_id();

					return apply_filters('woocommerce_product_add_to_cart_url', get_permalink($product_id), $this);
				}

				public function add_to_cart_text()
				{
					if ($this->is_purchasable() && $this->is_in_stock()) {
						$text = get_option('_woosg_archive_button_select');

						if (empty($text)) {
							$text = esc_html__('Select options', 'wpc-grouped-product');
						}
					} else {
						$text = get_option('_woosg_archive_button_read');

						if (empty($text)) {
							$text = esc_html__('Read more', 'wpc-grouped-product');
						}
					}

					return apply_filters('woosg_product_add_to_cart_text', $text, $this);
				}

				public function single_add_to_cart_text()
				{
					$text = get_option('_woosg_single_button_add');

					if (empty($text)) {
						$text = esc_html__('Add to cart', 'wpc-grouped-product');
					}

					return apply_filters('woosg_product_single_add_to_cart_text', $text, $this);
				}

				// extra functions

				public function has_variables()
				{
					if ($items = $this->get_items()) {
						foreach ($items as $item) {
							$item_product = wc_get_product($item['id']);

							if ($item_product && $item_product->is_type('variable')) {
								return true;
							}
						}
					}

					return false;
				}

				public function get_items()
				{
					$product_id = $this->id;
					$data       = array();

					if ($ids = get_post_meta($product_id, 'woosg_ids', true)) {
						$items = explode(',', $ids);

						if (is_array($items) && count($items) > 0) {
							foreach ($items as $item) {
								$item_data = explode('/', $item);
								$data[]    = array(
									'id'  => absint(isset($item_data[0]) ? $item_data[0] : 0),
									'qty' => (float) (isset($item_data[1]) ? $item_data[1] : 0)
								);
							}
						}
					}

					if (count($data) > 0) {
						return $data;
					}

					return false;
				}
			}
		}

		if (!class_exists('WPCleverWoosg')) {
			class WPCleverWoosg
			{
				function __construct()
				{
					// Menu
					add_action('admin_menu', array($this, 'woosg_admin_menu'));

					// Enqueue frontend scripts
					add_action('wp_enqueue_scripts', array($this, 'woosg_wp_enqueue_scripts'), 99);

					// Enqueue backend scripts
					add_action('admin_enqueue_scripts', array($this, 'woosg_admin_enqueue_scripts'));

					// Backend AJAX search
					add_action('wp_ajax_woosg_get_search_results', array($this, 'woosg_get_search_results'));

					// Add to selector
					add_filter('product_type_selector', array($this, 'woosg_product_type_selector'));

					// Product data tabs
					add_filter('woocommerce_product_data_tabs', array($this, 'woosg_product_data_tabs'), 10, 1);

					// Product tab
					if (get_option('_woosg_position', 'above') === 'tab') {
						add_filter('woocommerce_product_tabs', array($this, 'woosg_product_tabs'));
					}

					// Product filters
					add_filter('woocommerce_product_filters', array($this, 'woosg_product_filters'));

					// Product data panels
					add_action('woocommerce_product_data_panels', array($this, 'woosg_product_data_panels'));
					add_action('woocommerce_process_product_meta_woosg', array($this, 'woosg_save_option_field'));

					// Price html
					add_filter('woocommerce_get_price_html', array($this, 'woosg_get_price_html'), 99, 2);

					// Add to cart form & button
					add_action('woocommerce_woosg_add_to_cart', array($this, 'woosg_add_to_cart_form'));
					add_action('woocommerce_before_add_to_cart_button', array($this, 'woosg_add_to_cart_button'));

					// Add to cart
					add_filter('woocommerce_add_cart_item_data', array($this, 'woosg_add_cart_item_data'), 10, 2);
					add_action('woocommerce_add_to_cart', array($this, 'woosg_add_to_cart'), 10, 6);
					add_filter('woocommerce_get_cart_item_from_session', array(
						$this,
						'woosg_get_cart_item_from_session'
					), 10, 2);

					// Cart contents instead of woocommerce_before_calculate_totals, prevent price error on mini-cart
					add_filter('woocommerce_get_cart_contents', array($this, 'woosg_get_cart_contents'), 10, 1);

					// Admin
					add_filter('display_post_states', array($this, 'woosg_display_post_states'), 10, 2);

					// Add settings link
					add_filter('plugin_action_links', array($this, 'woosg_action_links'), 10, 2);
					add_filter('plugin_row_meta', array($this, 'woosg_row_meta'), 10, 2);

					// Search filters
					if (get_option('_woosg_search_sku', 'no') === 'yes') {
						add_filter('pre_get_posts', array($this, 'woosg_search_sku'), 99);
					}

					if (get_option('_woosg_search_exact', 'no') === 'yes') {
						add_action('pre_get_posts', array($this, 'woosg_search_exact'), 99);
					}

					if (get_option('_woosg_search_sentence', 'no') === 'yes') {
						add_action('pre_get_posts', array($this, 'woosg_search_sentence'), 99);
					}
				}

				function woosg_admin_menu()
				{
					add_submenu_page('wpclever', esc_html__('WPC Grouped Product', 'wpc-grouped-product'), esc_html__('Grouped Product', 'wpc-grouped-product'), 'manage_options', 'wpclever-woosg', array(
						&$this,
						'woosg_admin_menu_content'
					));
				}

				function woosg_admin_menu_content()
				{
					add_thickbox();
					$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'settings';
?>
					<div class="wpclever_settings_page wrap">
						<h1 class="wpclever_settings_page_title">WPC Grouped
							Product <?php echo esc_attr(WOOSG_VERSION); ?></h1>
						<div class="wpclever_settings_page_desc about-text">
							<p>
								Thank you for using our plugin! If you are satisfied, please reward it a full five-star
								<span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span> rating.
								<br />
								<a href="<?php echo esc_url(WOOSG_REVIEWS); ?>" target="_blank"><?php esc_html_e('Reviews', 'wpc-grouped-product'); ?></a> | <a href="<?php echo esc_url(WOOSG_CHANGELOG); ?>" target="_blank"><?php esc_html_e('Changelog', 'wpc-grouped-product'); ?></a>
								| <a href="<?php echo esc_url(WOOSG_DISCUSSION); ?>" target="_blank"><?php esc_html_e('Discussion', 'wpc-grouped-product'); ?></a>
							</p>
						</div>
						<div class="wpclever_settings_page_nav">
							<h2 class="nav-tab-wrapper">
								<a href="<?php echo esc_url(admin_url('admin.php?page=wpclever-woosg&tab=how')); ?>" class="<?php echo $active_tab === 'how' ? 'nav-tab nav-tab-active' : 'nav-tab'; ?>">
									<?php esc_html_e('How to use?', 'wpc-grouped-product'); ?>
								</a>
								<a href="<?php echo esc_url(admin_url('admin.php?page=wpclever-woosg&tab=settings')); ?>" class="<?php echo $active_tab === 'settings' ? 'nav-tab nav-tab-active' : 'nav-tab'; ?>">
									<?php esc_html_e('Settings', 'wpc-grouped-product'); ?>
								</a>
								<a href="<?php echo esc_url(admin_url('admin.php?page=wpclever-woosg&tab=premium')); ?>" class="<?php echo $active_tab === 'premium' ? 'nav-tab nav-tab-active' : 'nav-tab'; ?>" style="color: #c9356e">
									<?php esc_html_e('Premium Version', 'wpc-grouped-product'); ?>
								</a>
								<a href="<?php echo esc_url(admin_url('admin.php?page=wpclever-kit')); ?>" class="nav-tab">
									<?php esc_html_e('Essential Kit', 'wpc-grouped-product'); ?>
								</a>
							</h2>
						</div>
						<div class="wpclever_settings_page_content">
							<?php if ($active_tab === 'how') { ?>
								<div class="wpclever_settings_page_content_text">
									<p>
										<?php esc_html_e('When creating the product, please choose product data is "Smart grouped" then you can see the search field to start search and add products.', 'wpc-grouped-product'); ?>
									</p>
									<p>
										<img src="<?php echo esc_url(WOOSG_URI . 'assets/images/how-01.jpg'); ?>" />
									</p>
								</div>
							<?php } elseif ($active_tab === 'settings') { ?>
								<form method="post" action="options.php">
									<?php wp_nonce_field('update-options') ?>
									<table class="form-table">
										<tr class="heading">
											<th colspan="2">
												<?php esc_html_e('General', 'wpc-grouped-product'); ?>
											</th>
										</tr>
										<tr>
											<th><?php esc_html_e('Price format', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_price_format">
													<option value="normal" <?php echo (get_option('_woosg_price_format', 'from') === 'normal' ? 'selected' : ''); ?>><?php esc_html_e('Normal price', 'wpc-grouped-product'); ?></option>
													<option value="from" <?php echo (get_option('_woosg_price_format', 'from') === 'from' ? 'selected' : ''); ?>><?php esc_html_e('From price', 'wpc-grouped-product'); ?></option>
													<option value="none" <?php echo (get_option('_woosg_price_format', 'from') === 'none' ? 'selected' : ''); ?>><?php esc_html_e('None', 'wpc-grouped-product'); ?></option>
												</select>
												<span class="description">
													<?php esc_html_e('Choose the price format for grouped product on the shop page.', 'wpc-grouped-product'); ?>
												</span>
											</td>
										</tr>
										<tr class="heading">
											<th colspan="2">
												<?php esc_html_e('Grouped products', 'wpc-grouped-product'); ?>
											</th>
										</tr>
										<tr>
											<th><?php esc_html_e('Position', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_position">
													<option value="above" <?php echo (get_option('_woosg_position', 'above') === 'above' ? 'selected' : ''); ?>>
														<?php esc_html_e('Above add to cart button', 'wpc-grouped-product'); ?>
													</option>
													<option value="below" <?php echo (get_option('_woosg_position', 'above') === 'below' ? 'selected' : ''); ?>>
														<?php esc_html_e('Under add to cart button', 'wpc-grouped-product'); ?>
													</option>
													<option value="tab" <?php echo (get_option('_woosg_position', 'above') === 'tab' ? 'selected' : ''); ?>>
														<?php esc_html_e('In a new tab', 'wpc-grouped-product'); ?>
													</option>
												</select> <span class="description">
													<?php esc_html_e('Choose the position to show the grouped product list.', 'wpc-grouped-product'); ?>
												</span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Variations selector', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_variations_selector">
													<option value="default" <?php echo (get_option('_woosg_variations_selector', 'default') === 'default' ? 'selected' : ''); ?>>
														<?php esc_html_e('Default', 'wpc-grouped-product'); ?>
													</option>
													<option value="wpc_radio" <?php echo (get_option('_woosg_variations_selector', 'default') === 'wpc_radio' ? 'selected' : ''); ?>>
														<?php esc_html_e('Use WPC Variations Radio Buttons', 'wpc-grouped-product'); ?>
													</option>
												</select> <span class="description">If you choose "Use WPC Variations Radio Buttons", please install <a href="<?php echo esc_url(admin_url('plugin-install.php?tab=plugin-information&plugin=wpc-variations-radio-buttons&TB_iframe=true&width=800&height=550')); ?>" class="thickbox" title="Install WPC Variations Radio Buttons">WPC Variations Radio Buttons</a> to make it work.</span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Show thumbnail', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_show_thumb">
													<option value="yes" <?php echo (get_option('_woosg_show_thumb', 'yes') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_show_thumb', 'yes') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Show short description', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_show_description">
													<option value="yes" <?php echo (get_option('_woosg_show_description', 'no') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_show_description', 'no') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Show price', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_show_price">
													<option value="yes" <?php echo (get_option('_woosg_show_price', 'yes') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_show_price', 'yes') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Show plus/minus button', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_show_plus_minus">
													<option value="yes" <?php echo (get_option('_woosg_show_plus_minus', 'no') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_show_plus_minus', 'no') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select> <span class="description"><?php esc_html_e('Show the plus/minus button to increase/decrease the quantity.', 'wpc-grouped-product'); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Link to individual product', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_link">
													<option value="yes" <?php echo (get_option('_woosg_link', 'yes') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes, open in the same tab', 'wpc-grouped-product'); ?>
													</option>
													<option value="yes_blank" <?php echo (get_option('_woosg_link', 'yes') === 'yes_blank' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes, open in the new tab', 'wpc-grouped-product'); ?>
													</option>
													<option value="yes_popup" <?php echo (get_option('_woosg_link', 'yes') === 'yes_popup' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes, open quick view popup', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_link', 'yes') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select> <span class="description">If you choose "Open quick view popup", please install <a href="<?php echo esc_url(admin_url('plugin-install.php?tab=plugin-information&plugin=woo-smart-quick-view&TB_iframe=true&width=800&height=550')); ?>" class="thickbox" title="Install WPC Smart Quick View">WPC Smart Quick View</a> to make it work.</span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Change image', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_change_image">
													<option value="yes" <?php echo (get_option('_woosg_change_image', 'yes') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_change_image', 'yes') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select>
												<span class="description">
													<?php esc_html_e('Change the main product image when choosing the variation of grouped product.', 'wpc-grouped-product'); ?>
												</span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Change price', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_change_price">
													<option value="yes" <?php echo (get_option('_woosg_change_price', 'yes') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="yes_custom" <?php echo (get_option('_woosg_change_price', 'yes') === 'yes_custom' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes, custom selector', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_change_price', 'yes') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select> <input type="text" name="_woosg_change_price_custom" value="<?php echo get_option('_woosg_change_price_custom', '.summary > .price'); ?>" placeholder=".summary > .price" />
												<span class="description">
													<?php esc_html_e('Change the main product price when choosing the variation of grouped product. It uses JavaScript to change product price so it is very dependent on theme’s HTML. If it cannot find and update the product price, please contact us and we can help you adjust the JS file.', 'wpc-grouped-product'); ?>
												</span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Total text', 'wpc-grouped-product'); ?></th>
											<td>
												<input type="text" name="_woosg_total_text" value="<?php echo esc_html(get_option('_woosg_total_text', esc_html__('Total:', 'wpc-grouped-product'))); ?>" />
											</td>
										</tr>
										<tr class="heading">
											<th>
												<?php esc_html_e('"Add to cart" button labels', 'wpc-grouped-product'); ?>
											</th>
											<td>
												<?php esc_html_e('Leave blank if you want to use the default text and can be translated.', 'wpc-grouped-product'); ?>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Archive/shop page', 'wpc-grouped-product'); ?></th>
											<td>
												<input type="text" name="_woosg_archive_button_select" value="<?php echo esc_attr(get_option('_woosg_archive_button_select')); ?>" placeholder="<?php esc_html_e('Select options', 'wpc-grouped-product'); ?>" />
												<span class="description">
													<?php esc_html_e('For purchasable grouped.', 'wpc-grouped-product'); ?>
												</span><br />
												<input type="text" name="_woosg_archive_button_read" value="<?php echo esc_attr(get_option('_woosg_archive_button_read')); ?>" placeholder="<?php esc_html_e('Read more', 'wpc-grouped-product'); ?>" />
												<span class="description">
													<?php esc_html_e('For un-purchasable grouped.', 'wpc-grouped-product'); ?>
												</span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Single product page', 'wpc-grouped-product'); ?></th>
											<td>
												<input type="text" name="_woosg_single_button_add" value="<?php echo esc_attr(get_option('_woosg_single_button_add')); ?>" placeholder="<?php esc_html_e('Add to cart', 'wpc-grouped-product'); ?>" />
											</td>
										</tr>
										<tr class="heading">
											<th colspan="2">
												<?php esc_html_e('Cart & Checkout', 'wpc-grouped-product'); ?>
											</th>
										</tr>
										<tr>
											<th><?php esc_html_e('Including main product', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_including_main">
													<option value="yes" <?php echo (get_option('_woosg_including_main', 'no') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_including_main', 'no') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select> <span class="description"><?php esc_html_e('Including the main product on the cart with the price is zero. Helpful when you need to add some extra options for the main product or make it work with WPC Bought Together.', 'wpc-grouped-product'); ?></span>
											</td>
										</tr>
										<tr class="heading">
											<th colspan="2">
												<?php esc_html_e('Search', 'wpc-grouped-product'); ?>
											</th>
										</tr>
										<tr>
											<th><?php esc_html_e('Search limit', 'wpc-grouped-product'); ?></th>
											<td>
												<input name="_woosg_search_limit" type="number" min="1" max="500" value="<?php echo esc_attr(get_option('_woosg_search_limit', '5')); ?>" />
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Search by SKU', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_search_sku">
													<option value="yes" <?php echo (get_option('_woosg_search_sku', 'no') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_search_sku', 'no') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Search by ID', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_search_id">
													<option value="yes" <?php echo (get_option('_woosg_search_id', 'no') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_search_id', 'no') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select> <span class="description"><?php esc_html_e('Search by ID when only entered the numeric.', 'wpc-grouped-product'); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Search exact', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_search_exact">
													<option value="yes" <?php echo (get_option('_woosg_search_exact', 'no') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_search_exact', 'no') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select> <span class="description"><?php esc_html_e('Match whole product title or content?', 'wpc-grouped-product'); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Search sentence', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_search_sentence">
													<option value="yes" <?php echo (get_option('_woosg_search_sentence', 'no') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_search_sentence', 'no') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select> <span class="description"><?php esc_html_e('Do a phrase search?', 'wpc-grouped-product'); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e('Accept same products', 'wpc-grouped-product'); ?></th>
											<td>
												<select name="_woosg_search_same">
													<option value="yes" <?php echo (get_option('_woosg_search_same', 'no') === 'yes' ? 'selected' : ''); ?>>
														<?php esc_html_e('Yes', 'wpc-grouped-product'); ?>
													</option>
													<option value="no" <?php echo (get_option('_woosg_search_same', 'no') === 'no' ? 'selected' : ''); ?>>
														<?php esc_html_e('No', 'wpc-grouped-product'); ?>
													</option>
												</select> <span class="description"><?php esc_html_e('If yes, a product can be added many times.', 'wpc-grouped-product'); ?></span>
											</td>
										</tr>
										<tr class="submit">
											<th colspan="2">
												<input type="submit" name="submit" class="button button-primary" value="<?php esc_html_e('Update Options', 'wpc-grouped-product'); ?>" />
												<input type="hidden" name="action" value="update" />
												<input type="hidden" name="page_options" value="_woosg_price_format,_woosg_position,_woosg_variations_selector,_woosg_show_thumb,_woosg_show_description,_woosg_show_price,_woosg_show_plus_minus,_woosg_link,_woosg_change_image,_woosg_change_price,_woosg_change_price_custom,_woosg_total_text,_woosg_archive_button_select,_woosg_archive_button_read,_woosg_single_button_add,_woosg_including_main,_woosg_search_limit,_woosg_search_sku,_woosg_search_id,_woosg_search_exact,_woosg_search_sentence,_woosg_search_same" />
											</th>
										</tr>
									</table>
								</form>
							<?php } elseif ($active_tab === 'premium') { ?>
								<div class="wpclever_settings_page_content_text">
									<p>
										Get the Premium Version just $29! <a href="https://wpclever.net/downloads/wpc-grouped-product-for-woocommerce?utm_source=pro&utm_medium=woosg&utm_campaign=wporg" target="_blank">https://wpclever.net/downloads/wpc-grouped-product-for-woocommerce</a>
									</p>
									<p><strong>Extra features for Premium Version:</strong></p>
									<ul style="margin-bottom: 0">
										<li>- Add more than 3 products to the grouped.</li>
										<li>- Get the lifetime update & premium support.</li>
									</ul>
								</div>
							<?php } ?>
						</div>
					</div>
				<?php
				}

				function woosg_wp_enqueue_scripts()
				{
					wp_enqueue_style('woosg-frontend', WOOSG_URI . 'assets/css/frontend.css');
					wp_enqueue_script('woosg-frontend', WOOSG_URI . 'assets/js/frontend.js', array('jquery'), WOOSG_VERSION, true);
					wp_localize_script(
						'woosg-frontend',
						'woosg_vars',
						array(
							'alert_selection'          => esc_html__('Please select some product options before adding this grouped to the cart.', 'wpc-grouped-product'),
							'alert_empty'              => esc_html__('Please choose at least one product before adding this grouped to the cart.', 'wpc-grouped-product'),
							'select_options'           => esc_html__('Select options', 'wpc-grouped-product'),
							'add_to_cart'              => esc_html__('Add to cart', 'wpc-grouped-product'),
							'total_text'               => get_option('_woosg_total_text', ''),
							'change_image'             => get_option('_woosg_change_image', 'yes'),
							'change_price'             => get_option('_woosg_change_price', 'yes'),
							'price_selector'           => get_option('_woosg_change_price_custom', ''),
							'price_format'             => get_woocommerce_price_format(),
							'price_decimals'           => wc_get_price_decimals(),
							'price_thousand_separator' => wc_get_price_thousand_separator(),
							'price_decimal_separator'  => wc_get_price_decimal_separator(),
							'currency_symbol'          => get_woocommerce_currency_symbol(),
							'ver'                      => WOOSG_VERSION
						)
					);
				}

				function woosg_admin_enqueue_scripts()
				{
					wp_enqueue_style('hint', WOOSG_URI . 'assets/css/hint.css');
					wp_enqueue_style('woosg-backend', WOOSG_URI . 'assets/css/backend.css');
					wp_enqueue_script('dragarrange', WOOSG_URI . 'assets/js/drag-arrange.js', array('jquery'), WOOSG_VERSION, true);
					wp_enqueue_script('accounting', WOOSG_URI . 'assets/js/accounting.js', array('jquery'), WOOSG_VERSION, true);
					wp_enqueue_script('woosg-backend', WOOSG_URI . 'assets/js/backend.js', array('jquery'), WOOSG_VERSION, true);
					wp_localize_script(
						'woosg-backend',
						'woosg_vars',
						array(
							'price_decimals'           => wc_get_price_decimals(),
							'price_thousand_separator' => wc_get_price_thousand_separator(),
							'price_decimal_separator'  => wc_get_price_decimal_separator()
						)
					);
				}

				function woosg_action_links($links, $file)
				{
					static $plugin;

					if (!isset($plugin)) {
						$plugin = plugin_basename(__FILE__);
					}

					if ($plugin === $file) {
						$settings         = '<a href="' . admin_url('admin.php?page=wpclever-woosg&tab=settings') . '">' . esc_html__('Settings', 'wpc-grouped-product') . '</a>';
						$links['premium'] = '<a href="' . admin_url('admin.php?page=wpclever-woosg&tab=premium') . '">' . esc_html__('Premium Version', 'wpc-grouped-product') . '</a>';
						array_unshift($links, $settings);
					}

					return (array) $links;
				}

				function woosg_row_meta($links, $file)
				{
					static $plugin;

					if (!isset($plugin)) {
						$plugin = plugin_basename(__FILE__);
					}

					if ($plugin === $file) {
						$row_meta = array(
							'support' => '<a href="' . esc_url(WOOSG_SUPPORT) . '" target="_blank">' . esc_html__('Support', 'wpc-grouped-product') . '</a>',
						);

						return array_merge($links, $row_meta);
					}

					return (array) $links;
				}

				function woosg_add_cart_item_data($cart_item_data, $product_id)
				{
					$woosg_product = wc_get_product($product_id);

					if ($woosg_product && $woosg_product->is_type('woosg') && ($ids = get_post_meta($product_id, 'woosg_ids', true))) {
						// make sure that is grouped
						if (isset($_POST['woosg_ids'])) {
							$ids = $_POST['woosg_ids'];
							unset($_POST['woosg_ids']);
						}

						$ids = $this->woosg_clean_ids($ids);

						if (!empty($ids)) {
							$cart_item_data['woosg_ids'] = $ids;
						}
					}

					return $cart_item_data;
				}

				function woosg_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
				{
					if (!empty($cart_item_data['woosg_ids'])) {
						if ($items = $this->woosg_get_items($cart_item_data['woosg_ids'])) {
							foreach ($items as $item) {
								$item_id      = $item['id'];
								$item_qty     = $item['qty'] ?: 1;
								$item_product = wc_get_product($item_id);

								if (!$item_product || ($item_qty <= 0)) {
									continue;
								}

								$item_variation_id = 0;
								$item_variation    = array();

								if ($item_product instanceof WC_Product_Variation) {
									// ensure we don't add a variation to the cart directly by variation ID
									$item_variation_id = $item_id;
									$item_id           = $item_product->get_parent_id();
									$item_variation    = $item_product->get_variation_attributes();
								}

								// add to cart
								$product_qty = $item_qty * $quantity;
								$item_key    = WC()->cart->add_to_cart($item_id, $product_qty, $item_variation_id, $item_variation);

								if ($item_key) {
									WC()->cart->cart_contents[$cart_item_key]['woosg_keys'][] = $item_key;
								}
							} // end foreach
						}

						// remove grouped
						if (get_option('_woosg_including_main', 'no') !== 'yes') {
							WC()->cart->remove_cart_item($cart_item_key);
						}
					}
				}

				function woosg_get_cart_contents($cart_contents)
				{
					foreach ($cart_contents as $cart_item_key => $cart_item) {
						if (!empty($cart_item['woosg_ids'])) {
							$cart_item['data']->set_price(0);
						}

						if (!empty($cart_item['woosg_keys'])) {
							$has_key = false;

							foreach ($cart_item['woosg_keys'] as $key) {
								if (isset($cart_contents[$key])) {
									$has_key = true;
								}
							}

							if (!$has_key) {
								WC()->cart->remove_cart_item($cart_item_key);
								unset($cart_contents[$cart_item_key]);
							}
						}
					}

					return $cart_contents;
				}

				function woosg_get_cart_item_from_session($cart_item, $item_session_values)
				{
					if (isset($item_session_values['woosg_ids']) && !empty($item_session_values['woosg_ids'])) {
						$cart_item['woosg_ids'] = $item_session_values['woosg_ids'];
					}

					return $cart_item;
				}

				function woosg_get_search_results()
				{
					$keyword     = isset($_POST['keyword']) ? sanitize_text_field(wp_unslash($_POST['keyword'])) : '';
					$ids         = isset($_POST['ids']) ? $this->woosg_clean_ids($_POST['ids']) : '';
					$exclude_ids = array();
					$items       = explode(',', $ids);

					if (is_array($items) && count($items) > 2) {
						echo '<ul><span>Please use the Premium Version to add more than 3 products to the grouped & get the premium support. Click <a href="https://wpclever.net/downloads/wpc-grouped-product-for-woocommerce?utm_source=pro&utm_medium=woosg&utm_campaign=wporg" target="_blank">here</a> to buy, just $29!</span></ul>';
						die();
					}

					if ((get_option('_woosg_search_id', 'no') === 'yes') && is_numeric($keyword)) {
						// search by id
						$query_args = array(
							'p'         => absint($keyword),
							'post_type' => 'product'
						);
					} else {
						$query_args = array(
							'is_woosg'       => true,
							'post_type'      => 'product',
							'post_status'    => array('publish', 'private'),
							's'              => $keyword,
							'posts_per_page' => get_option('_woosg_search_limit', '5'),
							'tax_query'      => array(
								array(
									'taxonomy' => 'product_type',
									'field'    => 'slug',
									'terms'    => array('woosg'),
									'operator' => 'NOT IN',
								)
							)
						);

						if (get_option('_woosg_search_same', 'no') !== 'yes') {
							if (is_array($items) && count($items) > 0) {
								foreach ($items as $item) {
									$item_data     = explode('/', $item);
									$exclude_ids[] = absint(isset($item_data[0]) ? $item_data[0] : 0);
								}
							}

							$query_args['post__not_in'] = $exclude_ids;
						}
					}

					$query = new WP_Query($query_args);

					if ($query->have_posts()) {
						echo '<ul>';

						while ($query->have_posts()) {
							$query->the_post();
							$woosg_product = wc_get_product(get_the_ID());

							if (!$woosg_product || $woosg_product->is_type('woosg')) {
								continue;
							}

							$this->woosg_product_data_li($woosg_product, 0, true);

							if ($woosg_product->is_type('variable')) {
								// show all children
								$children = $woosg_product->get_children();

								if (is_array($children) && count($children) > 0) {
									foreach ($children as $child) {
										$child_product = wc_get_product($child);
										$this->woosg_product_data_li($child_product, 0, true);
									}
								}
							}
						}

						echo '</ul>';
						wp_reset_postdata();
					} else {
						echo '<ul><span>' . esc_html__('No results found for:', 'wpc-grouped-product') . ' ' . esc_html($keyword) . '</span></ul>';
					}

					die();
				}

				function woosg_search_sku($query)
				{
					if ($query->is_search && isset($query->query['is_woosg'])) {
						global $wpdb;
						$sku = $query->query['s'];
						$ids = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value = %s;", $sku));

						if (!$ids) {
							return;
						}

						unset($query->query['s'], $query->query_vars['s']);
						$query->query['post__in'] = array();

						foreach ($ids as $id) {
							$post = get_post($id);

							if ($post->post_type === 'product_variation') {
								$query->query['post__in'][]      = $post->post_parent;
								$query->query_vars['post__in'][] = $post->post_parent;
							} else {
								$query->query_vars['post__in'][] = $post->ID;
							}
						}
					}
				}

				function woosg_search_exact($query)
				{
					if ($query->is_search && isset($query->query['is_woosg'])) {
						$query->set('exact', true);
					}
				}

				function woosg_search_sentence($query)
				{
					if ($query->is_search && isset($query->query['is_woosg'])) {
						$query->set('sentence', true);
					}
				}

				function woosg_product_type_selector($types)
				{
					$types['woosg'] = esc_html__('Smart grouped', 'wpc-grouped-product');

					return $types;
				}

				function woosg_product_data_tabs($tabs)
				{
					$tabs['woosg'] = array(
						'label'  => esc_html__('Grouped Products', 'wpc-grouped-product'),
						'target' => 'woosg_settings',
						'class'  => array('show_if_woosg'),
					);

					return $tabs;
				}

				function woosg_product_tabs($tabs)
				{
					global $product;

					if ((get_option('_woosg_position', 'above') === 'tab') && $product->is_type('woosg')) {
						$tabs['woosg'] = array(
							'title'    => esc_html__('Grouped products', 'wpc-grouped-product'),
							'priority' => 50,
							'callback' => array($this, 'woosg_product_tab_grouped')
						);
					}

					return $tabs;
				}

				function woosg_product_tab_grouped()
				{
					$this->woosg_show_items();
				}

				function woosg_product_filters($filters)
				{
					$filters = str_replace('Woosg', esc_html__('Smart grouped', 'wpc-grouped-product'), $filters);

					return $filters;
				}

				function woosg_product_data_panels()
				{
					global $post;
					$post_id = $post->ID;
				?>
					<div id='woosg_settings' class='panel woocommerce_options_panel woosg_table'>
						<table>
							<tr>
								<th><?php esc_html_e('Search', 'wpc-grouped-product'); ?> (<a href="<?php echo esc_url(admin_url('admin.php?page=wpclever-woosg&tab=settings#search')); ?>" target="_blank"><?php esc_html_e('settings', 'wpc-grouped-product'); ?></a>)
								</th>
								<td>
									<div class="w100">
										<span class="loading" id="woosg_loading" style="display: none;"><?php esc_html_e('searching...', 'wpc-grouped-product'); ?></span>
										<input type="search" id="woosg_keyword" placeholder="<?php esc_html_e('Type any keyword to search', 'wpc-grouped-product'); ?>" />
										<div id="woosg_results" class="woosg_results" style="display: none;"></div>
									</div>
								</td>
							</tr>
							<tr class="woosg_tr_space">
								<th><?php esc_html_e('Selected', 'wpc-grouped-product'); ?></th>
								<td>
									<div class="w100">
										<input type="hidden" id="woosg_ids" class="woosg_ids" name="woosg_ids" value="<?php echo esc_attr(get_post_meta($post_id, 'woosg_ids', true)); ?>" readonly />
										<div id="woosg_selected" class="woosg_selected">
											<ul>
												<?php
												if ($ids = get_post_meta($post_id, 'woosg_ids', true)) {
													if ($items = $this->woosg_get_items($ids)) {
														foreach ($items as $item) {
															$item_product = wc_get_product($item['id']);

															if (!$item_product || $item_product->is_type('woosg')) {
																continue;
															}

															$this->woosg_product_data_li($item_product, $item['qty']);
														}
													}
												}
												?>
											</ul>
										</div>
									</div>
								</td>
							</tr>
							<tr class="woosg_tr_space">
								<th><?php esc_html_e('Note', 'wpc-grouped-product'); ?></th>
								<td>
									<span style="text-decoration: underline; color: #c9356e"><?php esc_html_e('Always put a price in the General tab to display the Add to Cart button.', 'wpc-grouped-product'); ?></span>
								</td>
							</tr>
							<tr class="woosg_tr_space">
								<th><?php esc_html_e('Custom display price', 'wpc-grouped-product'); ?></th>
								<td>
									<input type="text" name="woosg_custom_price" value="<?php echo stripslashes(get_post_meta($post_id, 'woosg_custom_price', true)); ?>" />
									E.g: <code>From $10 to $100</code>
								</td>
							</tr>
							<tr class="woosg_tr_space">
								<th><?php esc_html_e('Above text', 'wpc-grouped-product'); ?></th>
								<td>
									<div class="w100">
										<textarea name="woosg_before_text"><?php echo stripslashes(get_post_meta($post_id, 'woosg_before_text', true)); ?></textarea>
									</div>
								</td>
							</tr>
							<tr class="woosg_tr_space">
								<th><?php esc_html_e('Under text', 'wpc-grouped-product'); ?></th>
								<td>
									<div class="w100">
										<textarea name="woosg_after_text"><?php echo stripslashes(get_post_meta($post_id, 'woosg_after_text', true)); ?></textarea>
									</div>
								</td>
							</tr>
						</table>
					</div>
					<?php
				}

				function woosg_product_data_li($product, $qty = 0, $search = false)
				{
					$product_id = $product->get_id();

					if (class_exists('WPCleverWoopq') && (get_option('_woopq_decimal', 'no') === 'yes')) {
						$step = '0.000001';
					} else {
						$step = 1;
					}

					$qty_input = '<input type="number" value="' . esc_attr($qty) . '" min="0" step="' . esc_attr($step) . '"/>';

					if ($search) {
						$remove_btn = '<span class="remove hint--left" aria-label="' . esc_html__('Add', 'wpc-grouped-product') . '">+</span>';
					} else {
						$remove_btn = '<span class="remove hint--left" aria-label="' . esc_html__('Remove', 'wpc-grouped-product') . '">×</span>';
					}

					echo '<li ' . (!$product->is_in_stock() ? 'class="out-of-stock"' : '') . ' data-id="' . esc_attr($product_id) . '"><span class="move"></span><span class="qty hint--right" aria-label="' . esc_html__('Default quantity', 'wpc-grouped-product') . '">' . $qty_input . '</span> <span class="data"><span class="name">' . strip_tags($product->get_name()) . '</span> <span class="info">' . $product->get_price_html() . '</span> ' . ($product->is_sold_individually() ? '<span class="info">sold individually</span> ' : '') . '</span> <span class="type"><a href="' . get_edit_post_link($product_id) . '" target="_blank">' . esc_attr($product->get_type()) . ' #' . esc_attr($product_id) . '</a></span> ' . $remove_btn . '</li>';
				}

				function woosg_save_option_field($post_id)
				{
					if (isset($_POST['woosg_ids'])) {
						update_post_meta($post_id, 'woosg_ids', $this->woosg_clean_ids($_POST['woosg_ids']));
					}

					if (isset($_POST['woosg_custom_price']) && ($_POST['woosg_custom_price'] !== '')) {
						update_post_meta($post_id, 'woosg_custom_price', addslashes($_POST['woosg_custom_price']));
					} else {
						delete_post_meta($post_id, 'woosg_custom_price');
					}

					if (isset($_POST['woosg_before_text']) && ($_POST['woosg_before_text'] !== '')) {
						update_post_meta($post_id, 'woosg_before_text', addslashes($_POST['woosg_before_text']));
					} else {
						delete_post_meta($post_id, 'woosg_before_text');
					}

					if (isset($_POST['woosg_after_text']) && ($_POST['woosg_after_text'] !== '')) {
						update_post_meta($post_id, 'woosg_after_text', addslashes($_POST['woosg_after_text']));
					} else {
						delete_post_meta($post_id, 'woosg_after_text');
					}
				}

				function woosg_add_to_cart_form()
				{
					global $product;

					echo '<div class="woosg-form">';

					$type = $product->ID;
					// echo $type;
					$final = WC_Product_Factory::get_product_type($type);
					echo $final;


					// TEST DE AGREGAR COSAS SI EL PRODUCTO ES AGRUPADO


					if ($product->has_variables()) {
						wp_enqueue_script('wc-add-to-cart-variation');
					}

					if ((get_option('_woosg_position', 'above') === 'above') && apply_filters('woosg_show_items', true, $product->get_id())) {
						$this->woosg_show_items();
					}

					wc_get_template('single-product/add-to-cart/simple.php');

					if ((get_option('_woosg_position', 'above') === 'below') && apply_filters('woosg_show_items', true, $product->get_id())) {
						$this->woosg_show_items();
					}

					echo '</div><!-- /woosg-form -->';
				}

				function woosg_add_to_cart_button()
				{
					global $product;

					if ($product->is_type('woosg')) {
						echo '<input name="woosg_ids" class="woosg_ids woosg-ids" type="hidden" value="' . esc_attr(get_post_meta($product->get_id(), 'woosg_ids', true)) . '"/>';
					}
				}

				function woosg_show_items($product = null)
				{
					if (!$product) {
						global $product;
					}

					$product_id = $product->get_id();
					$order      = 1;

					if ($items = $product->get_items()) {
						echo '<div class="woosg_wrap woosg-wrap">';

						do_action('woosg_before_wrap', $product);

						if ($before_text = apply_filters('woosg_before_text', get_post_meta($product_id, 'woosg_before_text', true), $product_id)) {
							echo '<div class="woosg_before_text woosg-before-text woosg-text">' . do_shortcode(stripslashes($before_text)) . '</div>';
						}

						do_action('woosg_before_table', $product);
					?>
						<div class="woosg_products woosg-table woosg-products" data-variables="<?php echo esc_attr($product->has_variables() ? 'yes' : 'no'); ?>">
							<?php
							do_action('woosg_before_items', $product);

							foreach ($items as $item) {
								$woosg_product = wc_get_product($item['id']);

								if (!$woosg_product || ($order > 3)) {
									continue;
								}

								$item_price = wc_get_price_to_display($woosg_product);
								$item_class = 'woosg-product';
								$item_qty   = $item['qty'];
								$item_id    = $woosg_product->is_type('variable') ? 0 : $item['id'];

								if ($woosg_product->is_purchasable() && $woosg_product->is_in_stock()) {
									$min = apply_filters('woocommerce_quantity_input_min', 0, $woosg_product);
									$max = apply_filters('woocommerce_quantity_input_max', $woosg_product->get_max_purchase_quantity(), $woosg_product);

									if ($max < 0) {
										$max = 1000;
									}

									if ($item_qty < $min) {
										$item_qty = $min;
									}

									if (($max > 0) && ($item_qty > $max)) {
										$item_qty = $max;
									}
								} else {
									$item_class .= ' woosg-product-unpurchasable';
									$item_price = 0;
									$item_id    = -1;
								}
							?>
								<div class="<?php echo esc_attr($item_class); ?>" data-id="<?php echo esc_attr($item_id); ?>" data-price="<?php echo esc_attr($item_price); ?>" data-qty="<?php echo esc_attr($item_qty); ?>" data-order="<?php echo esc_attr($order); ?>">

									<?php
									do_action('woosg_before_item', $woosg_product, $product, $order);

									if (get_option('_woosg_show_thumb', 'yes') !== 'no') {
									?>
										<div class="woosg-thumb">
											<?php
											do_action('woosg_before_item_thumb', $woosg_product, $product);

											if (get_option('_woosg_link', 'yes') !== 'no') {
												echo '<a class="woosg-product-link' . (get_option('_woosg_link', 'yes') === 'yes_popup' ? ' woosq-btn' : '') . '" data-id="' . $item['id'] . '" href="' . get_permalink($item['id']) . '" ' . (get_option('_woosg_link', 'yes') === 'yes_blank' ? 'target="_blank"' : '') . '>';
											} ?>
											<div class="woosg-thumb-ori">
												<?php echo wp_kses(apply_filters('woosg_item_thumbnail', $woosg_product->get_image(), $woosg_product), array(
													'img' => array(
														'class' => array(),
														'src'   => array(),
														'id'    => array()
													)
												)); ?>
											</div>
											<div class="woosg-thumb-new"></div>
											<?php if (get_option('_woosg_link', 'yes') !== 'no') {
												echo '</a>';
											}

											do_action('woosg_after_item_thumb', $woosg_product, $product); ?>
										</div><!-- /woosg-thumb -->
									<?php
									} ?>

									<div class="woosg-title">
										<?php
										do_action('woosg_before_item_name', $woosg_product, $product);

										echo '<div class="woosg-title-inner">';
										$item_name = '';

										if (get_option('_woosg_link', 'yes') !== 'no') {
											$item_name .= '<a class="woosg-product-link' . (get_option('_woosg_link', 'yes') === 'yes_popup' ? ' woosq-btn' : '') . '" data-id="' . $item['id'] . '" href="' . get_permalink($item['id']) . '" ' . (get_option('_woosg_link', 'yes') === 'yes_blank' ? 'target="_blank"' : '') . '>';
										}

										if ($woosg_product->is_in_stock()) {
											$item_name .= $woosg_product->get_name();
										} else {
											$item_name .= '<s>' . $woosg_product->get_name() . '</s>';
										}

										if (get_option('_woosg_link', 'yes') !== 'no') {
											$item_name .= '</a>';
										}

										echo wp_kses(apply_filters('woosg_item_name', $item_name, $woosg_product), array(
											'a' => array(
												'class'   => array(),
												'data-id' => array(),
												'href'    => array(),
												'target'  => array()
											),
											's' => array()
										));
										echo '</div>';

										do_action('woosg_after_item_name', $woosg_product, $product);

										if (get_option('_woosg_show_price', 'yes') !== 'no') { ?>
											<div class="woosg-price">
												<div class="woosg-price-ori">
													<?php echo $woosg_product->get_price_html(); ?>
												</div>
												<div class="woosg-price-new"></div>
												<?php do_action('woosg_after_item_price', $woosg_product, $product); ?>
											</div>
											<?php }

										if (get_option('_woosg_show_description', 'no') === 'yes') {
											echo '<div class="woosg-description">' . apply_filters('woosg_item_description', $woosg_product->get_short_description(), $woosg_product) . '</div>';
										}

										if ($woosg_product->is_type('variable')) {
											if ((get_option('_woosg_variations_selector', 'default') === 'wpc_radio') && class_exists('WPClever_Woovr')) {
												WPClever_Woovr::woovr_variations_form($woosg_product);
											} else {
												$attributes           = $woosg_product->get_variation_attributes();
												$available_variations = $woosg_product->get_available_variations();
												$variations_json      = wp_json_encode($available_variations);
												$variations_attr      = function_exists('wc_esc_json') ? wc_esc_json($variations_json) : _wp_specialchars($variations_json, ENT_QUOTES, 'UTF-8', true);

												if (is_array($attributes) && (count($attributes) > 0)) {
													echo '<form class="variations_form" data-product_id="' . esc_attr($woosg_product->get_id()) . '" data-product_variations="' . esc_attr($variations_attr) . '">';
													echo '<div class="variations">';

													foreach ($attributes as $attribute_name => $options) { ?>
														<div class="variation">
															<div class="label">
																<?php echo esc_html(wc_attribute_label($attribute_name)); ?>
															</div>
															<div class="select">
																<?php
																$attr     = 'attribute_' . sanitize_title($attribute_name);
																$selected = isset($_REQUEST[$attr]) ? wc_clean(stripslashes(urldecode($_REQUEST[$attr]))) : $woosg_product->get_variation_default_attribute($attribute_name);

																wc_dropdown_variation_attribute_options(array(
																	'options'          => $options,
																	'attribute'        => $attribute_name,
																	'product'          => $woosg_product,
																	'selected'         => $selected,
																	'show_option_none' => esc_html__('Choose', 'wpc-grouped-product') . ' ' . wc_attribute_label($attribute_name)
																));
																?>
															</div>
														</div>
										<?php }

													echo '<div class="reset"><a class="reset_variations" href="#">' . esc_html__('Clear', 'wpc-grouped-product') . '</a></div>';
													echo '</div>';
													echo '</form>';

													if (get_option('_woosg_show_description', 'no') === 'yes') {
														echo '<div class="woosg-variation-description"></div>';
													}
												}
											}

											echo '<div class="woosg-availability"></div>';

											do_action('woosg_after_item_variations', $woosg_product, $product);
										} else {
											echo '<div class="woosg-availability">' . wc_get_stock_html($woosg_product) . '</div>';
										}
										?>
									</div><!-- /woosg-title -->

									<?php
									if ($woosg_product->is_purchasable() && $woosg_product->is_in_stock()) {
										echo '<div class="' . (get_option('_woosg_show_plus_minus', 'no') === 'yes' ? 'woosg-qty woosg-qty-plus-minus' : 'woosg-qty') . '" data-min="' . esc_attr($min) . '" data-max="' . esc_attr($max) . '">';

										$qty_args = array(
											'input_value' => $item_qty,
											'min_value'   => $min,
											'max_value'   => $max,
											'input_name'  => 'woosg_qty_' . $order
										);

										if ($woosg_product->is_sold_individually()) {
											$qty_args['max_value'] = 1;
										}

										if (get_option('_woosg_show_plus_minus', 'no') === 'yes') {
											echo '<span class="woosg-qty-minus">-</span>';
										}

										woocommerce_quantity_input($qty_args, $woosg_product);

										if (get_option('_woosg_show_plus_minus', 'no') === 'yes') {
											echo '<span class="woosg-qty-plus">+</span>';
										}

										do_action('woosg_after_item_qty', $woosg_product, $product);

										echo '</div><!-- /woosg-qty -->';
									} else {
										echo '<div class="' . (get_option('_woosg_show_plus_minus', 'no') === 'yes' ? 'woosg-qty woosg-qty-plus-minus' : 'woosg-qty') . '" data-min="' . esc_attr($item_qty) . '" data-max="' . esc_attr($item_qty) . '">';

										if (get_option('_woosg_show_plus_minus', 'no') === 'yes') {
											echo '<span class="woosg-qty-minus">-</span>';
										}

										echo '<div class="quantity"><input type="number" class="input-text qty text" value="' . esc_attr($item_qty) . '" readonly/></div>';

										if (get_option('_woosg_show_plus_minus', 'no') === 'yes') {
											echo '<span class="woosg-qty-plus">+</span>';
										}

										do_action('woosg_after_item_qty', $woosg_product, $product);

										echo '</div><!-- /woosg-qty -->';
									}

									do_action('woosg_after_item', $woosg_product, $product, $order);
									?>
								</div><!-- /woosg-product -->
							<?php
								$order++;
							}

							do_action('woosg_after_items', $product);
							?>
						</div>
		<?php
						echo '<div class="woosg_total woosg-total woosg-text"></div>';

						get_template_part('components/single/product-btn');

						do_action('woosg_after_table', $product);

						if ($after_text = apply_filters('woosg_after_text', get_post_meta($product_id, 'woosg_after_text', true), $product_id)) {
							echo '<div class="woosg_after_text woosg-after-text woosg-text">' . do_shortcode(stripslashes($after_text)) . '</div>';
						}

						do_action('woosg_after_wrap', $product);

						echo '</div><!-- /woosg-wrap -->';
					}
				}

				function woosg_get_price_html($price, $product)
				{
					if ($product->is_type('woosg')) {
						$custom_price = get_post_meta($product->get_id(), 'woosg_custom_price', true);

						if (!empty($custom_price)) {
							return $custom_price;
						}

						switch (get_option('_woosg_price_format', 'from')) {
							case 'none':
								return '';
								break;
							case 'from':
								return esc_html__('From', 'wpc-grouped-product') . ' ' . wc_price($product->get_price());
								break;
						}
					}

					return $price;
				}

				function woosg_display_post_states($states, $post)
				{
					if ('product' == get_post_type($post->ID)) {
						if (($product = wc_get_product($post->ID)) && $product->is_type('woosg')) {
							$count = 0;

							if ($ids = get_post_meta($post->ID, 'woosg_ids', true)) {
								if ($items = $this->woosg_get_items($ids)) {
									$count = count($items);
								}
							}

							$states[] = apply_filters('woosg_post_states', '<span class="woosg-state">' . sprintf(esc_html__('Grouped (%s)', 'wpc-grouped-product'), $count) . '</span>', $count, $product);
						}
					}

					return $states;
				}

				function woosg_get_items($ids)
				{
					$data = array();
					$ids  = $this->woosg_clean_ids($ids);

					if (!empty($ids)) {
						$items = explode(',', $ids);

						if (is_array($items) && count($items) > 0) {
							foreach ($items as $item) {
								$item_data = explode('/', $item);
								$data[]    = array(
									'id'  => absint(isset($item_data[0]) ? $item_data[0] : 0),
									'qty' => (float) (isset($item_data[1]) ? $item_data[1] : 0)
								);
							}
						}
					}

					if (count($data) > 0) {
						return $data;
					}

					return false;
				}

				function woosg_clean_ids($ids)
				{
					$ids = preg_replace('/[^,.\/0-9]/', '', $ids);

					return $ids;
				}
			}

			new WPCleverWoosg();
		}
	}
} else {
	add_action('admin_notices', 'woosg_notice_premium');
}

if (!function_exists('woosg_notice_wc')) {
	function woosg_notice_wc()
	{
		?>
		<div class="error">
			<p><strong>WPC Grouped Product</strong> requires WooCommerce version 3.0.0 or greater.</p>
		</div>
	<?php
	}
}

if (!function_exists('woosg_notice_premium')) {
	function woosg_notice_premium()
	{
	?>
		<div class="error">
			<p>Seems you're using both free and premium version of <strong>WPC Grouped Product</strong>. Please
				deactivate the free version when using the premium version.</p>
		</div>
<?php
	}
}
