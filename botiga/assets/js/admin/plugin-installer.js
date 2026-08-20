"use strict";

(function ($) {
  'use strict';

  var botigaPluginInstaller = window.botigaPluginInstaller || {};
  botigaPluginInstaller = {
    installButtonSelector: '.botiga-install-plugin',
    init: function init() {
      this.events();
    },
    events: function events() {
      var self = this;
      $(document).on('click', self.installButtonSelector, function (e) {
        e.preventDefault();
        var type = $(this).data('type') === 'external' ? 'external' : 'wporg';
        var plugin_name = $(this).data('plugin-name');
        var redirect_to = $(this).data('redirect-to');
        if (type === 'external') {
          var url = $(this).data('plugin-url');
          self.installExternalPlugin($(this), url, plugin_name, redirect_to);
        } else {
          var slug = $(this).data('plugin-slug');
          self.installPlugin($(this), slug, plugin_name, redirect_to);
        }
      });
    },
    installPlugin: function installPlugin(button, slug, plugin_name, redirect_to) {
      var data = {
        action: 'botiga_install_plugin',
        slug: slug,
        plugin_name: plugin_name,
        nonce: botigaPluginInstallerConfig.nonce
      };
      this.install(button, data, redirect_to);
    },
    installExternalPlugin: function installExternalPlugin(button, url, plugin_name, redirect_to) {
      var data = {
        action: 'botiga_install_external_plugin',
        url: url,
        plugin_name: plugin_name,
        nonce: botigaPluginInstallerConfig.nonce
      };
      this.install(button, data, redirect_to);
    },
    install: function install(button, data, redirect_to) {
      var default_text = button.text().trim();
      button.prop('disabled', true);
      button.text(botigaPluginInstallerConfig.i18n.installingText);
      $.post(botigaPluginInstallerConfig.ajax_url, data, function (response) {
        if (!response.success) {
          button.prop('disabled', false);
          button.text(default_text);
          alert(response.data.message);
          return;
        }
        button.text(botigaPluginInstallerConfig.i18n.activatingText);
        setTimeout(function () {
          if (redirect_to) {
            window.location.href = redirect_to;
            return;
          }
          window.location.reload();
        }, 1000);
      }).fail(function () {
        button.prop('disabled', false);
        button.text(default_text);
        alert(botigaPluginInstallerConfig.i18n.networkErrorText);
      });
    }
  };
  $(document).ready(function () {
    botigaPluginInstaller.init();
  });
})(jQuery);