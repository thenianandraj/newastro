/**
 * Design System Extensions
 *
 * This script extends the design system library functionality.
 *
 * Extensions included:
 * - Help widget: Extends widget selector to support all basic plugins (order, product, user)
 */
(function($) {
	'use strict';

	if (window.wt_ds_help_widget_extensions_initialized) {
		return;
	}
	window.wt_ds_help_widget_extensions_initialized = true;

	var HELP_WIDGET_SELECTOR = '.wbte_oimpexp_help-widget, .wbte_pimpexp_help-widget, .wbte_uimpexp_help-widget';

	function isHelpWidgetDocumentClickHandler(fn) {
		if (typeof fn !== 'function') {
			return false;
		}

		var handlerStr = fn.toString();
		return handlerStr.indexOf('wt_ds_help-widget_hidden_checkbox') !== -1 &&
			(
				handlerStr.indexOf('wbte_oimpexp_help-widget') !== -1 ||
				handlerStr.indexOf('wbte_pimpexp_help-widget') !== -1 ||
				handlerStr.indexOf('wbte_uimpexp_help-widget') !== -1
			);
	}

	function removeLegacyHelpWidgetClickHandlers() {
		try {
			var events = $._data(document, 'events');
			if (!events || !events.click) {
				return;
			}

			events.click = events.click.filter(function(handler) {
				return !(handler && handler.handler && isHelpWidgetDocumentClickHandler(handler.handler));
			});
		} catch (err) {}
	}

	function getHelpWidgetObjects() {
		var widgetObjects = [];
		if (typeof wbte_oimpexp_help_widget !== 'undefined') {
			widgetObjects.push(wbte_oimpexp_help_widget);
		}
		if (typeof wbte_pimpexp_help_widget !== 'undefined') {
			widgetObjects.push(wbte_pimpexp_help_widget);
		}
		if (typeof wbte_uimpexp_help_widget !== 'undefined') {
			widgetObjects.push(wbte_uimpexp_help_widget);
		}
		return widgetObjects;
	}

	function bindWidgetClickGuards() {
		$(HELP_WIDGET_SELECTOR).each(function() {
			var $widget = $(this);

			$widget.off('click.wt_ds_help_widget_guard').on('click.wt_ds_help_widget_guard', function(e) {
				if ($(e.target).closest('a').length) {
					return;
				}
				e.stopPropagation();
			});
		});
	}

	function bindUnifiedHelpWidgetHandler() {
		$(document).off('click.wt_ds_help_widget').on('click.wt_ds_help_widget', function(e) {
			var allWidgets = $(HELP_WIDGET_SELECTOR);
			var clickInsideWidget = false;

			allWidgets.each(function() {
				var $widget = $(this);
				if ($widget.is(e.target) || $widget.has(e.target).length) {
					clickInsideWidget = true;
					return false;
				}
			});

			if (!clickInsideWidget) {
				allWidgets.each(function() {
					var $checkbox = $(this).find('#wt_ds_help-widget_hidden_checkbox');
					if ($checkbox.length && $checkbox.is(':checked')) {
						$checkbox.prop('checked', false);
					}
				});
			}
		});

		bindWidgetClickGuards();
	}

	window.wt_ds_unified_help_widget_set = function() {
		removeLegacyHelpWidgetClickHandlers();
		bindUnifiedHelpWidgetHandler();
	};

	function applyHelpWidgetFix() {
		var widgetObjects = getHelpWidgetObjects();

		$.each(widgetObjects, function(index, widgetObj) {
			widgetObj.Set = window.wt_ds_unified_help_widget_set;
		});

		window.wt_ds_unified_help_widget_set();
	}

	// The design-system library loads after this file and re-binds its own document-level click
	// handler for the help widget, which re-introduces the bug this extension is fixing. We
	// intercept $.fn.on globally only to detect that specific late-bound handler (matched by the
	// hidden-checkbox + widget-class string heuristic in isHelpWidgetDocumentClickHandler) and
	// swap in our unified handler instead. All other `.on()` calls fall through untouched.
	var originalOn = $.fn.on;
	$.fn.on = function(types, selector, data, handler) {
		if (this.length && this[0] === document && typeof types === 'string' && types.indexOf('click') !== -1) {
			var fn = null;
			if (typeof selector === 'function') {
				fn = selector;
			} else if (typeof data === 'function') {
				fn = data;
			} else if (typeof handler === 'function') {
				fn = handler;
			}

			if (fn && isHelpWidgetDocumentClickHandler(fn)) {
				applyHelpWidgetFix();
				return this;
			}
		}

		return originalOn.apply(this, arguments);
	};

	$.each(getHelpWidgetObjects(), function(index, widgetObj) {
		if (widgetObj.Set && typeof widgetObj.Set === 'function') {
			widgetObj._originalSet = widgetObj.Set;
			widgetObj.Set = function() {};
		}
	});

	function initHelpWidgetExtensions() {
		applyHelpWidgetFix();
	}

	$(document).ready(function() {
		initHelpWidgetExtensions();
		setTimeout(initHelpWidgetExtensions, 300);
	});

	$(window).on('load', initHelpWidgetExtensions);
})(jQuery);
