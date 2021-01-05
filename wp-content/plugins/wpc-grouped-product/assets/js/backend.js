'use strict';

(function($) {
  var woosg_timeout = null;

  $(document).ready(function() {
    // options page
    woosg_active_options();

    woosg_active_settings();

    // arrange
    woosg_arrange();
  });

  $(document).on('change', '#product-type', function() {
    woosg_active_settings();
  });

  $(document).on('change', 'select[name="_woosg_change_price"]', function() {
    woosg_active_options();
  });

  // search input
  $(document).on('keyup', '#woosg_keyword', function() {
    if ($('#woosg_keyword').val() != '') {
      $('#woosg_loading').show();
      if (woosg_timeout != null) {
        clearTimeout(woosg_timeout);
      }
      woosg_timeout = setTimeout(woosg_ajax_get_data, 300);
      return false;
    }
  });

  // actions on search result items
  $(document).on('click touch', '#woosg_results li', function() {
    $(this).children('span.remove').attr('aria-label', 'Remove').html('×');
    $('#woosg_selected ul').append($(this));
    $('#woosg_results').hide();
    $('#woosg_keyword').val('');
    woosg_get_ids();
    woosg_arrange();
    return false;
  });

  // change qty of each item
  $(document).on('keyup change', '#woosg_selected .qty input', function() {
    woosg_get_ids();
    return false;
  });

  // actions on selected items
  $(document).on('click touch', '#woosg_selected span.remove', function() {
    $(this).parent().remove();
    woosg_get_ids();
    return false;
  });

  // hide search result box if click outside
  $(document).on('click touch', function(e) {
    if ($(e.target).closest($('#woosg_results')).length == 0) {
      $('#woosg_results').hide();
    }
  });

  $(document).on('woosg_drag_event', function() {
    woosg_get_ids();
  });

  function woosg_arrange() {
    $('#woosg_selected li').arrangeable({
      dragEndEvent: 'woosg_drag_event',
      dragSelector: '.move',
    });
  }

  function woosg_get_ids() {
    var listId = new Array();

    $('#woosg_selected li').each(function() {
      listId.push($(this).data('id') + '/' + $(this).find('input').val());
    });
    if (listId.length > 0) {
      $('#woosg_ids').val(listId.join(','));
    } else {
      $('#woosg_ids').val('');
    }
  }

  function woosg_active_options() {
    if ($('select[name="_woosg_change_price"]').val() == 'yes_custom') {
      $('input[name="_woosg_change_price_custom"]').show();
    } else {
      $('input[name="_woosg_change_price_custom"]').hide();
    }
  }

  function woosg_active_settings() {
    if ($('#product-type').val() == 'woosg') {
      $('li.general_tab').addClass('show_if_woosg');
      $('#general_product_data .pricing').addClass('show_if_woosg');

      $('.show_if_external').hide();
      $('.show_if_simple').show();
      $('.show_if_woosg').show();

      $('.product_data_tabs li').removeClass('active');
      $('.product_data_tabs li.woosg_tab').addClass('active');

      $('.panel-wrap .panel').hide();
      $('#woosg_settings').show();

      if ($('#woosg_optional_products').is(':checked')) {
        $('.woosg_tr_show_if_optional_products').show();
      } else {
        $('.woosg_tr_show_if_optional_products').hide();
      }

      if ($('#woosg_disable_auto_price').is(':checked')) {
        $('.woosg_tr_show_if_auto_price').hide();
      } else {
        $('.woosg_tr_show_if_auto_price').show();
      }
    } else {
      $('li.general_tab').removeClass('show_if_woosg');
      $('#general_product_data .pricing').removeClass('show_if_woosg');

      $('#_regular_price').prop('readonly', false);
      $('#_sale_price').prop('readonly', false);

      if ($('#product-type').val() != 'grouped') {
        $('.general_tab').show();
      }

      if ($('#product-type').val() == 'simple') {
        $('#_downloadable').closest('label').show();
        $('#_virtual').closest('label').show();
      }
    }
  }

  function woosg_ajax_get_data() {
    // ajax search product
    woosg_timeout = null;
    var data = {
      action: 'woosg_get_search_results',
      keyword: $('#woosg_keyword').val(),
      ids: $('#woosg_ids').val(),
    };

    jQuery.post(ajaxurl, data, function(response) {
      $('#woosg_results').show();
      $('#woosg_results').html(response);
      $('#woosg_loading').hide();
    });
  }
})(jQuery);