<?php

/**
 * Class DHWC_Variation_Swatches_Frontend
 */
class DHWC_Variation_Swatches_Frontend {
	/**
	 * The single instance of the class
	 *
	 * @var DHWC_Variation_Swatches_Frontend
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return DHWC_Variation_Swatches_Frontend
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
		
		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'get_swatch_html' ), 100, 2 );
		add_filter( 'dhwc_variation_swatches_swatch_html', array( $this, 'swatch_html' ), 5, 4 );
	}


	/**
	 * Filter function to add swatches bellow the default selector
	 *
	 * @param $html
	 * @param $args
	 *
	 * @return string
	 */
	public function get_swatch_html( $html, $args ) {
		$swatch_types = DHWC_Variation_Swatches()->types;
		$attr         = DHWC_Variation_Swatches()->get_tax_attribute( $args['attribute'] );

		// Return if this is normal attribute
		if ( empty( $attr ) ) {
			return $html;
		}

		if ( ! array_key_exists( $attr->attribute_type, $swatch_types ) ) {
			//Change select to Dropdown
			return $html;
		}

		$options   = $args['options'];
		$product   = $args['product'];
		$attribute = $args['attribute'];
		$class     = "variation-selector variation-select-{$attr->attribute_type}";
		$swatches  = '';

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[$attribute];
		}

		if ( array_key_exists( $attr->attribute_type, $swatch_types ) ) {
			if ( ! empty( $options ) && $product && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options ) ) {
						$swatches .= apply_filters( 'dhwc_variation_swatches_swatch_html', '', $term, $attr, $args );
					}
				}
			}

			if ( ! empty( $swatches ) ) {
				$class .= ' hidden';

				$swatches = '<div class="dhwcvs-swatches" data-attribute_name="attribute_' . esc_attr( $attribute ) . '">' . $swatches . '</div>';
				$html     = '<div class="' . esc_attr( $class ) . '">' . $html . '</div>' . $swatches;
			}
		}

		return $html;
	}

	/**
	 * Print HTML of a single swatch
	 *
	 * @param $html
	 * @param $term
	 * @param $attr
	 * @param $args
	 *
	 * @return string
	 */
	public function swatch_html( $html, $term, $attr, $args, $is_tooltip = true ) {
		$selected = '';
		$tooltip_class = $is_tooltip ? 'dhtooltip ':'';
		if(is_array($args)){
			if($args['selected']===true)
				$selected = 'selected';
			else
				$selected = sanitize_title( $args['selected'] ) == $term->slug ? 'selected' : '';
		}
		
		$name     = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );
		//var_dump($attr->attribute_type);
		switch ( $attr->attribute_type ) {
			case 'color':
				$color = get_term_meta( $term->term_id, 'color', true );
				list( $r, $g, $b ) = sscanf( $color, "#%02x%02x%02x" );
				$html = sprintf(
					'<span class="'.$tooltip_class.'dhswatch swatch-color swatch-%s %s" style="background-color:%s;color:%s;" aria-label="%s" data-value="%s"></span><span class="swatch-label">%s</span>',
					esc_attr( $term->slug ),
					$selected,
					esc_attr( $color ),
					"rgba($r,$g,$b,0.5)",
					esc_attr( $name ),
					esc_attr( $term->slug ),
					$name
				);
				break;

			case 'image':
				$image = get_term_meta( $term->term_id, 'image', true );
				$image = $image ? wp_get_attachment_image_src( $image) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
				$html  = sprintf(
					'<span class="'.$tooltip_class.'dhswatch swatch-image swatch-%s %s" aria-label="%s" data-value="%s"><img src="%s"></span><span class="swatch-label">%s</span>',
					esc_attr( $term->slug ),
					$selected,
					esc_attr( $name ),
					esc_attr( $term->slug ),
					esc_url( $image ),
					esc_attr( $name )
				);
				break;

			case 'label':
				$label = get_term_meta( $term->term_id, 'label', true );
				$label = $label ? $label : $name;
				$html  = sprintf(
					'<span class="dhswatch swatch-label swatch-%s %s" aria-label="%s" data-value="%s">%s</span>',
					esc_attr( $term->slug ),
					$selected,
					esc_attr( $name ),
					esc_attr( $term->slug ),
					esc_html( $label )
				);
				break;
			default:
				$label = get_term_meta( $term->term_id, 'label', true );
				$label = $label ? $label : $name;
				$html  = $label;
				break;
		}

		return $html;
	}
}