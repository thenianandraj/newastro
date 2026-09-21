(function () {
	function unpinOverlayHeader() {
		document.querySelectorAll('.header_outside_slider').forEach(function (el) {
			el.classList.remove('header_outside_slider');
		});
	}

	function isDesktop() {
		return window.innerWidth >= 980;
	}

	function itemLabel(li) {
		var node = li.querySelector(':scope > a, :scope > span');
		return node ? (node.textContent || '').replace(/\s+/g, ' ').trim() : '';
	}

	function hideDesktopBack() {
		if (!isDesktop()) {
			restoreMobileControls();
			return;
		}
		document.querySelectorAll('.header_nav_part .main-menu:not(.mobile_menu) li.menu-item.back, .header_nav_part .main-menu:not(.mobile_menu) .button_open').forEach(function (el) {
			el.style.setProperty('display', 'none', 'important');
			el.setAttribute('aria-hidden', 'true');
		});
	}

	function restoreMobileControls() {
		document.querySelectorAll('ul.mobile_menu li.menu-item.back, ul.mobile_menu .button_open, .header_nav_part .main-menu .button_open, .header_nav_part .main-menu li.menu-item.back').forEach(function (el) {
			el.style.removeProperty('display');
			el.removeAttribute('aria-hidden');
		});
		document.querySelectorAll('ul.mobile_menu').forEach(unwrapPacked);
	}

	function fixLogos() {
		document.querySelectorAll('.header_logo_part .logo').forEach(function (wrap) {
			var sticky = wrap.querySelector('img.logo_sticky');
			var fallbackSrc = sticky && sticky.getAttribute('src') ? sticky.getAttribute('src') : '';
			Array.prototype.forEach.call(wrap.querySelectorAll('img'), function (img) {
				if (img.getAttribute('src')) {
					return;
				}
				var src = '';
				Array.prototype.forEach.call(img.attributes, function (attr) {
					if (!src && /^https?:\/\//i.test(attr.name)) {
						src = attr.name;
					}
				});
				if (!src) {
					src = fallbackSrc;
				}
				if (src) {
					img.setAttribute('src', src);
					img.classList.add('lh-logo-fixed');
				}
			});
			var inSticky = wrap.closest('.sticky_header');
			if (sticky && inSticky) {
				sticky.style.setProperty('display', 'inline-block', 'important');
				sticky.style.setProperty('visibility', 'visible', 'important');
				sticky.style.setProperty('opacity', '1', 'important');
			}
		});
	}

	function unwrapPacked(ul) {
		ul.querySelectorAll(':scope > .lh-mega-rest, :scope > .lh-mega-col').forEach(function (shell) {
			var list = shell.querySelector('ul');
			if (list) {
				while (list.firstChild) {
					ul.insertBefore(list.firstChild, shell);
				}
			}
			shell.remove();
		});
		ul.classList.remove('lh-mega-packed');
	}

	function categoryWeight(li) {
		return 2 + li.querySelectorAll('li.menu-item:not(.back)').length;
	}

	function makeCol(items, extraClass) {
		var shell = document.createElement('li');
		shell.className = 'lh-mega-col menu-item ' + extraClass;
		var list = document.createElement('ul');
		list.className = 'lh-mega-col-list';
		items.forEach(function (li) {
			list.appendChild(li);
		});
		shell.appendChild(list);
		return shell;
	}

	function visibleItems(ul) {
		return Array.prototype.filter.call(ul.children, function (child) {
			return child.classList.contains('menu-item') && !child.classList.contains('back') && !child.classList.contains('lh-mega-col');
		});
	}

	function restorePackedClass(li, ul) {
		var cols = ul.querySelectorAll(':scope > .lh-mega-col');
		if (cols.length >= 3) {
			li.classList.remove('lh-mega-cols-2');
			li.classList.add('lh-mega-wide');
			return true;
		}
		if (cols.length === 2) {
			li.classList.remove('lh-mega-wide');
			li.classList.add('lh-mega-cols-2');
			return true;
		}
		if (cols.length) {
			unwrapPacked(ul);
		}
		return false;
	}

	function splitByWeight(items, colCount, pinLife) {
		var cols = [];
		var weights = [];
		var i;
		for (i = 0; i < colCount; i++) {
			cols.push([]);
			weights.push(0);
		}
		var queue = items.slice();
		var start = 0;
		if (pinLife && colCount >= 3) {
			for (i = 0; i < queue.length; i++) {
				if (/^life\s*horoscope$/i.test(itemLabel(queue[i]))) {
					var life = queue.splice(i, 1)[0];
					cols[0].push(life);
					weights[0] += categoryWeight(life);
					start = 1;
					break;
				}
			}
		}
		queue.forEach(function (item) {
			var best = start;
			for (i = start; i < colCount; i++) {
				if (weights[i] < weights[best]) {
					best = i;
				}
			}
			cols[best].push(item);
			weights[best] += categoryWeight(item);
		});
		return cols.filter(function (col) {
			return col.length > 0;
		});
	}

	function applyColumns(li, ul, cols) {
		if (cols.length < 2) {
			li.classList.remove('lh-mega-wide', 'lh-mega-cols-2');
			return;
		}
		cols.forEach(function (items, index) {
			ul.appendChild(makeCol(items, 'lh-mega-col-' + (index + 1)));
		});
		ul.classList.add('lh-mega-packed');
		li.classList.remove('lh-mega-wide', 'lh-mega-cols-2');
		if (cols.length >= 3) {
			li.classList.add('lh-mega-wide');
		} else {
			li.classList.add('lh-mega-cols-2');
		}
	}

	function isSingleColumnMenu(li) {
		var label = itemLabel(li);
		if (/online/i.test(label) || /2026/.test(label)) {
			return true;
		}
		return /(^|\s)(menu-item-81937|menu-item-72595)(\s|$)/.test(li.className);
	}

	function isTwoColumnMenu(li) {
		var label = itemLabel(li);
		if (/^transits?$/i.test(label)) {
			return true;
		}
		return /(^|\s)menu-item-72921(\s|$)/.test(li.className);
	}

	function packMenu(li) {
		var ul = li.querySelector(':scope > ul.sub-menu');
		if (!ul) {
			return;
		}
		if (isSingleColumnMenu(li)) {
			unwrapPacked(ul);
			li.classList.remove('lh-mega-wide', 'lh-mega-cols-2');
			li.classList.add('lh-mega-one');
			return;
		}
		li.classList.remove('lh-mega-one');
		if (isTwoColumnMenu(li)) {
			if (ul.querySelectorAll(':scope > .lh-mega-col').length === 2) {
				li.classList.remove('lh-mega-wide');
				li.classList.add('lh-mega-cols-2');
				return;
			}
			unwrapPacked(ul);
			applyColumns(li, ul, splitByWeight(visibleItems(ul), 2, false));
			return;
		}
		if (restorePackedClass(li, ul)) {
			return;
		}
		var items = visibleItems(ul);
		if (!items.length) {
			return;
		}
		var folders = items.filter(function (item) {
			return item.classList.contains('menu-item-has-children');
		});
		var linkCount = ul.querySelectorAll('li.menu-item:not(.back)').length;
		var colCount = 1;
		if (folders.length >= 6 || linkCount >= 24) {
			colCount = 3;
		} else if (folders.length >= 2 || items.length >= 6 || linkCount >= 8) {
			colCount = 2;
		}
		if (colCount < 2) {
			li.classList.remove('lh-mega-wide', 'lh-mega-cols-2');
			return;
		}
		unwrapPacked(ul);
		items = visibleItems(ul);
		var pinLife = colCount >= 3;
		applyColumns(li, ul, splitByWeight(items, colCount, pinLife));
	}

	function packAllMenus() {
		if (!isDesktop()) {
			restoreMobileControls();
			return;
		}
		document.querySelectorAll('.header_nav_part ul.main-menu:not(.mobile_menu) > .menu-item-has-children').forEach(packMenu);
	}

	function ensureMobileClass() {
		if (isDesktop()) {
			return;
		}
		document.body.classList.add('cws_mobile');
	}

	var mobileHeaderHome = null;

	function disableMobileStickyClone() {
		document.querySelectorAll('.sticky_header').forEach(function (el) {
			if (isDesktop()) {
				el.style.removeProperty('display');
				el.style.removeProperty('pointer-events');
				el.style.removeProperty('visibility');
				el.style.removeProperty('height');
				return;
			}
			el.classList.add('sticky_mobile_off');
			el.classList.remove('sticky_active');
			el.style.setProperty('display', 'none', 'important');
			el.style.setProperty('pointer-events', 'none', 'important');
			el.style.setProperty('visibility', 'hidden', 'important');
		});
	}

	function headerBarHeight(cont) {
		if (!cont) {
			return 64;
		}
		var box = cont.querySelector('.menu_box');
		if (box && box.offsetHeight) {
			return box.offsetHeight;
		}
		var bar = cont.querySelector('.header_container');
		var menuWrap = cont.querySelector('.mobile_menu_wrapper');
		var h = bar ? bar.offsetHeight : cont.offsetHeight;
		if (menuWrap) {
			h -= menuWrap.offsetHeight;
		}
		return h > 48 ? h : 64;
	}

	function applyPinnedHeaderStyles(cont) {
		var desired = document.body.classList.contains('admin-bar') ? 46 : 0;
		cont.style.setProperty('position', 'fixed', 'important');
		cont.style.setProperty('left', '0px', 'important');
		cont.style.setProperty('right', '0px', 'important');
		cont.style.setProperty('bottom', 'auto', 'important');
		cont.style.setProperty('width', '100%', 'important');
		cont.style.setProperty('height', 'auto', 'important');
		cont.style.setProperty('max-height', 'none', 'important');
		cont.style.setProperty('margin', '0px', 'important');
		cont.style.setProperty('transform', 'none', 'important');
		cont.style.setProperty('z-index', '1000000', 'important');
		cont.style.setProperty('background', '#fff', 'important');
		cont.style.setProperty('top', desired + 'px', 'important');
		var rect = cont.getBoundingClientRect();
		if (Math.abs(rect.top - desired) > 1) {
			var used = parseFloat(cont.style.top);
			if (isNaN(used)) {
				used = desired;
			}
			cont.style.setProperty('top', (used + desired - rect.top) + 'px', 'important');
		}
	}

	function clearPinnedHeaderStyles(cont) {
		['position', 'top', 'left', 'right', 'bottom', 'width', 'height', 'max-height', 'margin', 'transform', 'z-index', 'background'].forEach(function (prop) {
			cont.style.removeProperty(prop);
		});
	}

	function unpinMobileHeader() {
		var cont = document.querySelector('.header_cont.lh-mobile-pinned');
		if (!cont) {
			document.documentElement.style.removeProperty('--lh-mobile-header-h');
			return;
		}
		clearPinnedHeaderStyles(cont);
		cont.classList.remove('lh-mobile-pinned');
		if (mobileHeaderHome && mobileHeaderHome.parent) {
			if (mobileHeaderHome.ph && mobileHeaderHome.ph.parentNode) {
				mobileHeaderHome.ph.parentNode.removeChild(mobileHeaderHome.ph);
			}
			if (mobileHeaderHome.next && mobileHeaderHome.next.parentNode === mobileHeaderHome.parent) {
				mobileHeaderHome.parent.insertBefore(cont, mobileHeaderHome.next);
			} else {
				mobileHeaderHome.parent.appendChild(cont);
			}
		}
		mobileHeaderHome = null;
		document.documentElement.style.removeProperty('--lh-mobile-header-h');
	}

	function pinMobileHeaderToViewport() {
		if (isDesktop()) {
			unpinMobileHeader();
			return;
		}
		var cont = document.querySelector('.header_wrapper_container .header_cont') || document.querySelector('body > .header_cont.lh-mobile-pinned');
		if (!cont) {
			return;
		}
		if (!cont.classList.contains('lh-mobile-pinned')) {
			mobileHeaderHome = {
				parent: cont.parentNode,
				next: cont.nextSibling,
				ph: document.createElement('div')
			};
			mobileHeaderHome.ph.className = 'lh-mobile-header-ph';
			mobileHeaderHome.ph.setAttribute('aria-hidden', 'true');
			cont.parentNode.insertBefore(mobileHeaderHome.ph, cont);
			var adminBar = document.getElementById('wpadminbar');
			if (adminBar && adminBar.parentNode === document.body) {
				document.body.insertBefore(cont, adminBar.nextSibling);
			} else {
				document.body.insertBefore(cont, document.body.firstChild);
			}
			cont.classList.add('lh-mobile-pinned');
		}
		var h = Math.round(headerBarHeight(cont));
		document.documentElement.style.setProperty('--lh-mobile-header-h', h + 'px');
		if (mobileHeaderHome && mobileHeaderHome.ph) {
			mobileHeaderHome.ph.style.height = h + 'px';
		}
		applyPinnedHeaderStyles(cont);
	}

	function bindMobileParentToggle() {
		if (bindMobileParentToggle.bound) {
			return;
		}
		bindMobileParentToggle.bound = true;

		function mobileSubmenu(li) {
			if (!li) {
				return null;
			}
			return li.querySelector(':scope > ul.sub-menu') || li.querySelector(':scope > ul');
		}

		function toggleMobileItem(li) {
			var sub = mobileSubmenu(li);
			if (!sub) {
				return;
			}
			var open = li.classList.contains('lh-open');
			if (open) {
				li.classList.remove('lh-open', 'active');
				sub.style.setProperty('display', 'none', 'important');
			} else {
				li.classList.add('lh-open', 'active');
				sub.style.setProperty('display', 'block', 'important');
				sub.style.setProperty('height', 'auto', 'important');
				sub.style.setProperty('max-height', 'none', 'important');
				sub.style.setProperty('visibility', 'visible', 'important');
				sub.style.setProperty('opacity', '1', 'important');
				sub.style.setProperty('position', 'static', 'important');
				sub.style.setProperty('overflow', 'visible', 'important');
			}
		}

		function isParentLabel(a) {
			var row = a.closest('.menu_row');
			var li = a.closest('li.menu-item-has-children');
			if (!li || !mobileSubmenu(li)) {
				return null;
			}
			if (row && row.parentNode === li && row.contains(a)) {
				return li;
			}
			if (!row && a.parentNode === li) {
				return li;
			}
			return null;
		}

		document.addEventListener('click', function (e) {
			if (isDesktop()) {
				return;
			}
			var menuRoot = e.target.closest('ul.mobile_menu');
			if (!menuRoot) {
				return;
			}
			var btn = e.target.closest('.button_open');
			if (btn && menuRoot.contains(btn)) {
				e.preventDefault();
				e.stopImmediatePropagation();
				toggleMobileItem(btn.closest('li.menu-item-has-children'));
				return;
			}
			var a = e.target.closest('a');
			if (!a || !menuRoot.contains(a)) {
				return;
			}
			var parentLi = isParentLabel(a);
			if (parentLi) {
				e.preventDefault();
				e.stopImmediatePropagation();
				toggleMobileItem(parentLi);
				return;
			}
			var href = (a.getAttribute('href') || '').trim();
			if (href && href !== '#') {
				e.preventDefault();
				e.stopImmediatePropagation();
				window.location.href = a.href;
			}
		}, true);
	}

	function init() {
		ensureMobileClass();
		disableMobileStickyClone();
		pinMobileHeaderToViewport();
		unpinOverlayHeader();
		fixLogos();
		hideDesktopBack();
		packAllMenus();
		bindMobileParentToggle();
	}

	window.addEventListener('resize', function () {
		ensureMobileClass();
		disableMobileStickyClone();
		pinMobileHeaderToViewport();
		if (!isDesktop()) {
			restoreMobileControls();
		}
	});
	window.addEventListener('scroll', function () {
		if (!isDesktop()) {
			disableMobileStickyClone();
			var pinned = document.querySelector('.header_cont.lh-mobile-pinned');
			if (pinned) {
				applyPinnedHeaderStyles(pinned);
			} else {
				pinMobileHeaderToViewport();
			}
		}
	}, { passive: true });
	document.addEventListener('click', function (e) {
		if (isDesktop()) {
			return;
		}
		if (e.target.closest('.mobile_menu_switcher, .mobile_menu_hamburger')) {
			window.setTimeout(pinMobileHeaderToViewport, 320);
		}
	});

	unpinOverlayHeader();
	fixLogos();
	ensureMobileClass();
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
	window.addEventListener('load', init);
	setTimeout(init, 400);
	setTimeout(init, 1200);
})();
