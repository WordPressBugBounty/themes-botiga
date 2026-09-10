"use strict";

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * Botiga Setup Checklist admin interactions.
 *
 * @since 2.4.9
 */
var BotigaSetupChecklistNotifications = /*#__PURE__*/function () {
  /**
   * Creates the notification drawer controller.
   *
   * @param {HTMLElement|null} element Checklist root element.
   * @param {Object} config Checklist script configuration.
   */
  function BotigaSetupChecklistNotifications(element, config) {
    _classCallCheck(this, BotigaSetupChecklistNotifications);
    this.config = config || {};
    this.toggle = element ? element.querySelector('[data-botiga-setup-checklist-notifications-toggle]') : null;
    this.drawer = document.getElementById('botiga-setup-checklist-notifications');
    this.closeButton = this.drawer ? this.drawer.querySelector('[data-botiga-setup-checklist-notifications-close]') : null;
    this.tabs = this.drawer ? this.drawer.querySelectorAll('[data-botiga-setup-checklist-notification-tab]') : [];
    this.panels = this.drawer ? this.drawer.querySelectorAll('[data-botiga-setup-checklist-notification-panel]') : [];
    this.closeTimer = null;
  }

  /**
   * Initializes notification drawer interactions.
   *
   * @return {void}
   */
  return _createClass(BotigaSetupChecklistNotifications, [{
    key: "init",
    value: function init() {
      var _this = this;
      if (!this.toggle || !this.drawer) {
        return;
      }
      this.toggle.addEventListener('click', function () {
        if (_this.drawer.classList.contains('is-open')) {
          _this.close();
          return;
        }
        _this.open();
      });
      if (this.closeButton) {
        this.closeButton.addEventListener('click', function () {
          _this.close();
        });
      }
      this.tabs.forEach(function (tab) {
        tab.addEventListener('click', function (event) {
          _this.selectTab(event);
        });
      });
      document.addEventListener('keydown', function (event) {
        if ('Escape' !== event.key) {
          return;
        }
        _this.close();
      });
      window.addEventListener('scroll', function () {
        if (window.pageYOffset <= 60 || !_this.drawer.classList.contains('is-open')) {
          return;
        }
        _this.close();
      });
    }

    /**
     * Opens the notification drawer.
     *
     * @return {void}
     */
  }, {
    key: "open",
    value: function open() {
      if (!this.toggle || !this.drawer) {
        return;
      }
      if (this.closeTimer) {
        window.clearTimeout(this.closeTimer);
        this.closeTimer = null;
      }
      this.drawer.classList.remove('is-closing');
      this.drawer.classList.add('is-open');
      this.drawer.setAttribute('aria-hidden', 'false');
      this.toggle.setAttribute('aria-expanded', 'true');
      this.markRead();
    }

    /**
     * Closes the notification drawer.
     *
     * @return {void}
     */
  }, {
    key: "close",
    value: function close() {
      var _this2 = this;
      if (!this.toggle || !this.drawer || !this.drawer.classList.contains('is-open')) {
        return;
      }
      this.drawer.classList.add('is-closing');
      this.toggle.setAttribute('aria-expanded', 'false');
      this.closeTimer = window.setTimeout(function () {
        _this2.drawer.classList.remove('is-open', 'is-closing');
        _this2.drawer.setAttribute('aria-hidden', 'true');
        _this2.closeTimer = null;
      }, 300);
    }

    /**
     * Selects a notification feed tab.
     *
     * @param {Event} event Click event.
     *
     * @return {void}
     */
  }, {
    key: "selectTab",
    value: function selectTab(event) {
      var selectedTab = event.currentTarget;
      var selectedFeed = selectedTab.dataset.botigaSetupChecklistNotificationTab;
      if (!selectedFeed) {
        return;
      }
      this.tabs.forEach(function (tab) {
        var isSelected = tab === selectedTab;
        tab.classList.toggle('is-active', isSelected);
        tab.setAttribute('aria-selected', isSelected ? 'true' : 'false');
      });
      this.panels.forEach(function (panel) {
        var isSelected = panel.dataset.botigaSetupChecklistNotificationPanel === selectedFeed;
        panel.classList.toggle('is-active', isSelected);
        panel.hidden = !isSelected;
      });
    }

    /**
     * Marks the latest notification as read.
     *
     * @return {void}
     */
  }, {
    key: "markRead",
    value: function markRead() {
      var _this3 = this;
      if (!this.toggle || this.toggle.classList.contains('is-read')) {
        return;
      }
      var notifications = this.config.notifications;
      var latestDate = this.drawer.dataset.latestNotificationDate;
      if (!this.config.ajaxUrl || !notifications || !latestDate) {
        return;
      }
      var body = new URLSearchParams({
        action: notifications.action,
        nonce: notifications.nonce,
        latest_notification_date: latestDate
      });
      fetch(this.config.ajaxUrl, {
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
          return;
        }
        _this3.setReadState();
      }).catch(function () {
        // Keep the notification unread when the request fails.
      });
    }

    /**
     * Applies the read state to the notification UI.
     *
     * @return {void}
     */
  }, {
    key: "setReadState",
    value: function setReadState() {
      this.toggle.classList.add('is-read');
      var count = this.toggle.querySelector('.botiga-setup-checklist__notification-count');
      if (count) {
        count.remove();
      }
      var heading = this.drawer.querySelector('h2');
      var readHeading = this.drawer.dataset.readHeading;
      if (heading && readHeading) {
        heading.textContent = readHeading;
      }
    }
  }]);
}();
var BotigaSetupChecklist = /*#__PURE__*/function () {
  /**
   * Creates the Setup Checklist controller.
   *
   * @param {HTMLElement|null} element Checklist root element.
   */
  function BotigaSetupChecklist(element) {
    _classCallCheck(this, BotigaSetupChecklist);
    this.element = element;
    this.sectionToggles = element ? element.querySelectorAll('.botiga-setup-checklist__section-toggle') : [];
    this.promotionToggles = element ? element.querySelectorAll('.botiga-setup-checklist__promotion-toggle') : [];
    this.notifications = new BotigaSetupChecklistNotifications(element, window.botigaSetupChecklist || {});
  }

  /**
   * Initializes Setup Checklist interactions.
   *
   * @return {void}
   */
  return _createClass(BotigaSetupChecklist, [{
    key: "init",
    value: function init() {
      if (!this.element) {
        return;
      }
      this.restoreSectionStates();
      this.notifications.init();
      this.bindSectionToggles();
      this.bindPromotionToggles();
    }

    /**
     * Binds actionable section toggles.
     *
     * @return {void}
     */
  }, {
    key: "bindSectionToggles",
    value: function bindSectionToggles() {
      var _this4 = this;
      this.sectionToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function (event) {
          _this4.handleSectionToggle(event);
        });
      });
    }

    /**
     * Binds promotional section toggles.
     *
     * @return {void}
     */
  }, {
    key: "bindPromotionToggles",
    value: function bindPromotionToggles() {
      var _this5 = this;
      this.promotionToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function (event) {
          _this5.handlePromotionToggle(event);
        });
      });
    }

    /**
     * Restores manually changed section states for the current session.
     *
     * @return {void}
     */
  }, {
    key: "restoreSectionStates",
    value: function restoreSectionStates() {
      var _this6 = this;
      this.sectionToggles.forEach(function (toggle) {
        var section = toggle.closest('.botiga-setup-checklist__section');
        if (!section) {
          return;
        }
        var state = _this6.getStoredSectionState(section);
        if (!state) {
          return;
        }
        var isComplete = section.classList.contains('is-complete');
        if (state.isComplete !== isComplete) {
          _this6.removeStoredSectionState(section);
          return;
        }
        _this6.applyToggleState(toggle, section, state.isCollapsed);
      });
    }

    /**
     * Toggles one checklist section.
     *
     * @param {Event} event Click event.
     *
     * @return {void}
     */
  }, {
    key: "handleSectionToggle",
    value: function handleSectionToggle(event) {
      var toggle = event.currentTarget;
      var section = toggle.closest('.botiga-setup-checklist__section');
      if (!section) {
        return;
      }
      var isCollapsed = !section.classList.contains('is-collapsed');
      this.applyToggleState(toggle, section, isCollapsed);
      this.storeSectionState(section, isCollapsed);
    }

    /**
     * Toggles one promotional section.
     *
     * @param {Event} event Click event.
     *
     * @return {void}
     */
  }, {
    key: "handlePromotionToggle",
    value: function handlePromotionToggle(event) {
      var toggle = event.currentTarget;
      var section = toggle.closest('.botiga-setup-checklist__promotion');
      if (!section) {
        return;
      }
      var isCollapsed = !section.classList.contains('is-collapsed');
      this.applyToggleState(toggle, section, isCollapsed);
    }

    /**
     * Applies the expanded or collapsed state to a section.
     *
     * @param {HTMLElement} toggle Section toggle.
     * @param {HTMLElement} section Section element.
     * @param {boolean} isCollapsed Whether the section is collapsed.
     *
     * @return {void}
     */
  }, {
    key: "applyToggleState",
    value: function applyToggleState(toggle, section, isCollapsed) {
      toggle.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
      section.classList.toggle('is-collapsed', isCollapsed);
    }

    /**
     * Gets the body controlled by a toggle.
     *
     * @param {HTMLElement} toggle Section toggle.
     *
     * @return {HTMLElement|null}
     */
  }, {
    key: "getControlledBody",
    value: function getControlledBody(toggle) {
      var bodyId = toggle.getAttribute('aria-controls');
      if (!bodyId) {
        return null;
      }
      return document.getElementById(bodyId);
    }

    /**
     * Stores a manual section state in session storage.
     *
     * @param {HTMLElement} section Section element.
     * @param {boolean} isCollapsed Whether the section is collapsed.
     *
     * @return {void}
     */
  }, {
    key: "storeSectionState",
    value: function storeSectionState(section, isCollapsed) {
      var key = this.getSectionStorageKey(section);
      if (!key) {
        return;
      }
      var state = {
        isCollapsed: isCollapsed,
        isComplete: section.classList.contains('is-complete')
      };
      try {
        sessionStorage.setItem(key, JSON.stringify(state));
      } catch (error) {
        // Ignore unavailable session storage.
      }
    }

    /**
     * Gets a stored section state.
     *
     * @param {HTMLElement} section Section element.
     *
     * @return {{isCollapsed: boolean, isComplete: boolean}|null}
     */
  }, {
    key: "getStoredSectionState",
    value: function getStoredSectionState(section) {
      var key = this.getSectionStorageKey(section);
      if (!key) {
        return null;
      }
      try {
        var state = JSON.parse(sessionStorage.getItem(key));
        if (!state || typeof state.isCollapsed !== 'boolean' || typeof state.isComplete !== 'boolean') {
          return null;
        }
        return state;
      } catch (error) {
        return null;
      }
    }

    /**
     * Removes a stale stored section state.
     *
     * @param {HTMLElement} section Section element.
     *
     * @return {void}
     */
  }, {
    key: "removeStoredSectionState",
    value: function removeStoredSectionState(section) {
      var key = this.getSectionStorageKey(section);
      if (!key) {
        return;
      }
      try {
        sessionStorage.removeItem(key);
      } catch (error) {
        // Ignore unavailable session storage.
      }
    }

    /**
     * Gets the session storage key for a section.
     *
     * @param {HTMLElement} section Section element.
     *
     * @return {string}
     */
  }, {
    key: "getSectionStorageKey",
    value: function getSectionStorageKey(section) {
      var sectionId = section.dataset.section;
      if (!sectionId) {
        return '';
      }
      return "botigaSetupChecklist:".concat(sectionId);
    }
  }]);
}();
document.addEventListener('DOMContentLoaded', function () {
  var checklist = document.querySelector('.botiga-setup-checklist');
  if (!checklist) {
    return;
  }
  new BotigaSetupChecklist(checklist).init();
});