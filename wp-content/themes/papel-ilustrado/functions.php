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
?>