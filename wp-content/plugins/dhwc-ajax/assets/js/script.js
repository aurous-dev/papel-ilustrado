/*!
 * Bez 1.0.11
 * http://github.com/rdallasgray/bez
 *
 * A plugin to convert CSS3 cubic-bezier co-ordinates to jQuery-compatible easing functions
 *
 * With thanks to Nikolay Nemshilov for clarification on the cubic-bezier maths
 * See http://st-on-it.blogspot.com/2011/05/calculating-cubic-bezier-function.html
 *
 * Copyright 2016 Robert Dallas Gray. All rights reserved.
 * Provided under the FreeBSD license: https://github.com/rdallasgray/bez/blob/master/LICENSE.txt
 */
(function(factory) {
  if (typeof exports === "object") {
    factory(require("jquery"));
  } else if (typeof define === "function" && define.amd) {
    define(["jquery"], factory);
  } else {
    factory(jQuery);
  }
}(function($) {
  $.extend({ bez: function(encodedFuncName, coOrdArray) {
    if ($.isArray(encodedFuncName)) {
      coOrdArray = encodedFuncName;
      encodedFuncName = 'bez_' + coOrdArray.join('_').replace(/\./g, 'p');
    }
    if (typeof $.easing[encodedFuncName] !== "function") {
      var polyBez = function(p1, p2) {
        var A = [null, null], B = [null, null], C = [null, null],
            bezCoOrd = function(t, ax) {
              C[ax] = 3 * p1[ax], B[ax] = 3 * (p2[ax] - p1[ax]) - C[ax], A[ax] = 1 - C[ax] - B[ax];
              return t * (C[ax] + t * (B[ax] + t * A[ax]));
            },
            xDeriv = function(t) {
              return C[0] + t * (2 * B[0] + 3 * A[0] * t);
            },
            xForT = function(t) {
              var x = t, i = 0, z;
              while (++i < 14) {
                z = bezCoOrd(x, 0) - t;
                if (Math.abs(z) < 1e-3) break;
                x -= z / xDeriv(x);
              }
              return x;
            };
        return function(t) {
          return bezCoOrd(xForT(t), 1);
        }
      };
      $.easing[encodedFuncName] = function(x, t, b, c, d) {
        return c * polyBez([coOrdArray[0], coOrdArray[1]], [coOrdArray[2], coOrdArray[3]])(t/d) + b;
      }
    }
    return encodedFuncName;
  }});
}));

(function($) {
	 $.fn.dhwc_variation_swatches_form = function() {
       return this.each(function() {
           var $form = $(this),
               clicked = null,
               selected = [];
           if ($('.dhwcvs-swatches', $form).length) {
               $form.addClass('variations--swatches')
           } else {
               $form.addClass('variations--dropdown')
           }
           $form
               .on('woocommerce_update_variation_values', function() {
                   $(this).find('.variation-selector').each(function() {
                       var $this = $(this);
                       var $select = $this.find('select');
                       var swatches = $this.next('.dhwcvs-swatches');
                       swatches.find('.dhswatch').addClass('disabled');
                       $select.find('option.enabled').each(function() {
                           var opt = $(this);
                           swatches.find('.dhswatch[data-value="' + opt.attr('value') + '"]').removeClass('disabled').addClass('enabled');
                       });
                       if (!$('.selected', swatches).length && '' !== $select.val()) {
                           swatches.find('.dhswatch[data-value="' + $select.val() + '"]').addClass('enabled selected');
                       }
                   });
               })
               .on('click', '.dhswatch', function(e) {
                   e.preventDefault();
                   var $el = $(this),
                       $select = $el.closest('.value').find('select'),
                       attribute_name = $select.data('attribute_name') || $select.attr('name'),
                       value = $el.data('value');

                   $select.trigger('focusin');

                   // Check if this combination is available
                   if (!$select.find('option[value="' + value + '"]').length) {
                       return;
                   }

                   clicked = attribute_name;

                   if (selected.indexOf(attribute_name) === -1) {
                       selected.push(attribute_name);
                   }

                   if ($el.hasClass('selected')) {
                       $select.val('');
                       $el.removeClass('selected');

                       delete selected[selected.indexOf(attribute_name)];
                   } else {
                       $el.addClass('selected').siblings('.selected').removeClass('selected');
                       $select.val(value);
                   }

                   $select.change();
               })
               .on('click', '.reset_variations', function() {
                   $(this).closest('.variations_form').find('.dhswatch.selected').removeClass('selected');
                   selected = [];
               })
               .on('dhwcvs_no_matching_variations', function() {
                   window.alert(wc_add_to_cart_variation_params.i18n_no_matching_variations_text);
               });
       });
   };
   $(document).ready(function(){
	   $('.variations_form').dhwc_variation_swatches_form();
   })
})(jQuery);

(function($) {
	'use strict';
	var rquery = ( /\?/ );
	var DHWC_Ajax = function(){
		this.body = $(document.body);
		this.window = $(window);
		this.isAjaxLoading = false;
		this.isSearchResult = false;
		this.isSingleAddingToCart = false;
		this.loopWrapper = $(dhwc_ajax_params.elements.product_wrapper);
		this.selector = {
			toolbarSearchForm: $('.toolbar__search-form'),
			toolbarFilterWidgets: $('.toolbar__filter-widgets'),
			toolbarFilterCategory: $('.toolbar__filter-category'),
			categoriesMenu: $('.toobar__categories')
		}
	}
	
	DHWC_Ajax.prototype = {
		init: function(){
			var self = this;
			//Single Add to cart ajax
			this.initSingleAjaxAddToCart();
			
			if(!self.loopWrapper.length)
				return;
		
			var userAgent = navigator.userAgent;
			self.body.on('dhwc_ajax_after_replace_content dhwc_ajax_after_infiniteScroll', function(event, $content) {
	            if (-1 !== userAgent.indexOf('Safari') && -1 === userAgent.indexOf('Chrome') && -1 === navigator.userAgent.indexOf('Android'))
	            	self.loadImageSrcset($content);
	        }); 
			
			self.window.off('popstate.dhwc_ajax').on('popstate.dhwc_ajax', function(e) {
                if (!e.originalEvent.state) {
                    return;
                }
                if (e.originalEvent.state.dhwc_ajax) {
                    self.load(window.location.href, false, true);
                }
            });
			
			if (self.historySupport()) {
                window.history.replaceState({
                	dhwc_ajax: true
                }, '', window.location.href);
            }
			//Toolbars
			this.initCategoriesMenu();
			this.initToolbarFilterButton();
			this.iniToolbarSearchButton();
			this.initOrderingForm();
			
			this.initSearchForm();
			 
			this.initInfiniteScroll();
			this.initFilterWidgets();
			this.initPagination();
			
		},
		loadImageSrcset: function($imagesScope) {
	        $('img[srcset]', $imagesScope).each(function(index, image) {
	        	image.outerHTML = image.outerHTML;
	        });
	    },
		initSingleAjaxAddToCart: function($formScope) {
			
            if ('yes' !== dhwc_ajax_params.ajax_add_to_cart)
                return;

            var self 			= this,
            	$notices_target = $( '.woocommerce-notices-wrapper:first' ) || $thisbutton.closest( '.product' ).parent();

            $formScope = $formScope || $('.single-product');
            

            $(".product:not(.product-type-external) form.cart", $formScope).on('submit', function(e) {

                e.stopPropagation();
                e.preventDefault();

                var $form = $(this);
                
                if (self.isSingleAddingToCart)
                    return;

                self.isSingleAddingToCart = true;

                var $thisbutton = $('.single_add_to_cart_button', $form);
                
                $.ajax({
                    type: "POST",
                    url: self.create_ajax_url('add_to_cart'),
                    data: $form.serialize(),
                    cache: false,
                    headers: {
                        'cache-control': 'no-cache'
                    },
                    beforeSend: function() {
                        $('.single_add_to_cart_button', $form).addClass('js-loading');
                    },
                    error: function(XMLHttpRequest, status, error) {

                    	self.isSingleAddingToCart = false;

                        $('.single_add_to_cart_button', $form).removeClass('js-loading');
                    },
                    success: function(response, status, xhr) {
                    	
                    	$thisbutton.removeClass('js-loading');
                    	
                    	self.isSingleAddingToCart = false;
                    	
                    	$( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
                    	
                    	$notices_target.prepend( response.fragments.dhwc_ajax_add_to_cart_message );
                    	var fragments = response.fragments
                    	if ( fragments ) {
                			$.each( fragments, function( key ) {
                				$( key )
                					.addClass( 'updating' )
                					.fadeTo( '400', '0.6' )
                					.block({
                						message: null,
                						overlayCSS: {
                							opacity: 0.6
                						}
                					});
                			});

                			$.each( fragments, function( key, value ) {
                				$( key ).replaceWith( value );
                				$( key ).stop( true ).css( 'opacity', '1' ).unblock();
                			});

                			self.body.trigger( 'wc_fragments_loaded' );
                		}
                    	
                    	self.body.trigger('dhwc_ajax_single_ajax_added_to_cart', [response.fragments, response.cart_hash]);
                    },
                    complete: function() {
                    	if('yes'===dhwc_ajax_params.single_ajax_added_to_cart_scroll)
                    		self.scrollTo($notices_target)
    				}
                });

                return false;
            });
        },
		_showSearchForm: function($duration,$callback){
			this.body.addClass('dhwc-ajax--open-search-form');
			this.selector.toolbarSearchForm.stop(true).slideDown({
				duration: $duration,
				easing: $.bez([0.25, 0.46, 0.45, 0.94]),
				complete: function(){
					$callback && $callback();
				}
			});
		},
		_hideSearchForm: function($duration,$callback){
			$('.toolbar__search-button').removeClass('js-active');
			this.body.removeClass('dhwc-ajax--open-search-form');
			this.selector.toolbarSearchForm.stop(true).slideUp({
				duration: $duration,
				easing: $.bez([0.25, 0.46, 0.45, 0.94]),
				complete: function(){
					$callback && $callback();
				}
			});
		},
		toggleSearchForm: function($callback,$fast){
			var self = this,
				$duration = 500;
			
			if($fast)
				$duration = 0;
			
			if(self.body.hasClass('dhwc-ajax--open-widget')){
				self._hideWidgetsFilter();
				$('.toolbar__filter-button').removeClass('js-active').addClass('js-close');
			}
			
			if(self.body.hasClass('dhwc-ajax--open-categories')){
				self._hideCategoriesMenu();
				$('.toolbar__filter-category').removeClass('js-active');
			}
			
			if(self.body.hasClass('dhwc-ajax--open-search-form')){
				self._hideSearchForm($duration, $callback);
			}else{
				self._showSearchForm($duration,$callback);
			}
		},
		_showCategoriesMenu: function(){
			this.body.addClass('dhwc-ajax--open-categories');
			this.selector.categoriesMenu.stop(true).slideDown({
				duration: 500,
				easing: $.bez([0.25, 0.46, 0.45, 0.94]),
				complete: function(){
					
				}
			});
		},
		_hideCategoriesMenu: function(){
			$('.toolbar__filter-category').removeClass('js-active');
			this.body.removeClass('dhwc-ajax--open-categories');
			this.selector.categoriesMenu.stop(true).slideUp({
				duration: 500,
				easing: $.bez([0.25, 0.46, 0.45, 0.94]),
				complete: function(){
					
				}
			});		
		},
		toggleCategoriesMenu: function(){
			var self = this;
			if(self.body.hasClass('dhwc-ajax--open-widget')){
				self._hideWidgetsFilter();
				$('.toolbar__filter-button').removeClass('js-active').addClass('js-close');
			}
			if(self.body.hasClass('dhwc-ajax--open-search-form')){
				self._hideSearchForm(500);
				$('.toolbar__search-button').removeClass('js-active');
			}
			if(self.body.hasClass('dhwc-ajax--open-categories')){
				self._hideCategoriesMenu();
			}else{
				self._showCategoriesMenu();
			}
		},
		_showWidgetsFilter: function(){
			this.body.addClass('dhwc-ajax--open-widget');
			var widget_class = 'display-above';
			if($('.dhwc-ajax__toolbar').hasClass('is-above-absolute')){
				widget_class = 'display-absolute';
			}

			if(this.selector.toolbarFilterWidgets.hasClass(widget_class)){
				this.selector.toolbarFilterWidgets.stop(true).slideDown({
					duration: 500,
					easing: $.bez([0.25, 0.46, 0.45, 0.94])
				});
			}
		},
		_hideWidgetsFilter: function(){
			$('.toolbar__filter-button').removeClass('js-active').addClass('js-close');
			this.body.removeClass('dhwc-ajax--open-widget');
			var widget_class = 'display-above';
			if($('.dhwc-ajax__toolbar').hasClass('is-above-absolute')){
				widget_class = 'display-absolute';
			}
			if(this.selector.toolbarFilterWidgets.hasClass(widget_class)){
				this.selector.toolbarFilterWidgets.stop(true).slideUp({
					duration: 500,
					easing: $.bez([0.25, 0.46, 0.45, 0.94])
				});
			}		
		},
		toggleWidgetsFilter: function(){
			var self = this;
			if(!$(dhwc_ajax_params.elements.product_wrapper).data('found_posts'))
				return;
			
			if(self.body.hasClass('dhwc-ajax--open-search-form')){
				self._hideSearchForm(500);
				$('.toolbar__search-button').removeClass('js-active');
			}
			if(self.body.hasClass('dhwc-ajax--open-categories')){
				self._hideCategoriesMenu();
				$('.toolbar__filter-category').removeClass('js-active');
			}
			
			if(self.body.hasClass('dhwc-ajax--open-widget')){
				self._hideWidgetsFilter();
			}else{
				self._showWidgetsFilter();
			}
		},
		iniToolbarSearchButton: function(){
			var self = this; 
			this.body.on('click','.toolbar__search-button',function(e){
				e.stopPropagation();
	            e.preventDefault();
	            var $this = $(this),
	            	$form_input = $('.search-form__input',self.selector.toolbarSearchForm);
	            
	            $this.toggleClass('js-active');
	            
	            var focus = function(){
	            	try {
    	            	if($this.hasClass('js-active'))
    	            		$form_input.focus();
                    } catch (e) {}
	            }
	            
	            self.toggleSearchForm(focus)
			})
		},
		initSearchForm: function(){
			var self		 			= this,
				$form 					= $('form',self.selector.toolbarSearchForm),
        		$form_input 			= $('.search-form__input',self.selector.toolbarSearchForm),
        		$form_clear_button 		= $('.search-form__clear',self.selector.toolbarSearchForm);
			
			var show_result_count = function(){
				var $result_count = '<a title="'+ dhwc_ajax_params.i18n.remove_search_filter +'" class="toolbar__search__result-count" href="#">' + $(dhwc_ajax_params.elements.product_wrapper).data('result_count') +'</a>';
				$('.toolbar__search__result-count').remove();
				$('.toolbar-body').append($result_count)
			};
			
			this.body.on('click','.toolbar__search__result-count',function(e){
				e.stopPropagation();
				e.preventDefault();
				$form_clear_button.trigger('click')
			})
			
			$form.on('submit',function(e){
				 e.stopPropagation();
				 e.preventDefault();
				 
				 var $keyword = $form_input.val();
				 
				 if((/\S/.test($keyword)) && $keyword.length > dhwc_ajax_params.search_min_keyword - 1){
					 var $action = $form.attr('action'),
				 	 	 $url = ( $action += ( rquery.test( $action ) ? "&" : "?" ) + $form.serialize() );
	
					 self.isSearchResult = true;
						
					 self.load($url,false, true, show_result_count)
				 }
			});
			
			var afterClear = function(){
				$('.toolbar__search__result-count').remove();
            	$form_input.val('');
			}
			
			$form_clear_button.on('click',function(e){
				e.stopPropagation();
	            e.preventDefault();
	            
	            if(self.isSearchResult){
	            	self.isSearchResult = false;
    	            self.load(self.selector.toolbarSearchForm.data('current_url'), true, true, $.proxy(function(){
    	            	self.toggleSearchForm(afterClear,true);
    	            },self))
            	}else{
            		self.toggleSearchForm(afterClear,true);
            	}
	            
			})
			
			$form_input.on('keyup', $.proxy( function(e){
				var $field = e.target,
					$keyword = $field.value;
				if($keyword.length > 1){
					$form_clear_button.addClass('js-show')
				}else{
					$form_clear_button.removeClass('js-show')
				}
			}, this));
			
		},
		initToolbarFilterButton: function(){
			var self = this;
			self.body.on('click','.toolbar__filter-button, .filter-button__close',function(e){
				e.stopPropagation();
	            e.preventDefault();
	            var $this = $(this)
	            
	            if($this.hasClass('js-active')){
	            	$this.removeClass('js-active').addClass('js-close')
	            }else{
	            	$this.removeClass('js-close').addClass('js-active')
	            }
	            
	            self.toggleWidgetsFilter();
			});
		},
		initInfiniteScroll: function(){
			var self = this;
			if(typeof InfiniteScroll === 'undefined' || !$(dhwc_ajax_params.elements.pagination_wrapper).length)
				return;
			
			var $pagination_el = $('.dhwc-ajax__pagination'),
				$products = $(dhwc_ajax_params.elements.product_list),
			    $append = dhwc_ajax_params.elements.product_item,
				$outlayer = null;
			
			var options = {
				// options
                path: dhwc_ajax_params.elements.pagination_next,
                append: $append,
                nav:  dhwc_ajax_params.elements.pagination_wrapper,
                debug: false,
                scrollThreshold: 400,
                history: false,
                hideNav: dhwc_ajax_params.elements.pagination_wrapper,
                outlayer: $outlayer,
                loadOnScroll: true,
                button: '.load-more__button'
			};
			
			if(this.body.hasClass('dhwc-ajax-pagination-loadmore')){
				options.loadOnScroll = false;
				options.button = '.pagination__button-loadmore';
			}
			
			$products.infiniteScroll(options);
			
			$products.on('request.infiniteScroll', function() {
				$pagination_el.addClass('js-loading');
            });

			$products.on('error.infiniteScroll, last.infiniteScroll, append.infiniteScroll', function() {
				$pagination_el.removeClass('js-loading');
            });
			
			$products.on('append.infiniteScroll', function(event, response, path, items) {
                self.body.trigger('dhwc_ajax_after_infiniteScroll',[items]);
            });
		},
		initCategoriesMenu: function(){
			var self = this,
				$toolbar_categories = $('.toobar__categories');
			
			if(!$toolbar_categories.length)
				return;
			
			self.body.on('click','.toolbar__filter-category',function(e){
				e.stopPropagation();
	            e.preventDefault();
	            
	            $(this).toggleClass('js-active');
	            self.toggleCategoriesMenu();
			});
			
			self.body.on('click','.toolbar__category-active a',function(e){
				e.stopPropagation();
	            e.preventDefault();
	            
	            var $this = $(this),
            	$url = $this.attr('href');
	            
	            self.load($url,true,true);
	            
			});
			
			self.body.on('click','.toobar__categories .category__item-link',function(e){
				e.stopPropagation();
	            e.preventDefault();
	            
	            var $this = $(this),
	            	$url = $this.attr('href');
	            self._hideSearchForm();
	            self._hideWidgetsFilter();
	            $('.toolbar__search__result-count').remove();
	            self.load($url,true,true);
			})
		},
		initWCWidgetPrice: function(){
			var self = this,
				$filterWrapper = $('.woocommerce.widget_price_filter');
			
			 if(!$filterWrapper.length)
				 return;
			
			 $filterWrapper.find('input#min_price, input#max_price').hide();
	         $filterWrapper.find('.price_slider, .price_label').show();
	         
	         var min_price = $filterWrapper.find('.price_slider_amount #min_price').data('min'),
	            max_price = $filterWrapper.find('.price_slider_amount #max_price').data('max'),
	            current_min_price = $filterWrapper.find('.price_slider_amount #min_price').val(),
	            current_max_price = $filterWrapper.find('.price_slider_amount #max_price').val();
	
	        $filterWrapper.find('.price_slider:not(.ui-slider)').slider({
	            range: true,
	            animate: true,
	            min: min_price,
	            max: max_price,
	            values: [current_min_price, current_max_price],
	            create: function() {
	
	                $filterWrapper.find('.price_slider_amount #min_price').val(current_min_price);
	                $filterWrapper.find('.price_slider_amount #max_price').val(current_max_price);
	
	                self.body.trigger('price_slider_create', [current_min_price, current_max_price]);
	            },
	            slide: function(event, ui) {
	
	                $filterWrapper.find('input#min_price').val(ui.values[0]);
	                $filterWrapper.find('input#max_price').val(ui.values[1]);
	
	                self.body.trigger('price_slider_slide', [ui.values[0], ui.values[1]]);
	            },
	            change: function(event, ui) {
	
	            	self.body.trigger('price_slider_change', [ui.values[0], ui.values[1]]);
	            }
	        });
		},
		initWidgetLayeredNavFilter: function(){
			var self = this;
			var filter_widget_scope = '.toolbar__filter-widgets';
			if('sidebar'===dhwc_ajax_params.filter_widget_display)
				filter_widget_scope = dhwc_ajax_params.elements.sidebar_filter;
			
			self.body.on('click', filter_widget_scope + ' .dhwc_ajax_widget_layered_nav_filters a,.toolbar__filter-active .dhwc_ajax_widget_layered_nav_filters a', function(e) {
                e.stopPropagation();
                e.preventDefault();
                
                var $this = $(this),
                    $url = $this.attr('href');

                self.load($url, true, true);
            });
		},
		initWCWidgetPriceFilter: function(){
			 var self = this,
			 	$filterWrapper = $('.woocommerce.widget_price_filter');
			 
			 if(!$filterWrapper.length)
				 return;
			 
			 $('form',$filterWrapper).on('submit',function(e){
				 e.stopPropagation();
				 e.preventDefault();
				 var $form = $(this),
				 	 $action = $form.attr('action'),
				 	 $url = ( $action += ( rquery.test( $action ) ? "&" : "?" ) + $form.serialize() );
				 
				 self.load($url,true, true)
			 })
			 
		},
		initOrderingForm: function($on_change){
			var self = this,
				$form = $('.woocommerce-ordering');
			
			$on_change = $on_change || false
			
			$form.on('submit',function(e){
				 e.stopPropagation();
				 e.preventDefault();
				 var $form = $(this),
				 	 $action = $form.get(0).action,
				 	 $url = ( $action += ( rquery.test( $action ) ? "&" : "?" ) + $form.serialize() );
				 
				 self.load($url,true, true)
			 });
			
			if(true===$on_change){
				$form.on( 'change', 'select.orderby', function() {
					$( this ).closest( 'form' ).submit();
				});
			}
			
		},
		
		initFilterWidgets: function(){
			var self = this;
			
			if(!self.body.hasClass('dhwc-ajax-archive'))
				return;
			
			self.body.on('click','.filter-widgets__backdrop',function(e){
				e.stopPropagation();
                e.preventDefault();
                self.body.removeClass('dhwc-ajax--open-widget');
                $('.toolbar__filter-button').removeClass('js-active').addClass('js-close');
                 
			});
			
			self.initWidgetLayeredNavFilter();
			self.initWCWidgetPriceFilter();
			
			var filter_widget_scope = '.toolbar__filter-widgets';
			if('sidebar'===dhwc_ajax_params.filter_widget_display)
				filter_widget_scope = dhwc_ajax_params.elements.sidebar_filter;
			
			self.body.on('click',filter_widget_scope + ' .widget_layered_nav a, ' + filter_widget_scope + ' .wc-layered-nav-rating a, ' + filter_widget_scope + ' .dhwcvs-widget-layered-nav a',function(e){
				e.stopPropagation();
                e.preventDefault();
                 
                var $this = $(this),
                	$url = $this.attr('href');
                
                if('#'!==$url)
                	self.load($url, true, true);
			});
			
			self.body.on('click','.dhwc_ajax__overlay',function(e){
				e.stopPropagation();
                e.preventDefault();
                if(self.body.hasClass('dhwc-ajax--open-widget')){
    				self._hideWidgetsFilter();
    				$('.toolbar__filter-button').removeClass('js-active').addClass('js-close');
    			}
			});
			
			self.body.on('click','.widgettitle',function(e){
				if(self.getViewport().width < 768){
					e.stopPropagation();
	                e.preventDefault();
	                $(this).find('.dhwc-ajax-toggle-indicator').trigger('click');
				}
			});
			
			self.body.on('click','.dhwc-ajax-toggle-indicator',function(e){
				
				e.stopPropagation();
                e.preventDefault();
                
				var $this = $(this),
					$widget = $this.closest('.widget'),
					$content = $widget.find('.widgetcontent');

				if($content.is(':visible')){
					$widget.removeClass('js-open').addClass('js-close');
					$content.stop(true).slideUp({
						duration: 500,
						easing: $.bez([0.25, 0.46, 0.45, 0.94])
					});
				}else{
					$widget.removeClass('js-close').addClass('js-open');
					$content.stop(true).slideDown({
						duration: 500,
						easing: $.bez([0.25, 0.46, 0.45, 0.94]),
						complete: function(){
							$widget.removeClass('is-closed');
						}
					});
				}
			});
				
		},
		initPagination: function() {
            var self = this;
            var scroll = function(){
            	var scrollElement = self.loopWrapper;
            	if ( scrollElement.length ) {
        			$( 'html, body' ).animate( {
        				scrollTop: ( scrollElement.offset().top - 100 )
        			}, 1000 );
        		}
            }
            self.body.on('click','.is-ajax-pagination ' + dhwc_ajax_params.elements.pagination_wrapper + ' a', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $this = $(this),
                    $url = $this.attr('href');
                self.load($url, true, true, false, scroll);
            });
        },
		historySupport:function() {
	        return !!(window.history && history.pushState);
	    },
	    isSmoothScrollSupported: function(){
	    	return 'scrollBehavior' in document.documentElement.style;
	    },
	    setPushState: function($url) {
            //Set history state
            if (this.historySupport()) {
                window.history.pushState({
                    dhwc_ajax: true
                }, '', $url);
            }
        },
        scrollTo: function(scrollElement){
        	var scrollElement = $(scrollElement);
        	if(scrollElement.length){
        		$( 'html, body' ).animate( {
    				scrollTop: ( scrollElement.offset().top - 100 )
    			}, 1000 );
        	}
        },
        create_ajax_url: function(action){
        	return dhwc_ajax_params.ajax_url.toString().replace('__action__', action)
        },
        toggleLoading: function(){
        	var loopWrapper = $(dhwc_ajax_params.elements.product_wrapper);
        	if( this.isAjaxLoading)
        		loopWrapper.addClass('dhwc-ajax-loading')
        	else
        		loopWrapper.removeClass('dhwc-ajax-loading')
        },
        replaceContent: function($response){
        	var self = this;
        	
        	self.body.trigger('dhwc_ajax_before_replace_content',[$response]);
        	
        	document.title = $response.find('title').text()

        	$('.filter-widgets').replaceWith($response.find('.filter-widgets'));
        	
        	$(dhwc_ajax_params.elements.sidebar_filter).replaceWith($response.find(dhwc_ajax_params.elements.sidebar_filter));
        	
        	$('.toolbar-wc').replaceWith($response.find('.toolbar-wc'));
        	
        	self.initOrderingForm(true);
        	
        	$(dhwc_ajax_params.elements.page_title).replaceWith($response.find(dhwc_ajax_params.elements.page_title));
        	$(dhwc_ajax_params.elements.page_breadcrumb).replaceWith($response.find(dhwc_ajax_params.elements.page_breadcrumb));
        	
        	$('.categories__list').replaceWith($response.find('.categories__list'));
        	$('.toolbar__category-active').replaceWith($response.find('.toolbar__category-active'));
        	$('.woocommerce-products-header').replaceWith($response.find('.woocommerce-products-header'));
        	$('.heading__title').replaceWith($response.find('.heading__title'));
        	//init WC Price Widget again
        	self.initWCWidgetPrice();
        	self.initWCWidgetPriceFilter();
        	
        	//init jQuery slider
        	setTimeout(function(){
    			self.initWCWidgetPrice();
            	self.initWCWidgetPriceFilter();
    			if('function' === typeof $.fn.dhwc_ajax_slider_filter)
    				$('.dhwc-ajax__slider-filter').dhwc_ajax_slider_filter();
    		},500);

        	//active filter
        	$('.toolbar__filter-active').replaceWith($response.find('.toolbar__filter-active'));
        	
        	//content
        	$(dhwc_ajax_params.elements.product_wrapper).replaceWith($response.find(dhwc_ajax_params.elements.product_wrapper));
        	
        	//pagination
        	var $pagination_el = '.dhwc-ajax__pagination';
            if ($response.find($pagination_el).length) {
                $($pagination_el).show();
                $($pagination_el).replaceWith($response.find($pagination_el).first());
            } else {
               $($pagination_el).empty().hide();
            }

        	self.initInfiniteScroll();
        	
        	self.body.trigger('dhwc_ajax_after_replace_content',[$response]);
        },
        load: function($url, $pushstate, $scroll, $callback, $onBeforeLoad){
        	var self = this;

        	if('undefined' === typeof $url || self.isAjaxLoading)
        		return;
        	
        	$url = $url && $url.replace(/\/?(\?|#|$)/, '/$1');
        	
        	if ($pushstate)
                 self.setPushState($url);
        	
        	self.isAjaxLoading = true;
        	
        	$.ajax({
                url: $url,
                headers: {"dhwcajax" : 'yes'},
                dataType: 'html',
                method: 'GET',
                beforeSend: function() {
                    self.toggleLoading();
                    if($('.toolbar__filter-category').hasClass('js-active')){
                    	$('.toolbar__filter-category').trigger('click')
                    }
                    self.body.trigger('dhwc_ajax_before_load');
                    $onBeforeLoad && $onBeforeLoad();
                },
                error: function(XMLHttpRequest, status, error) {
                    self.isAjaxLoading = false;
                    self.toggleLoading();
                },
                success: function(response) {
                    //Fix ERROR: $(html) HTML strings must start with '<' character
                    response = $('<div>' + response + '</div>');
                    self.replaceContent(response);
                    self.isAjaxLoading = false;
                    self.toggleLoading();
                    $callback && $callback();
                    self.body.trigger('dhwc_ajax_after_load');
                }
            })
        },
        getViewport: function() {
            var e = window,
                a = 'inner';
            if (!('innerWidth' in window)) {
                a = 'client';
                e = document.documentElement || document.body;
            }
            return {
                width: e[a + 'Width'],
                height: e[a + 'Height']
            };
        }
	}
	

	$.dhwc_ajax = new DHWC_Ajax();
	
	$(document).ready(function(){
		$.dhwc_ajax.init();
	})
	
})(jQuery);


