"use strict";

function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t.return || t.return(); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
var botiga = botiga || {};

/**
 * Is the DOM ready?
 *
 * This implementation is coming from https://gomakethings.com/a-native-javascript-equivalent-of-jquerys-ready-method/
 *
 * @param {Function} fn Callback function to run.
 */
botiga.helpers = {
  botigaDomReady: function botigaDomReady(fn) {
    if (typeof fn !== 'function') {
      return;
    }
    if (document.readyState === 'interactive' || document.readyState === 'complete') {
      return fn();
    }
    document.addEventListener('DOMContentLoaded', fn, false);
  },
  isInVerticalViewport: function isInVerticalViewport(el) {
    var rect = el.getBoundingClientRect();
    return rect.top >= 0 && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight);
  },
  isInHorizontalViewport: function isInHorizontalViewport(el) {
    var rect = el.getBoundingClientRect();
    return rect.left >= 0 && rect.right <= document.documentElement.clientWidth;
  },
  ajax: function ajax(action, nonce, extraParams, successCallback) {
    var ajax = new XMLHttpRequest();
    ajax.open('POST', botiga.ajaxurl, true);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.onload = function () {
      if (this.status >= 200 && this.status < 400) {
        successCallback.apply(this);
      }
    };
    var extraParamsStr = '';
    extraParams = Object.entries(extraParams);
    for (var i = 0; i < extraParams.length; i++) {
      extraParamsStr += '&' + extraParams[i].join('=');
    }
    ajax.send('action=' + action + '&nonce=' + nonce + extraParamsStr);
  },
  setCookie: function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  },
  getCookie: function getCookie(cname) {
    var name = cname + "=",
      ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }
};

/**
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
botiga.navigation = {
  init: function init() {
    var self = this,
      siteNavigation = document.getElementById('site-navigation') == null ? document.getElementById('site-navigation-mobile') : document.getElementById('site-navigation'),
      offCanvas = document.getElementsByClassName('botiga-offcanvas-menu')[0],
      button = document.getElementsByClassName('menu-toggle')[0];
    if (siteNavigation === null) {
      return;
    }

    // Return early if the navigation don't exist.
    if (!siteNavigation && typeof button === 'undefined') {
      return;
    }
    if (typeof offCanvas === 'undefined') {
      return;
    }
    var closeButton = document.getElementsByClassName('mobile-menu-close')[0];

    // Return early if the button don't exist.
    if ('undefined' === typeof button) {
      return;
    }
    var menu = siteNavigation.getElementsByTagName('ul')[0];
    var mobileMenuClose = siteNavigation.getElementsByClassName('mobile-menu-close')[0];

    // Hide menu toggle button if menu is empty and return early.
    if ('undefined' === typeof menu) {
      button.style.display = 'none';
      return;
    }
    if (!menu.classList.contains('nav-menu')) {
      menu.classList.add('nav-menu');
    }
    var focusableEls = offCanvas.querySelectorAll('a[href]:not([disabled]):not(.mobile-menu-close)');
    var firstFocusableEl = focusableEls[0];
    button.addEventListener('click', function (e) {
      e.preventDefault();
      button.classList.add('open');
      offCanvas.classList.add('toggled');
      document.body.classList.add('mobile-menu-visible');

      // Toggle submenus
      var submenuToggles = offCanvas.querySelectorAll('.dropdown-symbol, .menu-item-has-children > a[href="#"]');
      var _iterator = _createForOfIteratorHelper(submenuToggles),
        _step;
      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var submenuToggle = _step.value;
          submenuToggle.addEventListener('touchstart', submenuToggleHandler);
          submenuToggle.addEventListener('click', submenuToggleHandler);
          submenuToggle.addEventListener('keydown', function (e) {
            var isTabPressed = e.key === 'Enter' || e.keyCode === 13;
            if (!isTabPressed) {
              return;
            }
            e.preventDefault();
            var parent = submenuToggle.parentNode.parentNode;
            parent.getElementsByClassName('sub-menu')[0].classList.toggle('toggled');
          });
        }

        //Trap focus inside modal
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
      firstFocusableEl.focus();
    });
    function submenuToggleHandler(e) {
      if (e.cancelable) {
        e.preventDefault();
      }
      var parent = e.target.closest('li');
      if (parent.querySelector('.sub-menu').classList.contains('toggling')) {
        return false;
      }
      parent.querySelector('.sub-menu').classList.toggle('toggling');
      parent.querySelector('.sub-menu').classList.toggle('toggled');
      setTimeout(function () {
        parent.querySelector('.sub-menu').classList.remove('toggling');
      }, 300);
    }

    // Close the offcanvas when a anchor that contains a hash is clicked
    var anchors = offCanvas.querySelectorAll('a[href*="#"]');
    if (anchors.length) {
      var _iterator2 = _createForOfIteratorHelper(anchors),
        _step2;
      try {
        for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
          var anchor = _step2.value;
          anchor.addEventListener('click', function (e) {
            if (e.target.hash && document.querySelector(e.target.hash) !== null && !e.target.classList.contains('botiga-tabs-nav-link')) {
              button.classList.remove('open');
              offCanvas.classList.remove('toggled');
              document.body.classList.remove('mobile-menu-visible');
            }
          });
        }
      } catch (err) {
        _iterator2.e(err);
      } finally {
        _iterator2.f();
      }
    }
    var focusableEls = offCanvas.querySelectorAll('a[href]:not([disabled])');
    var firstFocusableEl = focusableEls[0];
    var lastFocusableEl = focusableEls[focusableEls.length - 1];
    var KEYCODE_TAB = 9;
    lastFocusableEl.addEventListener('keydown', function (e) {
      var isTabPressed = e.key === 'Tab' || e.keyCode === KEYCODE_TAB;
      if (!isTabPressed) {
        return;
      }
      if (e.shiftKey) /* shift + tab */{} else /* tab */{
          firstFocusableEl.focus();
        }
    });
    closeButton.addEventListener('click', function (e) {
      e.preventDefault();
      var buttonRect = button.getBoundingClientRect();
      if (buttonRect.top + buttonRect.height > 0) {
        button.focus();
      }
      button.classList.remove('open');
      offCanvas.classList.remove('toggled');
      document.body.classList.remove('mobile-menu-visible');
    });
    document.addEventListener('click', function (e) {
      if (e.target.closest('.botiga-offcanvas-menu') === null && !e.target.classList.contains('menu-toggle') && e.target.closest('.menu-toggle') === null) {
        button.classList.remove('open');
        offCanvas.classList.remove('toggled');
        document.body.classList.remove('mobile-menu-visible');
      }
    });

    // Get all the link elements within the menu.
    var links = menu.getElementsByTagName('a');

    // Get all the link elements with children within the menu.
    var linksWithChildren = menu.querySelectorAll('.menu-item-has-children > a, .page_item_has_children > a');

    // Toggle focus each time a menu link is focused or blurred.
    var _iterator3 = _createForOfIteratorHelper(links),
      _step3;
    try {
      for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
        var link = _step3.value;
        link.addEventListener('focus', toggleFocus, true);
        link.addEventListener('blur', toggleFocus, true);
      }

      // Toggle focus each time a menu link with children receive a touch event.
    } catch (err) {
      _iterator3.e(err);
    } finally {
      _iterator3.f();
    }
    var _iterator4 = _createForOfIteratorHelper(linksWithChildren),
      _step4;
    try {
      for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
        var _link = _step4.value;
        _link.addEventListener('touchstart', toggleFocus, false);
      }

      /**
       * Sets or removes .focus class on an element.
       */
    } catch (err) {
      _iterator4.e(err);
    } finally {
      _iterator4.f();
    }
    function toggleFocus() {
      if (event.type === 'focus' || event.type === 'blur') {
        var _self2 = this;
        // Move up through the ancestors of the current link until we hit .nav-menu.
        while (!_self2.classList.contains('nav-menu')) {
          // On li elements toggle the class .focus.
          if ('li' === _self2.tagName.toLowerCase()) {
            _self2.classList.toggle('focus');
          }
          _self2 = _self2.parentNode;
        }
      }
      if (event.type === 'touchstart') {
        var menuItem = this.parentNode;
        event.preventDefault();
        var _iterator5 = _createForOfIteratorHelper(menuItem.parentNode.children),
          _step5;
        try {
          for (_iterator5.s(); !(_step5 = _iterator5.n()).done;) {
            var link = _step5.value;
            if (menuItem !== link) {
              link.classList.remove('focus');
            }
          }
        } catch (err) {
          _iterator5.e(err);
        } finally {
          _iterator5.f();
        }
        menuItem.classList.toggle('focus');
      }
    }

    // Mobile accordion style navigation
    this.mobileAccordionNavigation();

    // Hover with delay effect
    this.initHoverClass();

    // Menu reverse
    this.checkMenuReverse();
  },
  /**
   * Initialize hover class for dropdown items.
   */
  initHoverClass: function initHoverClass() {
    var self = this;
    if (typeof botiga.settings !== 'undefined' && 'no' === botiga.settings.misc.dropdowns_hover_delay) {
      return false;
    }

    // Add hover class to dropdown items. 
    // Run it only once and after the first user interaction on the page.
    var initialized = false;
    var events = [{
      name: 'scroll',
      selector: window
    }, {
      name: 'mouseenter',
      selector: document
    }, {
      name: 'mouseover',
      selector: document
    }, {
      name: 'touchstart',
      selector: document
    }];
    for (var _i = 0, _events = events; _i < _events.length; _i++) {
      var _event = _events[_i];
      _event.selector.addEventListener(_event.name, function () {
        if (initialized) {
          return false;
        }
        initialized = true;
        self.addHoverClassToDropdownItems();
      });
    }
  },
  /**
   * Add hover class to dropdown items.
   */
  addHoverClassToDropdownItems: function addHoverClassToDropdownItems() {
    var dropdownLis = document.querySelectorAll('.botiga-dropdown-li');
    var _iterator6 = _createForOfIteratorHelper(dropdownLis),
      _step6;
    try {
      var _loop = function _loop() {
        var li = _step6.value;
        var mouseOutTimeout,
          mouseOverTimeout,
          delayTime = 300;
        var parent = li;
        li.addEventListener('mouseover', function (e) {
          var self = this;
          clearTimeout(mouseOutTimeout);
          mouseOverTimeout = setTimeout(function () {
            self.classList.add('hovered');
          }, delayTime);
        });
        var subDropdownLis = li.querySelectorAll('.botiga-dropdown-ul > .botiga-dropdown-li');
        var _iterator7 = _createForOfIteratorHelper(subDropdownLis),
          _step7;
        try {
          for (_iterator7.s(); !(_step7 = _iterator7.n()).done;) {
            var subLi = _step7.value;
            subLi.addEventListener('mouseover', function (e) {
              clearTimeout(mouseOutTimeout);
              setTimeout(function () {
                parent.classList.add('hovered');
              }, delayTime);
            });
          }
        } catch (err) {
          _iterator7.e(err);
        } finally {
          _iterator7.f();
        }
        li.addEventListener('mouseout', function (e) {
          var self = this;
          clearTimeout(mouseOverTimeout);
          if (parent.contains(e.relatedTarget)) {
            return false;
          }
          mouseOutTimeout = setTimeout(function () {
            self.classList.remove('hovered');
          }, delayTime);
        });
      };
      for (_iterator6.s(); !(_step6 = _iterator6.n()).done;) {
        _loop();
      }
    } catch (err) {
      _iterator6.e(err);
    } finally {
      _iterator6.f();
    }
  },
  /*
  * Mobile navigation (accordion style navigation)
  */
  mobileAccordionNavigation: function mobileAccordionNavigation() {
    var navs = document.querySelectorAll('.botiga-dropdown-mobile-accordion');
    if (!navs.length) {
      return false;
    }
    var _iterator8 = _createForOfIteratorHelper(navs),
      _step8;
    try {
      for (_iterator8.s(); !(_step8 = _iterator8.n()).done;) {
        var nav = _step8.value;
        var nav_item = nav.querySelectorAll('.menu-item-has-children');
        if (!nav_item.length) {
          return false;
        }
        var _iterator9 = _createForOfIteratorHelper(nav_item),
          _step9;
        try {
          for (_iterator9.s(); !(_step9 = _iterator9.n()).done;) {
            var item = _step9.value;
            var dropdownToggler = item.querySelectorAll('.dropdown-symbol');
            dropdownToggler[0].addEventListener('click', function (e) {
              e.stopPropagation();
              var parent = this.parentNode;
              if (parent.classList.contains('expand')) {
                parent.classList.remove('expand');
              } else {
                parent.classList.add('expand');
              }
            });
          }
        } catch (err) {
          _iterator9.e(err);
        } finally {
          _iterator9.f();
        }
      }
    } catch (err) {
      _iterator8.e(err);
    } finally {
      _iterator8.f();
    }
  },
  /* 
  * Check if sub-menu items are visible. If not, reverse the item position 
  */
  checkMenuReverse: function checkMenuReverse() {
    var items = document.querySelectorAll('.header-login-register, .top-bar-login-register, .botiga-dropdown .menu li');
    var _iterator0 = _createForOfIteratorHelper(items),
      _step0;
    try {
      for (_iterator0.s(); !(_step0 = _iterator0.n()).done;) {
        var element = _step0.value;
        element.removeEventListener('mouseover', this.menuReverseEventHandler);
        element.addEventListener('mouseover', this.menuReverseEventHandler, {
          passive: true
        });
        element.removeEventListener('touchstart', this.menuReverseEventHandler);
        element.addEventListener('touchstart', this.menuReverseEventHandler, {
          passive: true
        });
      }
    } catch (err) {
      _iterator0.e(err);
    } finally {
      _iterator0.f();
    }
  },
  menuReverseEventHandler: function menuReverseEventHandler() {
    var is_rtl = 'rtl' === document.querySelector('html').getAttribute('dir');
    event.stopPropagation();
    var submenu = event.currentTarget.querySelector('.header-login-register>nav, .top-bar-login-register>nav, .sub-menu');
    if (submenu === null) {
      return false;
    }

    // Reverse horizontally
    submenu.classList.remove('sub-menu-reverse');
    if (is_rtl) {
      submenu.classList.add('sub-menu-reverse');
    } else {
      if (botiga.helpers.isInHorizontalViewport(submenu) == false && !submenu.closest('.menu-item').classList.contains('botiga-mega-menu')) {
        submenu.classList.add('sub-menu-reverse');
      } else {
        submenu.classList.remove('sub-menu-reverse');
      }
    }

    // Reverse vertically
    // Do not reverse vertically if the menu is in the header
    if (submenu.closest('.site-header') || submenu.closest('.bottom-header-row') || submenu.closest('.bhfb-header')) {
      return false;
    }
    submenu.classList.remove('sub-menu-reverse-vertically');
    if (botiga.helpers.isInVerticalViewport(submenu) == false && !submenu.closest('.menu-item').classList.contains('botiga-mega-menu')) {
      submenu.classList.add('sub-menu-reverse-vertically');
    } else {
      submenu.classList.remove('sub-menu-reverse-vertically');
    }
  }
};

/**
 * Desktop off canvas toggle navigation
 */
botiga.desktopOffCanvasToggleNav = {
  init: function init() {
    var siteNavigation = document.getElementById('site-navigation'),
      offCanvas = document.getElementsByClassName('botiga-desktop-offcanvas-menu')[0];

    // Return early if the navigation don't exist.
    if (!siteNavigation || typeof offCanvas === 'undefined') {
      return;
    }

    //Toggle submenus
    var submenuToggles = offCanvas.querySelectorAll('.dropdown-symbol, .menu-item-has-children > a[href="#"]');
    var _iterator1 = _createForOfIteratorHelper(submenuToggles),
      _step1;
    try {
      for (_iterator1.s(); !(_step1 = _iterator1.n()).done;) {
        var submenuToggle = _step1.value;
        submenuToggle.addEventListener('touchstart', submenuToggleHandler);
        submenuToggle.addEventListener('click', submenuToggleHandler);
        submenuToggle.addEventListener('keydown', function (e) {
          var isTabPressed = e.key === 'Enter' || e.keyCode === 13;
          if (!isTabPressed) {
            return;
          }
          e.preventDefault();
          var parent = submenuToggle.parentNode.parentNode;
          parent.getElementsByClassName('sub-menu')[0].classList.toggle('toggled');
        });
      }
    } catch (err) {
      _iterator1.e(err);
    } finally {
      _iterator1.f();
    }
    function submenuToggleHandler(e) {
      e.preventDefault();
      var parent = e.target.closest('li');
      parent.querySelector('.sub-menu').classList.toggle('toggled');
    }
  }
};

/**
 * Desktop offcanvas menu navigation
 */
botiga.desktopOffcanvasNav = {
  init: function init() {
    var buttons = document.querySelectorAll('.desktop-menu-toggle'),
      closeButton = document.getElementsByClassName('desktop-menu-close')[0],
      offcanvas = document.getElementsByClassName('botiga-desktop-offcanvas')[0];
    if (!buttons.length) {
      return false;
    }
    for (var i = 0; i < buttons.length; i++) {
      buttons[i].addEventListener('click', function (e) {
        e.preventDefault();
        if (offcanvas.classList.contains('botiga-desktop-offcanvas-show')) {
          offcanvas.classList.remove('botiga-desktop-offcanvas-show');
        } else {
          offcanvas.classList.add('botiga-desktop-offcanvas-show');
        }
      });
    }
    closeButton.addEventListener('click', function (e) {
      e.preventDefault();
      offcanvas.classList.remove('botiga-desktop-offcanvas-show');
    });

    // Close mega menu when clicking outside
    document.addEventListener('click', function (e) {
      if (e.target.closest('.botiga-desktop-offcanvas-menu') === null && offcanvas.querySelector('.botiga-mega-menu .sub-menu.toggled') !== null) {
        offcanvas.querySelector('.botiga-mega-menu .sub-menu.toggled').classList.remove('toggled');
      }
    });
  }
};

/**
 * Header search
 */
botiga.headerSearch = {
  init: function init() {
    var self = this,
      button = document.querySelectorAll('.header-search'),
      form = window.matchMedia('(max-width: 1024px)').matches ? document.querySelector('#masthead-mobile .header-search-form') : document.querySelector('#masthead .header-search-form'),
      overlay = document.getElementsByClassName('search-overlay')[0],
      searchBtn = form !== null ? form.getElementsByClassName('search-submit')[0] : undefined;
    if (button.length === 0) {
      return;
    }
    if (document.body.classList.contains('has-bhfb-builder')) {
      form = document.querySelector('.header-search-form');
    }
    if (typeof overlay !== 'undefined') {
      var _iterator10 = _createForOfIteratorHelper(button),
        _step10;
      try {
        for (_iterator10.s(); !(_step10 = _iterator10.n()).done;) {
          var buttonEl = _step10.value;
          buttonEl.addEventListener('click', function (e) {
            e.preventDefault();

            // Hide other search icons 
            if (button.length > 1) {
              var _iterator11 = _createForOfIteratorHelper(button),
                _step11;
              try {
                for (_iterator11.s(); !(_step11 = _iterator11.n()).done;) {
                  var btn = _step11.value;
                  btn.classList.toggle('hide');
                }
              } catch (err) {
                _iterator11.e(err);
              } finally {
                _iterator11.f();
              }
            }
            form.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.classList.toggle('header-search-form-active');
            e.target.closest('.header-search').getElementsByClassName('icon-search')[0].classList.toggle('active');
            e.target.closest('.header-search').getElementsByClassName('icon-cancel')[0].classList.toggle('active');
            e.target.closest('.header-search').classList.add('active');
            e.target.closest('.header-search').classList.remove('hide');
            var searchInput = '';
            if (window.matchMedia('screen and (min-width: 1024px)').matches) {
              searchInput = document.querySelectorAll('.bhfb-desktop .header-search-form .search-field')[0];
            } else {
              searchInput = document.querySelectorAll('.bhfb-mobile .header-search-form .search-field')[0];
            }
            if (e.target.closest('.header-search').parentNode.classList.contains('header-search-form-hide-input-on-mobile')) {
              searchInput = document.querySelectorAll('.bhfb-mobile .header-search-form .search-field')[1];
            }
            if (typeof searchInput !== 'undefined') {
              searchInput.focus();
            }
            if (e.target.closest('.botiga-offcanvas-menu') !== null) {
              e.target.closest('.botiga-offcanvas-menu').classList.remove('toggled');
            }
          });
        }
      } catch (err) {
        _iterator10.e(err);
      } finally {
        _iterator10.f();
      }
      overlay.addEventListener('click', function () {
        form.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('header-search-form-active');

        // Back buttons to default state
        self.backButtonsToDefaultState(button);
      });
    }
    if (typeof searchBtn !== 'undefined') {
      searchBtn.addEventListener('keydown', function (e) {
        var isTabPressed = e.key === 'Tab' || e.keyCode === KEYCODE_TAB;
        if (!isTabPressed) {
          return;
        }
        form.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('header-search-form-active');

        // Back buttons to default state
        self.backButtonsToDefaultState(button);
        button.focus();
      });
    }
    var desktop_offcanvas = document.getElementsByClassName('header-desktop-offcanvas-layout2')[0] !== null ? document.getElementsByClassName('botiga-desktop-offcanvas')[0] : false;
    if (desktop_offcanvas) {
      desktop_offcanvas.addEventListener('click', function (e) {
        if (e.target.closest('.header-search') === null) {
          form.classList.remove('active');
          overlay.classList.remove('active');
          document.body.classList.remove('header-search-form-active');

          // Back buttons to default state
          self.backButtonsToDefaultState(button);
        }
      });
    }
    return this;
  },
  backButtonsToDefaultState: function backButtonsToDefaultState(button) {
    var _iterator12 = _createForOfIteratorHelper(button),
      _step12;
    try {
      for (_iterator12.s(); !(_step12 = _iterator12.n()).done;) {
        var btn = _step12.value;
        btn.classList.remove('hide');
        btn.querySelector('.icon-cancel').classList.remove('active');
        btn.querySelector('.icon-search').classList.add('active');
      }
    } catch (err) {
      _iterator12.e(err);
    } finally {
      _iterator12.f();
    }
  }
};

/**
 * Sticky header
 */
botiga.stickyHeader = {
  init: function init() {
    var _this = this,
      sticky = document.getElementsByClassName('sticky-header')[0],
      bhfb_sticky = document.getElementsByClassName('bhfb-sticky-header')[0],
      body = document.getElementsByTagName('body')[0];
    if ('undefined' === typeof sticky && 'undefined' === typeof bhfb_sticky) {
      return;
    }
    var sticky_selector = 'undefined' !== typeof sticky ? '.sticky-header' : '.bhfb-sticky-header';
    if ('undefined' === typeof sticky) {
      sticky = bhfb_sticky;
    }
    this.stickyChangeLogo();
    var topOffset = window.pageYOffset || document.documentElement.scrollTop;
    if (topOffset > 10) {
      sticky.classList.add('is-sticky');
      body.classList.add('sticky-header-active');
      window.dispatchEvent(new Event('botiga.sticky.header.activated'));
    }
    var header_offset_y = document.querySelector(sticky_selector).getBoundingClientRect().y;
    if (document.body.classList.contains('admin-bar')) {
      header_offset_y = header_offset_y - 32;
    }
    if (document.body.classList.contains('botiga-site-layout-padded')) {
      header_offset_y = header_offset_y - parseInt(getComputedStyle(document.body).getPropertyValue('--botiga_padded_spacing'));
    }
    if (sticky.classList.contains('sticky-scrolltop') || document.querySelector('.bhfb.sticky-scrolltop') !== null) {
      var lastScrollTop = 0;
      window.addEventListener('scroll', function () {
        var scroll = window.pageYOffset || document.documentElement.scrollTop,
          is_sticky = scroll > lastScrollTop || scroll < 10;
        if (document.querySelector('.bhfb.sticky-scrolltop') !== null) {
          var bhfb_header_height = document.querySelector('.bhfb.sticky-scrolltop').getBoundingClientRect().height;
          is_sticky = scroll < bhfb_header_height;
        }
        if (is_sticky) {
          sticky.classList.remove('is-sticky');
          body.classList.remove('sticky-header-active');
          _this.isHBStickyDeactivated('scrolltop');
          body.classList.add('on-header-area');
          window.dispatchEvent(new Event('botiga.sticky.header.deactivated'));
        } else {
          sticky.classList.add('is-sticky');
          body.classList.add('sticky-header-active');
          _this.isHBStickyActive('scrolltop');
          body.classList.remove('on-header-area');
          window.dispatchEvent(new Event('botiga.sticky.header.activated'));
        }
        lastScrollTop = scroll <= 0 ? 0 : scroll;
      }, false);
    } else {
      window.addEventListener('scroll', function () {
        var vertDist = window.scrollY;
        if (vertDist > header_offset_y) {
          sticky.classList.add('sticky-shadow');
          body.classList.add('sticky-header-active');
          _this.isHBStickyActive();
          window.dispatchEvent(new Event('botiga.sticky.header.activated'));
        } else {
          sticky.classList.remove('sticky-shadow');
          body.classList.remove('sticky-header-active');
          _this.isHBStickyDeactivated();
          window.dispatchEvent(new Event('botiga.sticky.header.deactivated'));
        }
      }, false);
    }
  },
  isHBStickyActive: function isHBStickyActive(effect) {
    var bhfb = document.querySelector('header.bhfb'),
      has_admin_bar = document.body.classList.contains('admin-bar'),
      above_header_row = document.querySelector('.bhfb-above_header_row'),
      main_header_row = document.querySelector('.bhfb-main_header_row'),
      below_header_row = document.querySelector('.bhfb-below_header_row');
    if (bhfb === null) {
      return false;
    }
    var topVal = 0,
      convertToPositive = false;
    if (bhfb.classList.contains('sticky-row-main-header-row')) {
      if (!above_header_row.classList.contains('bt-d-none')) {
        topVal = above_header_row.clientHeight;
        if (document.body.classList.contains('botiga-site-layout-padded')) {
          convertToPositive = true;
        }
      } else {
        convertToPositive = true;
      }

      // Admin Bar
      if (has_admin_bar) {
        topVal = topVal - 32;
      } else {
        if (!above_header_row.classList.contains('bt-d-none') && document.body.classList.contains('botiga-site-layout-padded')) {
          convertToPositive = false;
        }
      }

      // Padded Layout
      if (document.body.classList.contains('botiga-site-layout-padded')) {
        topVal = topVal - parseFloat(getComputedStyle(document.body).getPropertyValue('--botiga_padded_spacing'));
      }

      // Conert to negative value
      topVal = convertToPositive ? +Math.abs(topVal) : -Math.abs(topVal);
      bhfb.style.top = "".concat(topVal, "px");
    }
    if (bhfb.classList.contains('sticky-row-below-header-row')) {
      if (!below_header_row.classList.contains('bt-d-none')) {
        if (has_admin_bar) {
          topVal = bhfb.clientHeight - below_header_row.clientHeight - 32 - parseFloat(getComputedStyle(below_header_row).borderBottomWidth);
        } else {
          topVal = bhfb.clientHeight - below_header_row.clientHeight - parseFloat(getComputedStyle(below_header_row).borderBottomWidth);
        }
      }
      if (above_header_row.classList.contains('bt-d-none') && main_header_row.classList.contains('bt-d-none')) {
        convertToPositive = true;
      }

      // Padded Layout
      if (document.body.classList.contains('botiga-site-layout-padded')) {
        topVal = topVal - parseFloat(getComputedStyle(document.body).getPropertyValue('--botiga_padded_spacing'));
      }

      // Conert to negative value
      topVal = convertToPositive ? +Math.abs(topVal) : -Math.abs(topVal);
      bhfb.style.top = "".concat(topVal, "px");
    }
    if (effect === 'scrolltop' && document.body.classList.contains('on-header-area')) {
      bhfb.classList.add('bhfb-no-transition');
      setTimeout(function () {
        bhfb.classList.remove('bhfb-no-transition');
      }, 500);
    }
  },
  isHBStickyDeactivated: function isHBStickyDeactivated(effect) {
    var bhfb = document.querySelector('header.bhfb');
    if (bhfb === null) {
      return false;
    }
    if (bhfb.classList.contains('sticky-row-main-header-row')) {
      bhfb.style.top = '0px';
    }
    if (bhfb.classList.contains('sticky-row-below-header-row')) {
      if (!document.querySelector('.bhfb-below_header_row').classList.contains('bt-d-none')) {
        bhfb.style.top = '0px';
      }
    }
  },
  stickyChangeLogo: function stickyChangeLogo() {
    var sticky_flag = false;
    if (window.matchMedia('screen and (min-width: 1024px)').matches) {
      if (typeof botiga_sticky_header_logo !== 'undefined') {
        var logo = document.body.classList.contains('has-bhfb-builder') ? document.querySelector('.bhfb-sticky-header .site-branding img') : document.querySelector('.sticky-header .site-branding img');
        if (logo === null) {
          return false;
        }
        var initialSrc = logo.getAttribute('src'),
          initialSrcset = logo.getAttribute('srcset'),
          initialSizes = logo.getAttribute('sizes'),
          initialHeight = logo.clientHeight;
        window.addEventListener('botiga.sticky.header.activated', function () {
          if (sticky_flag) {
            return false;
          }
          logo.setAttribute('src', botiga_sticky_header_logo[0]);
          logo.setAttribute('style', 'max-height: ' + initialHeight + 'px;');
          if (typeof botiga_sticky_header_logo['srcset'] !== 'undefined') {
            logo.setAttribute('srcset', botiga_sticky_header_logo['srcset']);
          }
          if (typeof botiga_sticky_header_logo['sizes'] !== 'undefined') {
            logo.setAttribute('sizes', botiga_sticky_header_logo['sizes']);
          }
          sticky_flag = true;
        });
        window.addEventListener('botiga.sticky.header.deactivated', function () {
          if (!sticky_flag) {
            return false;
          }
          logo.setAttribute('src', initialSrc);
          if (initialSrcset !== null) {
            logo.setAttribute('srcset', initialSrcset);
          }
          if (initialSizes !== null) {
            logo.setAttribute('sizes', initialSizes);
          }
          sticky_flag = false;
        });
      }
    }
  }
};

/**
 * Botiga scroll direction
 */
botiga.scrollDirection = {
  init: function init() {
    var elements = document.querySelectorAll('.botiga-single-sticky-add-to-cart-wrapper.hide-when-scroll'),
      body = document.getElementsByTagName('body')[0];
    if ('null' === typeof elements) {
      return;
    }
    var lastScrollTop = 0;
    window.addEventListener('scroll', function () {
      var scroll = window.pageYOffset || document.documentElement.scrollTop;
      if (scroll > lastScrollTop) {
        body.classList.remove('botiga-scrolling-up');
        body.classList.add('botiga-scrolling-down');
      } else {
        body.classList.remove('botiga-scrolling-down');
        body.classList.add('botiga-scrolling-up');
      }
      lastScrollTop = scroll <= 0 ? 0 : scroll;
    }, false);
  }
};

/**
 * Botiga custom add to cart button
 * 
 */
botiga.customAddToCartButton = {
  init: function init() {
    var button = document.querySelectorAll('.botiga-custom-addtocart');
    if (!button.length) {
      return false;
    }
    for (var i = 0; i < button.length; i++) {
      button[i].addEventListener('click', function (e) {
        e.preventDefault();
        var button = this,
          productId = this.getAttribute('data-product-id'),
          context = this.getAttribute('data-context'),
          initial_text = this.innerHTML,
          loading_text = this.getAttribute('data-loading-text'),
          added_text = this.getAttribute('data-added-text'),
          nonce = this.getAttribute('data-nonce');
        var ajax = new XMLHttpRequest();
        ajax.open('POST', botiga.ajaxurl, true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        button.innerHTML = loading_text;
        ajax.onload = function () {
          if (this.status >= 200 && this.status < 400) {
            button.innerHTML = added_text;
            setTimeout(function () {
              button.innerHTML = initial_text;
            }, 1500);
            jQuery(document.body).trigger('wc_fragment_refresh');
            jQuery(document.body).trigger('added_to_cart');
            document.body.dispatchEvent(new Event('botiga.custom_added_to_cart'));
          }
        };
        ajax.send('action=botiga_custom_addtocart&product_id=' + productId + '&context=' + context + '&nonce=' + nonce);
      });
    }
  }
};

/**
 * Back to top button
 */
botiga.backToTop = {
  init: function init() {
    this.backToTop();
    window.addEventListener('scroll', function () {
      this.backToTop();
    }.bind(this));
    this.safariDoubleClickFix();
  },
  backToTop: function backToTop() {
    var button = document.getElementsByClassName('back-to-top')[0];
    if ('undefined' !== typeof button) {
      var scrolled = window.pageYOffset;
      if (scrolled > 300) {
        button.classList.add('display');
      } else {
        button.classList.remove('display');
      }
      button.removeEventListener('click', this.scrollToTop);
      button.addEventListener('click', this.scrollToTop);
    }
  },
  scrollToTop: function scrollToTop() {
    window.scrollTo({
      top: 0,
      left: 0,
      behavior: 'smooth'
    });
  },
  // Unknown safari issue. If we add a 'touchend' event listener to the button the problem is resolved.
  // Fixes: https://wordpress.org/support/topic/double-tap-issue-on-mobile/
  safariDoubleClickFix: function safariDoubleClickFix() {
    var links = document.querySelectorAll('.product-gallery-summary .botiga-single-addtocart-wrapper .button, .single-product .content-wrapper a, .single-product .footer-widgets a, .single-product .site-footer a');
    if (!links.length) {
      return false;
    }
    var _iterator13 = _createForOfIteratorHelper(links),
      _step13;
    try {
      for (_iterator13.s(); !(_step13 = _iterator13.n()).done;) {
        var link = _step13.value;
        link.addEventListener('touchend', function () {});
      }
    } catch (err) {
      _iterator13.e(err);
    } finally {
      _iterator13.f();
    }
  }
};

/**
 * Quantity button
 */
botiga.qtyButton = {
  init: function init(type) {
    this.events(type);
    this.wooEvents();
  },
  isInsideCarousel: function isInsideCarousel(el) {
    return el.closest('.botiga-carousel') !== null && el.closest('.botiga-carousel').classList.contains('botiga-carousel-not-initialized') === false && el.closest('.botiga-carousel').getAttribute('data-initialized') !== 'true';
  },
  events: function events(type) {
    var self = this;
    var qty = document.querySelectorAll('.botiga-quantity-minus');
    if (qty.length < 1) {
      return false;
    }
    for (var i = 0; i < qty.length; i++) {
      var wrapper = qty[i].closest('.quantity');
      if (wrapper === null || wrapper.dataset.qtyInitialized || self.isInsideCarousel(qty[i])) {
        continue;
      }
      if (wrapper.classList.contains('hidden')) {
        return false;
      }
      var qtyInput = wrapper.querySelector('.qty'),
        plus = wrapper.querySelector('.botiga-quantity-plus'),
        minus = wrapper.querySelector('.botiga-quantity-minus'),
        input = wrapper.querySelector('.input-text');
      plus.classList.add('show');
      minus.classList.add('show');
      qtyInput.addEventListener('change', function (e) {
        self.behaviorsBasedOnQuantityValue(this, this.value);
      });
      qtyInput.addEventListener('keyup', function (e) {
        self.behaviorsBasedOnQuantityValue(this, this.value);
      });
      plus.addEventListener('click', function (e) {
        e.preventDefault();
        var input = this.parentNode.querySelector('.qty'),
          qtyMax = Number(input.getAttribute('max')) || 99999,
          qtyMin = Number(input.getAttribute('min')),
          qtyStep = Number(input.getAttribute('step')),
          qtyValue = Number(input.value),
          changeEvent = document.createEvent('HTMLEvents');
        input.value = Math.max(qtyMin, Math.min(qtyMax, (qtyValue + qtyStep).toFixed(1)));
        changeEvent.initEvent('change', true, false);
        input.dispatchEvent(changeEvent);
        self.updateAddToCartQuantity(this, input.value);
        self.updateBuyNowButtonQuantity(this, input.value);
        self.behaviorsBasedOnQuantityValue(this, input.value);
      });
      minus.addEventListener('click', function (e) {
        e.preventDefault();
        var input = this.parentNode.querySelector('.qty'),
          qtyMax = Number(input.getAttribute('max')) || 99999,
          qtyMin = Number(input.getAttribute('min')),
          qtyStep = Number(input.getAttribute('step')),
          qtyValue = Number(input.value),
          changeEvent = document.createEvent('HTMLEvents');
        input.value = Math.max(qtyMin, Math.min(qtyMax, (qtyValue - qtyStep).toFixed(1)));
        changeEvent.initEvent('change', true, false);
        input.dispatchEvent(changeEvent);
        self.updateAddToCartQuantity(this, input.value);
        self.updateBuyNowButtonQuantity(this, input.value);
        self.behaviorsBasedOnQuantityValue(this, input.value);
      });
      input.addEventListener('change', function (e) {
        self.updateAddToCartQuantity(this, this.value);
        self.updateBuyNowButtonQuantity(this, this.value);
      });
      wrapper.dataset.qtyInitialized = true;
    }
  },
  wooEvents: function wooEvents() {
    var _self = this;
    if (typeof jQuery !== 'undefined') {
      jQuery('body').on('updated_cart_totals', function () {
        _self.events();
      });
      jQuery(document).on('wc_fragments_loaded', function () {
        _self.events();
      });
    }
  },
  updateAddToCartQuantity: function updateAddToCartQuantity(qtyItem, qtyValue) {
    if (qtyItem.closest('.woocommerce-cart-form') !== null) {
      return false;
    }
    var productSelector = qtyItem.closest('.product') ? '.product' : '.wc-block-grid__product',
      product = qtyItem.closest(productSelector),
      qtyInput = qtyItem.parentNode.querySelector('.qty');
    if (product) {
      var addToCartButton = product.querySelector('.add_to_cart_button:not(.single_add_to_cart_button)');
      if (addToCartButton) {
        addToCartButton.setAttribute('data-quantity', qtyValue);
      }
    }
    var miniCartItem = qtyItem.closest('.mini_cart_item') ? qtyItem.closest('.mini_cart_item') : qtyItem.closest('.woocommerce-cart-form__cart-item');
    if (miniCartItem) {
      var $cart = jQuery(qtyItem.closest('.widget_shopping_cart'));
      $cart.block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6
        }
      });
      jQuery.post({
        url: botiga.ajaxurl,
        data: {
          action: 'botiga_update_mini_cart_quantity',
          quantity: qtyInput.value,
          cart_item_key: qtyInput.name,
          nonce: jQuery('#mini_cart_qty_nonce').val()
        },
        success: function success(response) {
          jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
          setTimeout(function () {
            $cart.unblock();
          }, 100);
        }
      });
    }
  },
  updateBuyNowButtonQuantity: function updateBuyNowButtonQuantity(qtyItem, qtyValue) {
    var is_checkout_quantity = qtyItem.parentNode.parentNode.classList.contains('botiga-sc-product-name') || qtyItem.parentNode.parentNode.classList.contains('product-name') ? true : false,
      is_mini_cart_quantity = qtyItem.closest('.woocommerce-mini-cart-item') !== null ? true : false;
    if (is_checkout_quantity || is_mini_cart_quantity) {
      return false;
    }
    var productSelector = qtyItem.closest('.product') ? '.product' : '.wc-block-grid__product',
      product = qtyItem.closest(productSelector),
      qtyInput = qtyItem.parentNode.querySelector('.qty'),
      buyNowButton = product ? product.querySelector('.botiga-buy-now-button') : null;
    if (buyNowButton === null) {
      return false;
    }
    var url = new URL(buyNowButton.getAttribute('href'));
    url.searchParams.set('quantity', qtyValue);
    buyNowButton.setAttribute('href', url);
  },
  behaviorsBasedOnQuantityValue: function behaviorsBasedOnQuantityValue(qtyItem, qtyValue) {
    var productSelector = qtyItem.closest('.product') ? '.product' : '.wc-block-grid__product',
      product = qtyItem.closest(productSelector);
    if (product) {
      var addToCartButton = product.querySelector('.add_to_cart_button:not(.single_add_to_cart_button)');
      if (addToCartButton) {
        if (qtyValue == 0) {
          addToCartButton.classList.add('disabled');
        } else {
          addToCartButton.classList.remove('disabled');
        }
      }
    }
  }
};

/**
 * Carousel 
 */
botiga.carousel = {
  init: function init() {
    this.build();
    this.events();
  },
  build: function build() {
    var self = this;
    if (document.querySelector('.botiga-carousel') === null && document.querySelector('.has-cross-sells-carousel') === null && document.querySelector('.botiga-woocommerce-mini-cart__cross-sell') === null) {
      return false;
    }
    var carouselEls = document.querySelectorAll('.botiga-carousel, #masthead .cross-sells, .botiga-side-mini-cart .cross-sells, .cart-collaterals .cross-sells');
    var _iterator14 = _createForOfIteratorHelper(carouselEls),
      _step14;
    try {
      for (_iterator14.s(); !(_step14 = _iterator14.n()).done;) {
        var carouselEl = _step14.value;
        if (carouselEl.querySelector('.botiga-carousel-stage') === null) {
          carouselEl.querySelector('.products').classList.add('botiga-carousel-stage');
        }
        if (carouselEl.getAttribute('data-initialized') !== 'true') {
          var perPage = carouselEl.getAttribute('data-per-page');
          if (perPage === null) {
            var stageClassList = carouselEl.querySelector('.products').classList.value;
            [1, 2, 3, 4, 5].forEach(function (columns) {
              if (stageClassList.indexOf('columns-' + columns) > 0) {
                perPage = columns;
              }
            });
          }

          // Mount carousel wrapper
          var wrapper = document.createElement('div'),
            stage = carouselEl.querySelector('.botiga-carousel-stage');
          wrapper.className = 'botiga-carousel-wrapper';
          wrapper.innerHTML = stage.outerHTML;
          stage.remove();
          carouselEl.append(wrapper);

          // Margin
          var margin = 30;
          if (typeof botiga_carousel !== 'undefined') {
            margin = parseInt(botiga_carousel.margin_desktop);
          } else if (carouselEl.closest('.botiga-woocommerce-mini-cart__cross-sell') !== null) {
            margin = 15;
          }
          var itemsQty = carouselEl.querySelectorAll('li.product').length;
          if (itemsQty <= parseInt(perPage)) {
            carouselEl.classList.add('botiga-carousel-not-initialized');
            return false;
          }

          // Initialize
          var carousel = new Siema({
            parentSelector: carouselEl,
            selector: '.botiga-carousel-stage',
            duration: 200,
            easing: 'ease-out',
            perPage: perPage !== null ? {
              0: 1,
              768: 2,
              1025: parseInt(perPage)
            } : 2,
            startIndex: 0,
            draggable: true,
            multipleDrag: false,
            threshold: 20,
            loop: true,
            rtl: 'rtl' === document.querySelector('html').getAttribute('dir') ? true : false,
            // autoplay: true, TO DO
            margin: margin,
            onInit: function onInit() {
              window.dispatchEvent(new Event('botiga.carousel.initialized'));
              self.maybeInitExtraFeatures();
            }
          });
        }
      }
    } catch (err) {
      _iterator14.e(err);
    } finally {
      _iterator14.f();
    }
  },
  events: function events() {
    var _this = this;
    if (typeof jQuery !== 'undefined') {
      var onpageload = true;
      jQuery(document.body).on('wc_fragment_refresh added_to_cart removed_from_cart', function () {
        setTimeout(function () {
          var mini_cart = document.getElementById('site-header-cart');
          if (mini_cart === null) {
            return false;
          }
          var mini_cart_list = mini_cart.querySelector('.cart_list');
          if (mini_cart_list !== null) {
            if (mini_cart_list.children.length > 2) {
              mini_cart.classList.remove('mini-cart-has-no-scroll');
              mini_cart.classList.add('mini-cart-has-scroll');
            } else {
              mini_cart.classList.remove('mini-cart-has-scroll');
              mini_cart.classList.add('mini-cart-has-no-scroll');
            }
          }
          _this.build();
          onpageload = false;
        }, onpageload ? 1000 : 0);
      });
    }
  },
  maybeInitExtraFeatures: function maybeInitExtraFeatures() {
    botiga.qtyButton.init();
  }
};

/**
 * Copy link to clipboard
 */
botiga.copyLinkToClipboard = {
  init: function init(event, el) {
    event.preventDefault();
    navigator.clipboard.writeText(window.location.href);
    el.classList.add('copied');
    el.setAttribute('data-botiga-tooltip', botiga.i18n.botiga_sharebox_copy_link_copied);
    setTimeout(function () {
      el.setAttribute('data-botiga-tooltip', botiga.i18n.botiga_sharebox_copy_link);
      el.classList.remove('copied');
    }, 1000);
  }
};

/**
 * Toggle class
 */
botiga.toggleClass = {
  init: function init(event, el, triggerEvent) {
    event.preventDefault();
    event.stopPropagation();
    var selector = document.querySelector(el.getAttribute('data-botiga-selector')),
      removeClass = el.getAttribute('data-botiga-toggle-class-remove'),
      classname = el.getAttribute('data-botiga-toggle-class'),
      classes = selector.classList;
    if (typeof removeClass === 'string') {
      classes.remove(removeClass);
    }
    classes.toggle(classname);
    if (triggerEvent) {
      var ev = document.createEvent('HTMLEvents');
      ev.initEvent(triggerEvent, true, false);
      window.dispatchEvent(ev);
    }
  }
};

/**
 * Collapse
 */
botiga.collapse = {
  init: function init() {
    var elements = document.querySelectorAll('[data-botiga-collapse]');
    if (!elements.length) {
      return false;
    }
    var _this = this;
    var _loop2 = function _loop2(i) {
        var opts = elements[i].getAttribute('data-botiga-collapse'),
          options = JSON.parse(opts.replace(/'/g, '"').replace(';', ''));
        if (!options.enable) {
          return {
            v: false
          };
        }
        _this.expand(elements[i], options, true);
        elements[i].addEventListener('click', function (e) {
          e.preventDefault();
          this.dispatchEvent(new Event('botiga.collapse.before.expand'));
          if (!elements[i].classList.contains('active')) {
            _this.expand(elements[i], options);
          } else {
            _this.collapse(elements[i], options);
          }
          this.dispatchEvent(new Event('botiga.collapse.after.collapse'));
        });
        if (options.options.oneAtTime) {
          elements[i].addEventListener('botiga.collapse.before.expand', function () {
            var botiga_collapse = document.querySelectorAll(options.options.oneAtTimeParentSelector + ' [data-botiga-collapse]');
            for (var u = 0; u < botiga_collapse.length; u++) {
              _this.collapseAll(botiga_collapse[u], options);
            }
          });
        }
      },
      _ret;
    for (var i = 0; i < elements.length; i++) {
      _ret = _loop2(i);
      if (_ret) return _ret.v;
    }
  },
  expand: function expand(el, options, first_load) {
    if (first_load && !el.classList.contains('active')) {
      return false;
    }
    var targetSelectorId = options.id,
      target = document.getElementById(targetSelectorId),
      targetContent = target.querySelector('.botiga-collapse__content');
    target.addEventListener('transitionend', this.expandTransitionEnd.bind(this, el));
    target.style = 'max-height: ' + targetContent.clientHeight + 'px;';
    el.classList.add('active');
    target.classList.add('active');
  },
  expandTransitionEnd: function expandTransitionEnd(el) {
    if (!el.classList.contains('active')) {
      return false;
    }
    el.dispatchEvent(new Event('botiga.collapse.expanded'));
  },
  collapse: function collapse(el, options, a) {
    var targetSelectorId = options.id,
      target = document.getElementById(targetSelectorId);
    target.style = 'max-height: 0px;';
    el.classList.remove('active');
    target.classList.remove('active');
    el.dispatchEvent(new Event('botiga.collapse.collapsed'));
  },
  collapseAll: function collapseAll(el, options) {
    el.classList.remove('active');
    el.nextElementSibling.classList.remove('active');
    el.nextElementSibling.style = 'max-height: 0px;';
  }
};
botiga.tabsNav = {
  init: function init() {
    var tabsNav = document.querySelectorAll('.botiga-tabs-nav');
    if (!tabsNav.length) {
      return false;
    }
    this.events();
  },
  events: function events() {
    var tabsNavItems = document.querySelectorAll('.botiga-tabs-nav-item');
    var _iterator15 = _createForOfIteratorHelper(tabsNavItems),
      _step15;
    try {
      var _loop3 = function _loop3() {
        var tabItem = _step15.value;
        var hasClickOnMouseOver = tabItem.closest('.botiga-tabs-nav').classList.contains('botiga-tabs-nav-click-on-mouseover');
        if (hasClickOnMouseOver) {
          var st;
          tabItem.addEventListener('mouseover', function (e) {
            e.preventDefault();
            var self = this;
            st = setTimeout(function () {
              self.dispatchEvent(new Event('click'));
            }, 500);
          });
          tabItem.addEventListener('mouseout', function (e) {
            clearTimeout(st);
          });
        }
        tabItem.addEventListener('click', function (e) {
          e.preventDefault();
          var _this = this,
            tabId = this.querySelector('.botiga-tabs-nav-link').getAttribute('href'),
            previousTabId = this.closest('.botiga-tabs-nav').querySelector('.botiga-tabs-nav-item.is-active .botiga-tabs-nav-link').getAttribute('href'),
            previousActiveTab = document.querySelector(previousTabId),
            tabContentTo = document.querySelector(tabId),
            activeTabsCount = tabContentTo.parentNode.querySelectorAll('.botiga-tab-content.is-active').length;
          if (tabContentTo === null) {
            return false;
          }
          if (activeTabsCount === 0 || activeTabsCount > 1) {
            return false;
          }
          if (tabContentTo.classList.contains('is-active')) {
            return false;
          }
          tabsNavItems = this.closest('.botiga-tabs-nav').querySelectorAll('.botiga-tabs-nav-item');
          var _iterator16 = _createForOfIteratorHelper(tabsNavItems),
            _step16;
          try {
            for (_iterator16.s(); !(_step16 = _iterator16.n()).done;) {
              var _tabItem = _step16.value;
              var _tabContentTo = document.querySelector(_tabItem.querySelector('.botiga-tabs-nav-link').getAttribute('href'));
              if (_tabContentTo === null) {
                continue;
              }
              _tabItem.classList.remove('is-active');
              _tabContentTo.classList.remove('is-active');
            }
          } catch (err) {
            _iterator16.e(err);
          } finally {
            _iterator16.f();
          }
          previousActiveTab.classList.add('removing');
          setTimeout(function () {
            _this.classList.add('is-active');
            tabContentTo.classList.add('activating');
            setTimeout(function () {
              tabContentTo.classList.add('is-active');
              tabContentTo.classList.remove('activating');
            }, 300);
            previousActiveTab.classList.remove('removing');
          }, 300);
        });
      };
      for (_iterator15.s(); !(_step15 = _iterator15.n()).done;) {
        _loop3();
      }
    } catch (err) {
      _iterator15.e(err);
    } finally {
      _iterator15.f();
    }
  }
};

/**
 * Misc
 */
botiga.misc = {
  init: function init() {
    this.general();
    this.wcExpressPayButtons();
    this.singleProduct();
    this.cart();
    this.checkout();
    this.customizer();
  },
  general: function general() {
    var mini_cart = document.getElementById('site-header-cart');
    if (mini_cart === null) {
      return false;
    }
    mini_cart.addEventListener('mouseover', function () {
      document.body.classList.add('bt-mini-cart-hovered');
    });
    mini_cart.addEventListener('mouseout', function () {
      document.body.classList.remove('bt-mini-cart-hovered');
    });
  },
  wcExpressPayButtons: function wcExpressPayButtons() {
    var is_checkout_page = document.querySelector('body.woocommerce-checkout'),
      is_cart_page = document.querySelector('body.woocommerce-cart'),
      is_single_product = document.querySelector('body.single-product');
    if (is_single_product === null && is_checkout_page === null && is_cart_page === null) {
      return false;
    }
    if (typeof jQuery === 'function') {
      (function ($) {
        if (typeof $('#wc-stripe-payment-request-button-separator, #wcpay-payment-request-wrapper').get(0) === 'undefined') {
          return false;
        }
        if (is_checkout_page === null) {
          $('#wc-stripe-payment-request-button-separator, #wcpay-payment-request-button-separator').appendTo('form.cart');
          $('#wc-stripe-payment-request-wrapper, #wcpay-payment-request-wrapper').appendTo('form.cart');
          $(window).trigger('botiga.wcexpresspaybtns.appended');
        }
      })(jQuery);
    }
  },
  singleProduct: function singleProduct() {
    var _this = this,
      is_product_page = document.querySelector('body.single-product');
    if (is_product_page === null) {
      return false;
    }

    // Move reset variations button for better styling
    if (typeof jQuery === 'function') {
      (function ($) {
        $('.variations_form').each(function () {
          if ($(this).data('misc-variations') === true) {
            return false;
          }

          // Move reset button
          _this.moveResetVariationButton($(this));

          // First load
          _this.checkIfHasVariationSelected($(this));

          // on change variation select
          $(this).on('woocommerce_variation_select_change', function () {
            _this.checkIfHasVariationSelected($(this));
          });
          $(this).data('misc-variations', true);
        });

        // Single Product - In Cart Flag.
        if ($('.botiga-in-cart-flag').length) {
          var $form = $('body.single-product .entry-summary .variations_form, .botiga-tb-sp-add-to-cart'),
            addToCartButton = $form.find('.single_add_to_cart_button'),
            defaultAddToCartButtonText = addToCartButton.text();
          $form.each(function () {
            $(this).on('found_variation', function (event, variation) {
              var variationInCart = botigaInCartFlag.variations_in_cart.filter(function (a) {
                return a.variation_id === variation.variation_id;
              });
              var addToCartButtonText = variationInCart.length ? variationInCart[0].addtocart_button_text : addToCartButton.text();
              addToCartButton.html(addToCartButtonText);
            });
            $(this).on('reset_data', function () {
              addToCartButton.text(defaultAddToCartButtonText);
            });
          });
        }
      })(jQuery);
    }
  },
  moveResetVariationButton: function moveResetVariationButton($this) {
    var clone = $this.find('.reset_variations');
    clone.remove();
    $this.find('table').after(clone);
  },
  checkIfHasVariationSelected: function checkIfHasVariationSelected($this) {
    var all_empty = true;
    $this.find('td.value select').each(function () {
      if (jQuery(this).val() !== '') {
        all_empty = false;
        jQuery(this).closest('table').addClass('has-variation-selected');
        return false;
      } else {
        jQuery(this).closest('table').removeClass('has-variation-selected');
      }
    });
    if (all_empty) {
      $this.closest('.variations_form').find('.reset_variations').css('display', 'none');
    } else {
      $this.closest('.variations_form').find('.reset_variations').css('display', 'inline-block');
    }
  },
  cart: function cart() {
    var is_cart_page = document.querySelector('body.woocommerce-cart');
    if (is_cart_page === null) {
      return false;
    }
    if (typeof jQuery === 'function') {
      (function ($) {
        if ($('header.has-sticky-header').length) {
          $(document).on('updated_cart_totals', function () {
            $(window).on('scroll', function (e) {
              $('html, body').stop(true, false);
              $(this).off(e);
              $('html, body').animate({
                scrollTop: $('[role="alert"]').offset().top - $('header.has-sticky-header').height()
              }, 1000);
            });
          });
        }
      })(jQuery);
    }
  },
  checkout: function checkout() {
    var is_checkout_page = document.querySelector('body.woocommerce-checkout');
    if (is_checkout_page === null) {
      return false;
    }

    // There's no woo hook for that, so we need do that with js
    if (typeof jQuery === 'function') {
      jQuery(document).on('updated_checkout', function () {
        var shipping_totals_table_column = document.querySelector('#order_review .woocommerce-shipping-totals > td');
        if (shipping_totals_table_column !== null) {
          document.querySelector('#order_review .woocommerce-shipping-totals > td').setAttribute('colspan', 2);
        }
      });
    }
  },
  customizer: function customizer() {
    if (!window.parent.document.body.classList.contains('wp-customizer')) {
      return false;
    }
    window.onload = function () {
      var cart_count = document.querySelectorAll('.cart-count');
      if (cart_count.length) {
        jQuery(document.body).trigger('wc_fragment_refresh');
      }
    };
  }
};
botiga.helpers.botigaDomReady(function () {
  botiga.navigation.init();
  botiga.desktopOffcanvasNav.init();
  botiga.desktopOffCanvasToggleNav.init();
  botiga.headerSearch.init();
  botiga.customAddToCartButton.init();
  botiga.stickyHeader.init();
  botiga.scrollDirection.init();
  botiga.backToTop.init();
  botiga.qtyButton.init();
  botiga.carousel.init();
  botiga.collapse.init();
  botiga.tabsNav.init();
  botiga.misc.init();
});