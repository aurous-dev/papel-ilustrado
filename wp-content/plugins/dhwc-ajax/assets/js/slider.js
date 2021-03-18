jQuery( function( $ ) {
	var rquery = ( /\?/ );
	$( document.body ).bind( 'dhwc_ajax_slider_create dhwc_ajax_slider_slide', function( event, $scope, min, max ) {
		
		if($scope.hasClass('is-filter-weight'))
			$unit = dhwc_ajax_slider_params.weight_unit;
		else
			$unit = dhwc_ajax_slider_params.dimension_unit;
		
		$( '.slider-filter__label span.from', $scope).html(min + $unit);

		$( '.slider-filter__label span.to', $scope).html(max + $unit);
		
		var $button 		= $('.slider-filter__button',$scope),
			$current_url 	= $button.data('current_url'),
			$filter_name 	= $button.data('filter_name'),
			$link;
				
		$link = ( $current_url += ( rquery.test( $current_url ) ? "&" : "?" ) + $filter_name + '=' + min + '-' + max );
	
		$button.attr('href',$link);
		
		$( document.body ).trigger( 'dhwc_ajax_slider_updated', [ $scope, min, max ] );
	});
	
	$.fn.dhwc_ajax_slider_filter = function(){
		return this.each(function(){
			var $this = $(this),
				$control = $('.slider-filter__control',$this),
				$label = $('.slider-filter__label',$this);
			
			$control.show();
			$label.show();
			
			var from = $control.data( 'from' ),
				to = $control.data( 'to' ),
				current_from = $control.data( 'current_from' ),
				current_to = $control.data( 'current_to' );
	
			$control.not( '.ui-slider').slider({
				range: true,
				animate: true,
				min: from,
				max: to,
				values: [ current_from, current_to ],
				create: function() {
	
					$control.data( 'current_from' , current_from );
					$control.data( 'current_to' , current_to );
	
					$( document.body ).trigger( 'dhwc_ajax_slider_create', [ $this, current_from, current_to] );
				},
				slide: function( event, ui ) {
					$control.data( 'current_from' , ui.values[0] );
					$control.data( 'current_to' , ui.values[1] );
	
					$( document.body ).trigger( 'dhwc_ajax_slider_slide', [ $this, ui.values[0], ui.values[1] ] );
				},
				change: function( event, ui ) {
					$( document.body ).trigger( 'dhwc_ajax_slider_change', [ $this, ui.values[0], ui.values[1] ] );
				}
			});
		})
	};
	
	$('.dhwc-ajax__slider-filter').dhwc_ajax_slider_filter();

	var hasSelectiveRefresh = (
		'undefined' !== typeof wp &&
		wp.customize &&
		wp.customize.selectiveRefresh &&
		wp.customize.widgetsPreview &&
		wp.customize.widgetsPreview.WidgetPartial
	);
	if ( hasSelectiveRefresh ) {
		wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function() {
			$('.dhwc-ajax__slider-filter').dhwc_ajax_slider_filter();
		} );
	}
});