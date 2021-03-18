<?php
/*
 * Plugin Name: DHWC Ajax
 * Plugin URI: http://sitesao.com/
 * Description: Enable Ajax for WooCommerce Shop. Simple but flexible.
 * Version: 1.2.2
 * Author: Sitesao team
 * Text Domain: dhwc-ajax
 * Author URI: http://sitesao.com/
 * License: License GNU General Public License version 2 or later;
 * Copyright 2017  Sitesao team
 * WC requires at least: 3.0.0
 * WC tested up to: 4.9.2
 */

if(!defined('DHWC_AJAX_VERSION')){
	define('DHWC_AJAX_VERSION','1.2.2');
}

if(!defined('DHWC_AJAX_URL')){
	define('DHWC_AJAX_URL',untrailingslashit( plugins_url( '/', __FILE__ ) ));
}
if(!defined('DHWC_AJAX_DIR')){
	define('DHWC_AJAX_DIR',untrailingslashit( plugin_dir_path( __FILE__ ) ));
}
if(!class_exists('DHWC_Ajax')){
	require_once DHWC_AJAX_DIR.'/includes/class-dhwc-ajax.php';
}