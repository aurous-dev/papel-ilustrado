<?php

/**
 * Class DHWC_Ajax_Widget_ACF_Filter
 */
class DHWC_Ajax_Widget_ACF_Filter extends DHWC_Ajax_Widget
{

	public function __construct()
	{
		$this->widget_cssclass    = 'woocommerce widget_layered_nav dhwc_ajax_acf_filter';
		$this->widget_description = __('Display product filter with Advanced Custom Fields in your store', 'dhwc-ajax');
		$this->widget_id          = 'dhwc_ajax_acf_filter';
		$this->widget_name        = __('DHWC Ajax ACF Filter', 'dhwc-ajax');
		parent::__construct();
	}

	public function update($new_instance, $old_instance)
	{
		$this->init_settings();
		return parent::update($new_instance, $old_instance);
	}

	public function form($instance)
	{
		$this->init_settings();
		parent::form($instance);
	}

	/**
	 * Init settings after post types are registered.
	 */
	public function init_settings()
	{
		$this->settings = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __('Filter Custom Field', 'dhwc-ajax'),
				'label' => __('Title', 'dhwc-ajax')
			),
			'field_name' => array(
				'type'  => 'select',
				'std'	=> '',
				'label' => __('Filter by Field', 'dhwc-ajax'),
				'options' => dhwc_ajax_get_acf_fields_options()
			),
			'show_count'     => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __('Show product counts', 'dhwc-ajax'),
			)
		);
	}

	public function widget($args, $instance)
	{
		global $wp;

		if (!is_shop() && !is_product_taxonomy()) {
			return;
		}

		$field_name = !empty($instance['field_name']) ? $instance['field_name'] : '';
		$show_count = isset($instance['show_count']) ? (int) $instance['show_count'] : $this->settings['show_count']['std'];

		if (empty($field_name)) {
			return;
		}

		$field = acf_get_field($field_name);

		if (empty($field)) {
			return;
		}

		$filter_name = dhwc_ajax_get_acf_field_filter_query($field_name);

		$filtered 	= !empty($_GET[$filter_name]) ? wc_clean(wp_unslash($_GET[$filter_name])) : '';
		$link = $this->get_current_page_url();
		$found = false;

		$field_type = $field['type'];
		ob_start();

		$this->widget_start($args, $instance);
		echo '<ul>';

		foreach ((array)$field['choices'] as $value => $label) {
			$count = absint($this->get_filtered_product($field_name, $value, $field_type));

			if (!$count) {
				continue;
			}

			$found = true;
			$count_html = '';

			if ($show_count) {
				$count_html = apply_filters('dhwc_ajax_acf_filter_count', '<span class="count">' . $count . '</span>', $count, $field_name);
			}

			if ((string)$filtered === (string) $value) {
				$link = remove_query_arg($filter_name, $link);
				echo '<li class="tr-' . $value . ' chosen"><a href="' . $link . '">' . $label . $count_html . '</a></li>';
			} else {
				$link = add_query_arg(array($filter_name => $value, 'acf' => '1'), $link);
				echo '<li class="tr-' . $value . '"><a href="' . $link . '">' . $label . $count_html . '</a></li>';
			}
		}
		echo '</ul>';
		$this->widget_end($args);

		if (!$found) {
			ob_end_clean();
		} else {
			echo ob_get_clean(); // @codingStandardsIgnoreLine
		}
	}


	protected function get_filtered_product($field_name, $value, $field_type)
	{
		global $wpdb;

		$tax_query  = WC_Query::get_main_tax_query();
		$meta_query = WC_Query::get_main_meta_query();

		$meta_key = dhwc_ajax_get_acf_field_filter_query($field_name);
		// Unset current acf filter.
		foreach ($meta_query as $key => $query) {
			if (!empty($query[$meta_key])) {
				unset($meta_query[$meta_key]);
			}
		}

		$field_query = array(
			'key'   => $field_name,
			'value' => $value,
		);
		if ('checkbox' === $field_type) {
			$field_query['compare'] = 'LIKE';
		}
		// Set new filter.
		$meta_query[$meta_key] = $field_query;

		$meta_query     = new WP_Meta_Query($meta_query);
		$tax_query      = new WP_Tax_Query($tax_query);

		$meta_query_sql = $meta_query->get_sql('post', $wpdb->posts, 'ID');
		$tax_query_sql  = $tax_query->get_sql($wpdb->posts, 'ID');

		$price_range_query = DHWC_Ajax_Query::get_product_price_range_query();

		$sql  = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) FROM {$wpdb->posts} ";
		$sql .=  $price_range_query['join'] . $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " WHERE {$wpdb->posts}.post_type IN ( '" . implode("','", array_map('esc_sql', $this->_get_filtered_product_types())) . "' ) AND {$wpdb->posts}.post_status = 'publish' ";
		$sql .= $price_range_query['where'] . $tax_query_sql['where'] . $meta_query_sql['where'];


		if ($onsale_sql = DHWC_Ajax_Query::get_product_sale_sql()) {
			$sql .= ' AND ' . $onsale_sql;
		}

		if ($featured_sql = DHWC_Ajax_Query::get_product_featured_sql()) {
			$sql .= ' AND ' . $featured_sql;
		}

		$search = WC_Query::get_main_search_query_sql();
		if ($search) {
			$sql .= ' AND ' . $search;
		}

		return absint($wpdb->get_var($sql)); // WPCS: unprepared SQL ok.
	}
}
add_action('widgets_init', function () {
	return register_widget("DHWC_Ajax_Widget_ACF_Filter");
});
