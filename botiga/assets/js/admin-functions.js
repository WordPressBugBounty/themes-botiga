"use strict";

/**
 * Header/Footer Update.
 */
(function ($) {
  'use strict';

  $(document).on('click', '.botiga-update-hf', function (e) {
    e.preventDefault();
    if (confirm(botigaadm.hfUpdate.confirmMessage)) {
      $.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          action: 'botiga_hf_update_notice_1_1_9_callback',
          nonce: $(this).data('nonce')
        },
        success: function success(response) {
          if (response.success) {
            window.location.reload();
          } else {
            alert(botigaadm.hfUpdate.errorMessage);
          }
        }
      });
    }
  });
})(jQuery);

/**
 * Header/Footer Update Dismiss.
 */
(function ($) {
  'use strict';

  $(document).on('click', '.botiga-update-hf-dismiss', function (e) {
    e.preventDefault();
    if (confirm(botigaadm.hfUpdateDimiss.confirmMessage)) {
      $.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          action: 'botiga_hf_update_dismiss_notice_1_1_9_callback',
          nonce: $(this).data('nonce')
        },
        success: function success(response) {
          if (response.success) {
            window.location.reload();
          } else {
            alert(botigaadm.hfUpdateDimiss.errorMessage);
          }
        }
      });
    }
  });
})(jQuery);

/**
 * Patcher end-of-life notice dismissal.
 *
 * @since 2.4.9
 */
(function () {
  'use strict';

  document.addEventListener('click', function (event) {
    if (!(event.target instanceof Element)) {
      return;
    }
    var dismissButton = event.target.closest('[data-botiga-patcher-eol-dismiss]');
    var config = window.botigaPatcherEol;
    if (!dismissButton || !config) {
      return;
    }
    event.preventDefault();
    var notice = dismissButton.closest('[data-botiga-patcher-eol-notice]');
    if (!notice || dismissButton.disabled) {
      return;
    }
    dismissButton.disabled = true;
    var body = new URLSearchParams({
      action: config.action,
      nonce: config.nonce
    });
    fetch(config.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: body.toString()
    }).then(function (response) {
      return response.json();
    }).then(function (response) {
      if (!response.success) {
        dismissButton.disabled = false;
        return;
      }
      notice.remove();
    }).catch(function () {
      dismissButton.disabled = false;
    });
  });
})();