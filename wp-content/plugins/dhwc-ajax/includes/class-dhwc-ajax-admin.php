<?php

class DHWC_Ajax_Admin {
	public function __construct(){
		
		add_filter( 'woocommerce_get_settings_pages', array($this,'add_settings') );
	}
	
	
	public function add_settings($settings){
		$settings[] = include DHWC_AJAX_DIR.'/includes/class-dhwc-ajax-admin-settings.php';
		return $settings;
	}
}

new DHWC_Ajax_Admin();