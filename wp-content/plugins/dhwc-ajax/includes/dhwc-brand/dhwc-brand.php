<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!defined('DHWC_BRAND_TEXT_DOMAIN'))
	define('DHWC_BRAND_TEXT_DOMAIN', 'dhwc-ajax');

if(!defined('DHWC_BRAND_DIR'))
	define('DHWC_BRAND_DIR', dirname(__FILE__));

if(!defined('DHWC_BRAND_URL'))
	define('DHWC_BRAND_URL', plugins_url('dhwc-brand',DHWC_BRAND_DIR));

if(!class_exists('DHWC_Brand',false))
	require_once DHWC_BRAND_DIR.'/includes/class-dhwc-brand.php';