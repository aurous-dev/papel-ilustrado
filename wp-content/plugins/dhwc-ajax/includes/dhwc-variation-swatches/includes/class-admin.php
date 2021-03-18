<?php

/**
 * Class DHWC_Variation_Swatches_Admin
 */
class DHWC_Variation_Swatches_Admin {
	/**
	 * The single instance of the class
	 *
	 * @var TA_WC_Variation_Swatches_Admin
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return DHWC_Variation_Swatches_Admin
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'init_attribute_hooks' ) );
		add_action( 'admin_print_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'woocommerce_product_option_terms', array( $this, 'product_option_terms' ), 10, 3 );

		// Display attribute fields
		add_action( 'dhwc_variation_swatches_product_attribute_field', array( $this, 'attribute_fields' ), 10, 3 );

		// ajax add attribute
		add_action( 'wp_ajax_dhwcvs_add_new_attribute', array( $this, 'add_new_attribute_ajax' ) );

		add_action( 'admin_footer', array( $this, 'add_attribute_term_template' ) );
	}

	/**
	 * Init hooks for adding fields to attribute screen
	 * Save new term meta
	 * Add thumbnail column for attribute term
	 */
	public function init_attribute_hooks() {
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( empty( $attribute_taxonomies ) ) {
			return;
		}

		foreach ( $attribute_taxonomies as $tax ) {
			add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', array( $this, 'add_attribute_fields' ) );
			add_action( 'pa_' . $tax->attribute_name . '_edit_form_fields', array( $this, 'edit_attribute_fields' ), 10, 2 );

			add_filter( 'manage_edit-pa_' . $tax->attribute_name . '_columns', array( $this, 'add_attribute_columns' ) );
			add_filter( 'manage_pa_' . $tax->attribute_name . '_custom_column', array( $this, 'add_attribute_column_content' ), 10, 3 );
		}

		add_action( 'created_term', array( $this, 'save_term_meta' ), 10, 2 );
		add_action( 'edit_term', array( $this, 'save_term_meta' ), 10, 2 );
	}

	/**
	 * Load stylesheet and scripts in edit product attribute screen
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( strpos( $screen->id, 'edit-pa_' ) === false && strpos( $screen->id, 'product' ) === false ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style( 'dhwc-variation-swatches-admin', DHWC_VARIATION_SWATCHES_URL.'assets/css/admin.css', array( 'wp-color-picker' ), '1.0.0' );
		wp_enqueue_script( 'dhwc-variation-swatches-admin',DHWC_VARIATION_SWATCHES_URL.'assets/js/admin.js', array( 'jquery', 'wp-color-picker', 'wp-util' ), '1.0.0', true );

		wp_localize_script(
			'dhwc-variation-swatches-admin',
			'dhwc_variation_swatches_params',
			array(
				'i18n'        => array(
					'mediaTitle'  => esc_html__( 'Choose an image', 'dhwc-ajax' ),
					'mediaButton' => esc_html__( 'Use image', 'dhwc-ajax' ),
				),
				'placeholder' => WC()->plugin_url() . '/assets/images/placeholder.png'
			)
		);
	}

	/**
	 * Create hook to add fields to add attribute term screen
	 *
	 * @param string $taxonomy
	 */
	public function add_attribute_fields( $taxonomy ) {
		$attr = DHWC_Variation_Swatches()->get_tax_attribute( $taxonomy );

		do_action( 'dhwc_variation_swatches_product_attribute_field', $attr->attribute_type, '', 'add' );
	}

	/**
	 * Create hook to fields to edit attribute term screen
	 *
	 * @param object $term
	 * @param string $taxonomy
	 */
	public function edit_attribute_fields( $term, $taxonomy ) {
		$attr  = DHWC_Variation_Swatches()->get_tax_attribute( $taxonomy );
		$value = get_term_meta( $term->term_id, $attr->attribute_type, true );

		do_action( 'dhwc_variation_swatches_product_attribute_field', $attr->attribute_type, $value, 'edit' );
	}

	/**
	 * Print HTML of custom fields on attribute term screens
	 *
	 * @param $type
	 * @param $value
	 * @param $form
	 */
	public function attribute_fields( $type, $value, $form ) {
		// Return if this is a default attribute type
		if ( in_array( $type, array( 'select', 'text' ) ) ) {
			return;
		}

		// Print the open tag of field container
		printf(
			'<%s class="form-field">%s<label for="term-%s">%s</label>%s',
			'edit' == $form ? 'tr' : 'div',
			'edit' == $form ? '<th>' : '',
			esc_attr( $type ),
			DHWC_Variation_Swatches()->types[$type],
			'edit' == $form ? '</th><td>' : ''
		);

		switch ( $type ) {
			case 'image':
				$image = $value ? wp_get_attachment_image_src( $value ) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
				?>
				<div class="dhwcvs-term-image-thumbnail" style="float:left;margin-right:10px;">
					<img src="<?php echo esc_url( $image ) ?>" width="60px" height="60px" />
				</div>
				<div style="line-height:60px;">
					<input type="hidden" class="dhwcvs-term-image" name="image" value="<?php echo esc_attr( $value ) ?>" />
					<button type="button" class="dhwcvs-upload-image-button button"><?php esc_html_e( 'Upload/Add image', 'dhwc-ajax' ); ?></button>
					<button type="button" class="dhwcvs-remove-image-button button <?php echo esc_attr($value) ? '' : 'hidden' ?>"><?php esc_html_e( 'Remove image', 'dhwc-ajax' ); ?></button>
				</div>
				<?php
				break;

			default:
				?>
				<input type="text" id="term-<?php echo esc_attr( $type ) ?>" name="<?php echo esc_attr( $type ) ?>" value="<?php echo esc_attr( $value ) ?>" />
				<?php
				break;
		}
		// Print the close tag of field container
		echo 'edit' == $form ? '</td></tr>' : '</div>';
	}

	/**
	 * Save term meta
	 *
	 * @param int $term_id
	 * @param int $tt_id
	 */
	public function save_term_meta( $term_id, $tt_id ) {
		foreach ( DHWC_Variation_Swatches()->types as $type => $label ) {
			if ( isset( $_POST[$type] ) ) {
				update_term_meta( $term_id, $type, $_POST[$type] );
			}
		}
	}

	/**
	 * Add selector for extra attribute types
	 *
	 * @param $taxonomy
	 * @param $index
	 */
	public function product_option_terms( $attribute_taxonomy, $i, $attribute ) {
		if ( ! array_key_exists( $attribute_taxonomy->attribute_type, DHWC_Variation_Swatches()->types ) ) {
			return;
		}
		?>
		<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'dhwc-ajax' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo esc_attr($i); ?>][]">
			<?php
			$args = array(
				'orderby'    => ! empty( $attribute_taxonomy->attribute_orderby ) ? $attribute_taxonomy->attribute_orderby : 'name',
				'hide_empty' => 0,
			);
			$all_terms = get_terms( $attribute->get_taxonomy(), apply_filters( 'woocommerce_product_attribute_terms', $args ) );
			if ( $all_terms ) {
				foreach ( $all_terms as $term ) {
					$options = $attribute->get_options();
					$options = ! empty( $options ) ? $options : array();
					echo '<option value="' . esc_attr( $term->term_id ) . '"' . wc_selected( $term->term_id, $options ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
				}
			}
			?>
		</select>
		<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'dhwc-ajax' ); ?></button>
		<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'dhwc-ajax' ); ?></button>
		<button class="button fr plus dhwcvs_add_new_attribute" data-type="<?php echo esc_attr($attribute_taxonomy->attribute_type) ?>"><?php esc_html_e( 'Add new', 'dhwc-ajax' ); ?></button>

		<?php
	}

	/**
	 * Add thumbnail column to column list
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_attribute_columns( $columns ) {
		$new_columns          = array();
		$new_columns['cb']    = $columns['cb'];
		$new_columns['thumb'] = '';
		unset( $columns['cb'] );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Render thumbnail HTML depend on attribute type
	 *
	 * @param $columns
	 * @param $column
	 * @param $term_id
	 */
	public function add_attribute_column_content( $columns, $column, $term_id ) {
		if('thumb'===$column){
			$attr  = DHWC_Variation_Swatches()->get_tax_attribute( $_REQUEST['taxonomy'] );
			$value = get_term_meta( $term_id, $attr->attribute_type, true );
			
			switch ( $attr->attribute_type ) {
				case 'color':
					printf( '<div class="swatch-preview swatch-color" style="background-color:%s;"></div>', esc_attr( $value ) );
					break;
	
				case 'image':
					$image = $value ? wp_get_attachment_image_src( $value ) : '';
					$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
					printf( '<img class="swatch-preview swatch-image" src="%s" width="44px" height="44px">', esc_url( $image ) );
					break;
	
				case 'label':
					printf( '<div class="swatch-preview swatch-label">%s</div>', esc_html( $value ) );
					break;
			}
		}
	}

	/**
	 * Print HTML of modal at admin footer and add js templates
	 */
	public function add_attribute_term_template() {
		global $pagenow, $post;

		if ( $pagenow != 'post.php' || ( isset( $post ) && get_post_type( $post->ID ) != 'product' ) ) {
			return;
		}
		?>

		<div id="dhwcvs-modal-container" class="dhwcvs-modal-container">
			<div class="dhwcvs-modal">
				<button type="button" class="button-link media-modal-close dhwcvs-modal-close">
					<span class="media-modal-icon"></span></button>
				<div class="dhwcvs-modal-header"><h2><?php esc_html_e( 'Add new term', 'dhwc-ajax' ) ?></h2></div>
				<div class="dhwcvs-modal-content">
					<p class="dhwcvs-term-name">
						<label>
							<?php esc_html_e( 'Name', 'dhwc-ajax' ) ?>
							<input type="text" class="widefat dhwcvs-input" name="name">
						</label>
					</p>
					<p class="dhwcvs-term-slug">
						<label>
							<?php esc_html_e( 'Slug', 'dhwc-ajax' ) ?>
							<input type="text" class="widefat dhwcvs-input" name="slug">
						</label>
					</p>
					<div class="dhwcvs-term-swatch">

					</div>
					<div class="hidden dhwcvs-term-tax"></div>

					<input type="hidden" class="dhwcvs-input" name="nonce" value="<?php echo wp_create_nonce( '_dhwcvs_create_attribute' ) ?>">
				</div>
				<div class="dhwcvs-modal-footer">
					<button class="button button-secondary dhwcvs-modal-close"><?php esc_html_e( 'Cancel', 'dhwc-ajax' ) ?></button>
					<button class="button button-primary dhwcvs-new-attribute-submit"><?php esc_html_e( 'Add New', 'dhwc-ajax' ) ?></button>
					<span class="message"></span>
					<span class="spinner"></span>
				</div>
			</div>
			<div class="dhwcvs-modal-backdrop media-modal-backdrop"></div>
		</div>

		<script type="text/template" id="tmpl-dhwcvs-input-color">

			<label><?php esc_html_e( 'Color', 'dhwc-ajax' ) ?></label><br>
			<input type="text" class="dhwcvs-input dhwcvs-input-color" name="swatch">

		</script>

		<script type="text/template" id="tmpl-dhwcvs-input-image">

			<label><?php esc_html_e( 'Image', 'dhwc-ajax' ) ?></label><br>
			<div class="dhwcvs-term-image-thumbnail" style="float:left;margin-right:10px;">
				<img src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/placeholder.png' ) ?>" width="60px" height="60px" />
			</div>
			<div style="line-height:60px;">
				<input type="hidden" class="dhwcvs-input dhwcvs-input-image dhwcvs-term-image" name="swatch" value="" />
				<button type="button" class="dhwcvs-upload-image-button button"><?php esc_html_e( 'Upload/Add image', 'dhwc-ajax' ); ?></button>
				<button type="button" class="dhwcvs-remove-image-button button hidden"><?php esc_html_e( 'Remove image', 'dhwc-ajax' ); ?></button>
			</div>

		</script>

		<script type="text/template" id="tmpl-dhwcvs-input-label">

			<label>
				<?php esc_html_e( 'Label', 'dhwc-ajax' ) ?>
				<input type="text" class="widefat dhwcvs-input dhwcvs-input-label" name="swatch">
			</label>

		</script>

		<script type="text/template" id="tmpl-dhwcvs-input-tax">

			<input type="hidden" class="dhwcvs-input" name="taxonomy" value="{{data.tax}}">
			<input type="hidden" class="dhwcvs-input" name="type" value="{{data.type}}">

		</script>
		<?php
	}

	/**
	 * Ajax function to handle add new attribute term
	 */
	public function add_new_attribute_ajax() {
		$nonce  = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
		$tax    = isset( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : '';
		$type   = isset( $_POST['type'] ) ? $_POST['type'] : '';
		$name   = isset( $_POST['name'] ) ? $_POST['name'] : '';
		$slug   = isset( $_POST['slug'] ) ? $_POST['slug'] : '';
		$swatch = isset( $_POST['swatch'] ) ? $_POST['swatch'] : '';

		if ( ! wp_verify_nonce( $nonce, '_dhwcvs_create_attribute' ) ) {
			wp_send_json_error( esc_html__( 'Wrong request', 'dhwc-ajax' ) );
		}

		if ( empty( $name ) || empty( $swatch ) || empty( $tax ) || empty( $type ) ) {
			wp_send_json_error( esc_html__( 'Not enough data', 'dhwc-ajax' ) );
		}

		if ( ! taxonomy_exists( $tax ) ) {
			wp_send_json_error( esc_html__( 'Taxonomy is not exists', 'dhwc-ajax' ) );
		}

		if ( term_exists( $_POST['name'], $_POST['tax'] ) ) {
			wp_send_json_error( esc_html__( 'This term is exists', 'dhwc-ajax' ) );
		}

		$term = wp_insert_term( $name, $tax, array( 'slug' => $slug ) );

		if ( is_wp_error( $term ) ) {
			wp_send_json_error( $term->get_error_message() );
		} else {
			$term = get_term_by( 'id', $term['term_id'], $tax );
			update_term_meta( $term->term_id, $type, $swatch );
		}

		wp_send_json_success(
			array(
				'msg'  => esc_html__( 'Added successfully', 'dhwc-ajax' ),
				'id'   => $term->term_id,
				'slug' => $term->slug,
				'name' => $term->name,
			)
		);
	}
}
