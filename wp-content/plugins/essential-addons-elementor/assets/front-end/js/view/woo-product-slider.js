/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/woo-product-slider.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/woo-product-slider.js":
/*!*******************************************!*\
  !*** ./src/js/view/woo-product-slider.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) { symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); } keys.push.apply(keys, symbols); } return keys; }\n\nfunction _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }\n\nfunction _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }\n\nea.hooks.addAction(\"init\", \"ea\", function () {\n  var wooProductSlider = function wooProductSlider($scope, $) {\n    var $wooProductSlider = $scope.find(\".eael-woo-product-slider\").eq(0),\n        $type = $wooProductSlider.data(\"type\"),\n        $autoplay = $wooProductSlider.data(\"autoplay\") !== undefined ? $wooProductSlider.data(\"autoplay\") : 999999,\n        $pagination = $wooProductSlider.data(\"pagination\") !== undefined ? $wooProductSlider.data(\"pagination\") : \".swiper-pagination\",\n        $arrow_next = $wooProductSlider.data(\"arrow-next\") !== undefined ? $wooProductSlider.data(\"arrow-next\") : \".swiper-button-next\",\n        $arrow_prev = $wooProductSlider.data(\"arrow-prev\") !== undefined ? $wooProductSlider.data(\"arrow-prev\") : \".swiper-button-prev\",\n        $speed = $wooProductSlider.data(\"speed\") !== undefined ? $wooProductSlider.data(\"speed\") : 400,\n        $loop = $wooProductSlider.data(\"loop\") !== undefined ? $wooProductSlider.data(\"loop\") : 0,\n        $grab_cursor = $wooProductSlider.data(\"grab-cursor\") !== undefined ? $wooProductSlider.data(\"grab-cursor\") : 0,\n        $pause_on_hover = $wooProductSlider.data(\"pause-on-hover\") !== undefined ? $wooProductSlider.data(\"pause-on-hover\") : \"\",\n        $content_effect = $wooProductSlider.data(\"animation\") !== undefined ? $wooProductSlider.data(\"animation\") : \"zoomIn\",\n        $showEffect = $wooProductSlider.data(\"show-effect\") !== undefined ? $wooProductSlider.data(\"show-effect\") : \"\";\n    var $sliderOptions = {\n      direction: \"horizontal\",\n      speed: $speed,\n      //effect: \"slide\",\n      centeredSlides: true,\n      grabCursor: $grab_cursor,\n      autoHeight: true,\n      loop: $loop,\n      //slidesPerGroup: 3,\n      loopedSlides: 3,\n      autoplay: {\n        delay: $autoplay,\n        disableOnInteraction: false\n      },\n      pagination: {\n        el: $pagination,\n        clickable: true\n      },\n      navigation: {\n        nextEl: $arrow_next,\n        prevEl: $arrow_prev\n      },\n      slidesPerView: 1,\n      spaceBetween: 30\n    };\n\n    if ($showEffect === 'yes') {\n      // $carouselOptions.slidesPerView = 'auto';\n      $sliderOptions.on = {\n        init: function init() {\n          $wooProductSlider.find('.swiper-slide-active .product-details-wrap').addClass('animate__animated' + ' animate__' + $content_effect);\n        },\n        transitionStart: function transitionStart() {\n          $wooProductSlider.find('.product-details-wrap').removeClass('animate__animated animate__' + $content_effect);\n        },\n        transitionEnd: function transitionEnd(swiper) {\n          $wooProductSlider.find('.swiper-slide-active .product-details-wrap').addClass('animate__animated' + ' animate__' + $content_effect);\n        }\n      };\n    }\n\n    swiperLoader($wooProductSlider, $sliderOptions).then(function (eaelwooProductSlider) {\n      if ($autoplay === 0) {\n        eaelwooProductSlider.autoplay.stop();\n      }\n\n      if ($pause_on_hover && $autoplay !== 0) {\n        $wooProductSlider.on(\"mouseenter\", function () {\n          eaelwooProductSlider.autoplay.stop();\n        });\n        $wooProductSlider.on(\"mouseleave\", function () {\n          eaelwooProductSlider.autoplay.start();\n        });\n      } //gallery pagination\n\n\n      var $paginationGallerySelector = $scope.find('.eael-woo-product-slider-container .eael-woo-product-slider-gallary-pagination').eq(0);\n\n      if ($paginationGallerySelector.length > 0) {\n        swiperLoader($paginationGallerySelector, {\n          spaceBetween: 20,\n          centeredSlides: true,\n          touchRatio: 0.2,\n          slideToClickedSlide: true,\n          loop: $loop,\n          //slidesPerGroup: 1,\n          loopedSlides: 3,\n          slidesPerView: 3,\n          freeMode: true,\n          watchSlidesVisibility: true,\n          watchSlidesProgress: true\n        }).then(function ($paginationGallerySlider) {\n          eaelwooProductSlider.controller.control = $paginationGallerySlider;\n          $paginationGallerySlider.controller.control = eaelwooProductSlider;\n        });\n      }\n    }); // Quick view\n\n    $scope.on(\"click\", \".open-popup-link\", function (e) {\n      e.preventDefault();\n      e.stopPropagation();\n      var $this = $(this);\n      var quickview_setting = $this.data('quickview-setting');\n      var popup_view = $(\".eael-woocommerce-popup-view\");\n      popup_view.find(\".eael-popup-details-render\").html('<div class=\"eael-preloader\"></div>');\n      popup_view.addClass(\"eael-product-popup-ready\").removeClass(\"eael-product-modal-removing\");\n      popup_view.show();\n      $.ajax({\n        url: localize.ajaxurl,\n        type: \"post\",\n        data: _objectSpread(_objectSpread({\n          action: \"eael_product_quickview_popup\"\n        }, quickview_setting), {}, {\n          security: localize.nonce\n        }),\n        success: function success(response) {\n          if (response.success) {\n            var product_preview = $(response.data);\n            var popup_details = product_preview.children(\".eael-product-popup-details\");\n            popup_details.find(\".variations_form\").wc_variation_form();\n            var popup_view_render = popup_view.find(\".eael-popup-details-render\");\n            popup_view.find(\".eael-popup-details-render\").html(popup_details);\n            var product_gallery = popup_view.find(\".woocommerce-product-gallery\");\n            product_gallery.css(\"opacity\", 1);\n            popup_view_render.addClass(\"elementor-\" + quickview_setting.page_id);\n            popup_view_render.children().addClass(\"elementor-element elementor-element-\" + quickview_setting.widget_id); // $(window).resize(function() {\n\n            if ($(window).width() > 1024) {\n              // your code\n              if (popup_details.height() > 400) {\n                popup_details.css(\"height\", \"75vh\");\n              } else {\n                popup_details.css(\"height\", \"auto\");\n              }\n            }\n\n            if ($(window).width() <= 767 && $(window).width() > 320) {\n              if (popup_details.height() > 400) {\n                popup_details.css(\"height\", \"75vh\");\n              } else {\n                popup_details.css(\"height\", \"auto\");\n              }\n            } // });\n\n\n            setTimeout(function () {\n              product_gallery.wc_product_gallery();\n            }, 1000);\n          }\n        }\n      });\n    });\n    $(document).on(\"keypress\", \".eael-product-details-wrap input[type=number]\", function (e) {\n      var keyValue = e.keyCode || e.which;\n      var regex = /^[0-9]+$/;\n      var isValid = regex.test(String.fromCharCode(keyValue));\n\n      if (!isValid) {\n        return false;\n      }\n\n      return isValid;\n    }); // handle add to cart for quick view\n\n    $(document).on(\"click\", \".eael-woo-slider-popup .single_add_to_cart_button\", function (e) {\n      e.preventDefault();\n      e.stopImmediatePropagation();\n      var $this = $(this),\n          product_id = $(this).val(),\n          variation_id = $this.closest(\"form.cart\").find('input[name=\"variation_id\"]').val() || \"\",\n          quantity = $this.closest(\"form.cart\").find('input[name=\"quantity\"]').val(),\n          items = $this.closest(\"form.cart.grouped_form\"),\n          form = $this.closest(\"form.cart\"),\n          product_data = [];\n      items = items.serializeArray();\n\n      if (form.hasClass(\"variations_form\")) {\n        product_id = form.find('input[name=\"product_id\"]').val();\n      }\n\n      if (items.length > 0) {\n        items.forEach(function (item, index) {\n          var p_id = parseInt(item.name.replace(/[^\\d.]/g, \"\"), 10);\n\n          if (item.name.indexOf(\"quantity[\") >= 0 && item.value != \"\" && p_id > 0) {\n            product_data[product_data.length] = {\n              product_id: p_id,\n              quantity: item.value,\n              variation_id: 0\n            };\n          }\n        });\n      } else {\n        product_data[0] = {\n          product_id: product_id,\n          quantity: quantity,\n          variation_id: variation_id\n        };\n      }\n\n      $this.removeClass(\"eael-addtocart-added\");\n      $this.addClass(\"eael-addtocart-loading\");\n      $.ajax({\n        url: localize.ajaxurl,\n        type: \"post\",\n        data: {\n          action: \"eael_product_add_to_cart\",\n          product_data: product_data,\n          eael_add_to_cart_nonce: localize.nonce,\n          cart_item_data: form.serializeArray()\n        },\n        success: function success(response) {\n          if (response.success) {\n            $(document.body).trigger(\"wc_fragment_refresh\");\n            $this.removeClass(\"eael-addtocart-loading\");\n            $this.addClass(\"eael-addtocart-added\");\n          }\n        }\n      });\n    });\n    $(document).on(\"click\", \".eael-product-popup-close\", function (event) {\n      event.stopPropagation();\n      $(\".eael-product-popup\").addClass(\"eael-product-modal-removing\").removeClass(\"eael-product-popup-ready\");\n    });\n    $(document).on(\"click\", function (event) {\n      if (event.target.closest(\".eael-product-popup-details\")) return;\n      $(\".eael-product-popup.eael-product-zoom-in.eael-product-popup-ready\").addClass(\"eael-product-modal-removing\").removeClass(\"eael-product-popup-ready\"); //\n    });\n\n    if (isEditMode) {\n      $(\".eael-product-image-wrap .woocommerce-product-gallery\").css(\"opacity\", \"1\");\n    }\n\n    var eael_popup = $(document).find(\".eael-woocommerce-popup-view\");\n\n    if (eael_popup.length < 1) {\n      eael_add_popup();\n    }\n\n    function eael_add_popup() {\n      var markup = \"<div style=\\\"display: none\\\" class=\\\"eael-woocommerce-popup-view eael-product-popup eael-product-zoom-in woocommerce\\\">\\n                    <div class=\\\"eael-product-modal-bg\\\"></div>\\n                    <div class=\\\"eael-popup-details-render eael-woo-slider-popup\\\"><div class=\\\"eael-preloader\\\"></div></div>\\n                </div>\";\n      $(\"body\").append(markup);\n    }\n  };\n\n  var swiperLoader = function swiperLoader(swiperElement, swiperConfig) {\n    if ('undefined' === typeof Swiper) {\n      var asyncSwiper = elementorFrontend.utils.swiper;\n      return new asyncSwiper(swiperElement, swiperConfig).then(function (newSwiperInstance) {\n        return newSwiperInstance;\n      });\n    } else {\n      return swiperPromise(swiperElement, swiperConfig);\n    }\n  };\n\n  var swiperPromise = function swiperPromise(swiperElement, swiperConfig) {\n    return new Promise(function (resolve, reject) {\n      var swiperInstance = new Swiper(swiperElement, swiperConfig);\n      resolve(swiperInstance);\n    });\n  };\n\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-woo-product-slider.default\", wooProductSlider);\n});\n\n//# sourceURL=webpack:///./src/js/view/woo-product-slider.js?");

/***/ })

/******/ });