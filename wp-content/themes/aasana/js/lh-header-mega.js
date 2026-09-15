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
			return;
		}
		document.querySelectorAll('.header_nav_part .main-menu li.menu-item.back, .header_nav_part .button_open').forEach(function (el) {
			el.style.setProperty('display', 'none', 'important');
			el.setAttribute('aria-hidden', 'true');
		});
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
			return;
		}
		document.querySelectorAll('.header_nav_part ul.main-menu:not(.mobile_menu) > .menu-item-has-children').forEach(packMenu);
	}

	function init() {
		unpinOverlayHeader();
		fixLogos();
		hideDesktopBack();
		packAllMenus();
	}

	unpinOverlayHeader();
	fixLogos();
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
	window.addEventListener('load', init);
	setTimeout(init, 400);
	setTimeout(init, 1200);
})();
