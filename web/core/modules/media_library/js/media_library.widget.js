/**
 * DO NOT EDIT THIS FILE.
 * See the following change record for more information,
 * https://www.drupal.org/node/2815083
 * @preserve
 **/

(function ($, Drupal, Sortable) {
  Drupal.behaviors.MediaLibraryWidgetSortable = {
    attach: function attach(context) {
      var selection = context.querySelectorAll('.js-media-library-selection');
      selection.forEach(function (widget) {
        Sortable.create(widget, {
          draggable: '.js-media-library-item',
          handle: '.js-media-library-item-preview',
          onEnd: function onEnd() {
            $(widget).children().each(function (index, child) {
              $(child).find('.js-media-library-item-weight').val(index);
            });
          }
        });
      });
    }
  };
  Drupal.behaviors.MediaLibraryWidgetToggleWeight = {
    attach: function attach(context) {
      var strings = {
        show: Drupal.t('Show media item weights'),
        hide: Drupal.t('Hide media item weights')
      };
      $(once('media-library-toggle', '.js-media-library-widget-toggle-weight', context)).on('click', function (e) {
        e.preventDefault();
        $(e.currentTarget).toggleClass('active').text($(e.currentTarget).hasClass('active') ? strings.hide : strings.show).closest('.js-media-library-widget').find('.js-media-library-item-weight').parent().toggle();
      }).text(strings.show);
      $(once('media-library-toggle', '.js-media-library-item-weight', context)).parent().hide();
    }
  };
  Drupal.behaviors.MediaLibraryWidgetDisableButton = {
    attach: function attach(context) {
      once('media-library-disable', '.js-media-library-open-button[data-disabled-focus="true"]', context).forEach(function (button) {
        $(button).focus();
        setTimeout(function () {
          $(button).attr('disabled', 'disabled');
        }, 50);
      });
    }
  };
})(jQuery, Drupal, Sortable);
