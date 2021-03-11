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

//  Expose ACF on products

$post_types = array_merge(get_post_types(), cptui_get_post_type_slugs());

foreach ($post_types as $type) {
	add_filter(
		'acf/rest_api/' . $type . '/get_fields',
		function ($data, $response) use ($post_types) {
			if ($response instanceof WP_REST_Response) {
				$data = $response->get_data();
			}

			array_walk_recursive($data, 'get_fields_recursive', $post_types);

			return $data;
		},
		10,
		3
	);
}

function get_fields_recursive($item)
{
	if (is_object($item)) {
		$item->acf = array();

		if ($fields = get_fields($item)) {
			$item->acf = $fields;
			array_walk_recursive($item->acf, 'get_fields_recursive');
		}
	}
}


?>