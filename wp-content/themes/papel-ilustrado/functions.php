<?php 

// Add support for thumbnails
add_theme_support( 'post-thumbnails' );

// Add suppport for menus
register_nav_menus(
    array(
    'primary-menu' => __( 'Primary Menu' ),
    'secondary-menu' => __( 'Secondary Menu' )
    )
);
// If Options PAGE
if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page(array(
		'page_title' 	=> 'Opciones Generales',
		'menu_title'	=> 'Opciones Generales',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
}
// If Options PAGE

// Add image to menu
// add_filter('wp_nav_menu_items', 'my_wp_nav_menu_items', 10, 2);

// function my_wp_nav_menu_items( $items, $args ) {
	
// 	// get menu
// 	$menu = wp_get_nav_menu_object($args->menu);
	
	
// 	// modify primary only
// 	// if( $args->theme_location == 'top' ) {
		
// 		// vars
// 		$logo = get_field('imagen', $menu);
// 		// $color = get_field('color', $menu);
		
		
// 		// prepend logo
// 		$html_logo = '<li class="menu-item-logo"><a href="'.home_url().'"><img src="'.$logo.'" /></a></li>';
		
		
// 		// append style
// 		// $html_color = '<style type="text/css">.navigation-top{ background: '.$color.';}</style>';
		
		
// 		// append html
// 		$items = $html_logo . $items;
		
// 	// }
	
	
// 	// return
// 	return $items;
	
// }

?>