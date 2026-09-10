"use strict";

/**
 * Botiga Setup Checklist Customizer interactions.
 *
 * @since 2.4.9
 */
(function ($) {
  'use strict';

  var isStyleGuideOpen = function isStyleGuideOpen() {
    return $('.botiga-style-guide').is(':visible');
  };
  var scheduleOpenStyleGuide = function scheduleOpenStyleGuide() {
    var attempts = 0;
    var _tryOpenStyleGuide = function tryOpenStyleGuide() {
      if (isStyleGuideOpen()) {
        return;
      }
      var styleGuideButton = $('.botiga-style-guide-toggle-button');
      if (styleGuideButton.length) {
        styleGuideButton.trigger('click');
      }
      attempts++;
      if (attempts < 20) {
        window.setTimeout(_tryOpenStyleGuide, 100);
      }
    };
    _tryOpenStyleGuide();
  };
  $(document).ready(scheduleOpenStyleGuide);
})(jQuery);