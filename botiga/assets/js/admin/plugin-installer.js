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
        var pluginName = $(this).data('plugin-name');
        var pluginAction = $(this).data('plugin-action') === 'activate' ? 'activate' : 'install';
        var redirectTo = $(this).data('redirect-to');
        if (type === 'external') {
          var url = $(this).data('plugin-url');
          self.installExternalPlugin($(this), url, pluginName, redirectTo, pluginAction);
        } else {
          var slug = $(this).data('plugin-slug');
          self.installPlugin($(this), slug, pluginName, redirectTo, pluginAction);
        }
      });
    },
    installPlugin: function installPlugin(button, slug, pluginName, redirectTo, pluginAction) {
      var data = {
        action: 'botiga_install_plugin',
        slug: slug,
        plugin_name: pluginName,
        nonce: botigaPluginInstallerConfig.nonce
      };
      this.install(button, data, redirectTo, pluginAction);
    },
    installExternalPlugin: function installExternalPlugin(button, url, pluginName, redirectTo, pluginAction) {
      var data = {
        action: 'botiga_install_external_plugin',
        url: url,
        plugin_name: pluginName,
        nonce: botigaPluginInstallerConfig.nonce
      };
      this.install(button, data, redirectTo, pluginAction);
    },
    install: function install(button, data, redirectTo, pluginAction) {
      var defaultText = button.text().trim();
      var progressText = pluginAction === 'activate' ? botigaPluginInstallerConfig.i18n.activatingText : botigaPluginInstallerConfig.i18n.installingText;
      button.prop('disabled', true);
      button.text(progressText);
      $.post(botigaPluginInstallerConfig.ajax_url, data, function (response) {
        if (!response.success) {
          button.prop('disabled', false);
          button.text(defaultText);
          alert(response.data.message);
          return;
        }
        button.text(botigaPluginInstallerConfig.i18n.activatingText);
        setTimeout(function () {
          if (redirectTo) {
            window.location.href = redirectTo;
            return;
          }
          window.location.reload();
        }, 1000);
      }).fail(function () {
        button.prop('disabled', false);
        button.text(defaultText);
        alert(botigaPluginInstallerConfig.i18n.networkErrorText);
      });
    }
  };
  $(document).ready(function () {
    botigaPluginInstaller.init();
  });
})(jQuery);