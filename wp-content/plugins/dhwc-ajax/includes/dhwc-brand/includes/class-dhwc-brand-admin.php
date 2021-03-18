<?php

class DHWC_Brand_Admin {
	public function __construct(){
		//Admin script
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		//Brand permalink setting
		add_action( 'admin_init', array($this,'permalink_product_brand_add') );
		add_action( 'admin_init', array($this,'permalink_product_brand_save') );
		
		// Brand sortable.
		add_filter( 'woocommerce_sortable_taxonomies',array($this,'product_brand_sortable'));
		// Brand columns.
		add_filter( 'manage_edit-product_brand_columns', array( $this, 'product_brand_columns' ) );
		add_filter( 'manage_product_brand_custom_column', array( $this, 'product_brand_column' ), 10, 3 );
		
		//Brand DESC
		add_action( 'product_brand_pre_add_form', array($this,'product_brand_description') );
		
		//Brand Field
		add_action('product_brand_add_form_fields',array($this,'add_product_brand_fields'));
		add_action('product_brand_edit_form_fields',array($this,'edit_product_brand_field'));
		add_action( 'created_term', array( $this, 'save_product_brand_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_product_brand_fields' ), 10, 3 );
		
		//Product list table
		add_filter( 'manage_edit-product_columns',array($this,'product_list_table_columns') );
		add_action('manage_product_posts_custom_column', array($this,'product_list_table_column'));
	}
	
	public function product_list_table_columns($existing_columns){
		$columns = array();
		foreach ($existing_columns as $key=>$column){
			if($key=='product_cat'){
				$columns[$key] =$column;
				$columns['product_brand'] = __( 'Brands', DHWC_BRAND_TEXT_DOMAIN );
			}else{
				$columns[$key]=$column;
			}
		}
		return $columns;
	}
	
	public function product_list_table_column($column){
		global $post;
		if('product_brand'===$column){
			if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
				echo '<span class="na">&ndash;</span>';
			} else {
				foreach ( $terms as $term ) {
					$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=product' ) . ' ">' . $term->name . '</a>';
				}
				echo implode( ', ', $termlist );
			}
		}
		return $column;
	}
	
	public function permalink_product_brand_add(){
		add_settings_field(
			'dhwc_product_brand_slug',      		
			__( 'Product brand base', DHWC_BRAND_TEXT_DOMAIN ),
			array($this,'_permalink_product_brand_input'),
			'permalink', 
			'optional' 
		);
	}
	
	public function _permalink_product_brand_input(){
		$permalinks = get_option( 'woocommerce_permalinks' );
		?>
		<input name="dhwc_product_brand_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['brand_base'] ) ) echo esc_attr( $permalinks['brand_base'] ); ?>" placeholder="<?php echo _x('product-brand', 'slug', DHVC_WOO) ?>" />
		<?php
	}
	
	public function permalink_product_brand_save(){
		if ( ! is_admin() ) {
			return;
		}
		
		if (isset($_POST['dhwc_product_brand_slug'])){
					
			$permalinks = (array) get_option( 'woocommerce_permalinks', array() );

			$permalinks['brand_base']  = wc_sanitize_permalink( wp_unslash( $_POST['dhwc_product_brand_slug'] ) ); // WPCS: input var ok, sanitization ok.
			
			update_option( 'woocommerce_permalinks', $permalinks );
		}
	}
	
	public function product_brand_description(){
		echo wpautop( __( 'Product brands for your store can be managed here. To change the order of brands on the front-end you can drag and drop to sort them. To see more brands listed click the "screen options" link at the top of the page.', DHWC_BRAND_TEXT_DOMAIN ) );
	}
	public function admin_styles(){
		$screen  = get_current_screen();
		$screen_id    = $screen ? $screen->id : '';
		// Edit product brand pages.
		if ( in_array( $screen_id, array('edit-product', 'edit-product_brand' ) ) ) {
			wp_enqueue_style('dhwc-brand-admin',DHWC_BRAND_URL.'/assets/css/admin.css');
		}
	}
	
	public function admin_scripts(){
		$screen  = get_current_screen();
		$screen_id    = $screen ? $screen->id : '';
		// Edit product brand pages.
		if ( in_array( $screen_id, array( 'edit-product_brand' ) ) ) {
			wp_enqueue_media();
		}
		
	}
	
	public function product_brand_sortable($sortable){
		$sortable[] = 'product_brand';
		return $sortable;
	}
	
	public function product_brand_columns($columns){
		$new_columns = array();
		
		if ( isset( $columns['cb'] ) ) {
			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}
		
		$new_columns['thumb'] = __( 'Image', DHWC_BRAND_TEXT_DOMAIN );
		
		$columns           = array_merge( $new_columns, $columns );
		$columns['handle'] = '';
		
		return $columns;
	}
	
	public function product_brand_column( $columns, $column, $id  ){
		if ( 'thumb' === $column ) {
			
			$thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );
		
			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = wc_placeholder_img_src();
			}
		
			// Prevent esc_url from breaking spaces in urls for image embeds. Ref: https://core.trac.wordpress.org/ticket/23605 .
			$image    = str_replace( ' ', '%20', $image );
			$columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'dhwc-ajax' ) . '" class="wp-post-image" height="48" width="48" />';
		}
		if ( 'handle' === $column ) {
			$columns .= '<input type="hidden" name="term_id" value="' . esc_attr( $id ) . '" />';
		}
		return $columns;
	}
	
	public function add_product_brand_fields(){
		?>
		<div class="form-field term-thumbnail-wrap">
			<label><?php esc_html_e( 'Thumbnail', DHWC_BRAND_TEXT_DOMAIN ); ?></label>
			<div id="product_brand_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
			<div style="line-height: 60px;">
				<input type="hidden" id="product_brand_thumbnail_id" name="product_brand_thumbnail_id" />
				<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', DHWC_BRAND_TEXT_DOMAIN ); ?></button>
				<button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', DHWC_BRAND_TEXT_DOMAIN ); ?></button>
			</div>
			<script type="text/javascript">

				// Only show the "remove image" button when needed
				if ( ! jQuery( '#product_brand_thumbnail_id' ).val() ) {
					jQuery( '.remove_image_button' ).hide();
				}

				// Uploading files
				var file_frame;

				jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php esc_html_e( 'Choose an image', DHWC_BRAND_TEXT_DOMAIN ); ?>',
						button: {
							text: '<?php esc_html_e( 'Use image', DHWC_BRAND_TEXT_DOMAIN ); ?>'
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
						var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

						jQuery( '#product_brand_thumbnail_id' ).val( attachment.id );
						jQuery( '#product_brand_thumbnail' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
						jQuery( '.remove_image_button' ).show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery( document ).on( 'click', '.remove_image_button', function() {
					jQuery( '#product_brand_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
					jQuery( '#product_brand_thumbnail_id' ).val( '' );
					jQuery( '.remove_image_button' ).hide();
					return false;
				});
			</script>
			<div class="clear"></div>
		</div>
		<?php 
	}
	
	public function edit_product_brand_field($term){
		$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
		
		if ( $thumbnail_id ) {
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		} else {
			$image = wc_placeholder_img_src();
		}
		?>
		<tr class="form-field term-thumbnail-wrap">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Thumbnail', DHWC_BRAND_TEXT_DOMAIN ); ?></label></th>
			<td>
				<div id="product_brand_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="product_brand_thumbnail_id" name="product_brand_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ); ?>" />
					<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', DHWC_BRAND_TEXT_DOMAIN ); ?></button>
					<button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', DHWC_BRAND_TEXT_DOMAIN ); ?></button>
				</div>
				<script type="text/javascript">

					// Only show the "remove image" button when needed
					if ( '0' === jQuery( '#product_brand_thumbnail_id' ).val() ) {
						jQuery( '.remove_image_button' ).hide();
					}

					// Uploading files
					var file_frame;

					jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( file_frame ) {
							file_frame.open();
							return;
						}

						// Create the media frame.
						file_frame = wp.media.frames.downloadable_file = wp.media({
							title: '<?php esc_html_e( 'Choose an image', DHWC_BRAND_TEXT_DOMAIN ); ?>',
							button: {
								text: '<?php esc_html_e( 'Use image', DHWC_BRAND_TEXT_DOMAIN ); ?>'
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						file_frame.on( 'select', function() {
							var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
							var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

							jQuery( '#product_brand_thumbnail_id' ).val( attachment.id );
							jQuery( '#product_brand_thumbnail' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
							jQuery( '.remove_image_button' ).show();
						});

						// Finally, open the modal.
						file_frame.open();
					});

					jQuery( document ).on( 'click', '.remove_image_button', function() {
						jQuery( '#product_brand_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
						jQuery( '#product_brand_thumbnail_id' ).val( '' );
						jQuery( '.remove_image_button' ).hide();
						return false;
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php 
	}
	
	public function save_product_brand_fields($term_id, $tt_id = '', $taxonomy = ''){
		if ( isset( $_POST['product_brand_thumbnail_id'] ) && 'product_brand' === $taxonomy ) { 
			update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['product_brand_thumbnail_id'] ) );
		}
	}
}
new DHWC_Brand_Admin();