(function () {
	function isDesktopNav(el) {
		return el && !el.closest('.mobile_menu_wrapper') && !document.body.classList.contains('cws_mobile');
	}

	document.addEventListener('click', function (e) {
		var a = e.target.closest('.header_nav_part .main-menu > .menu-item > .sub-menu > .menu-item > a');
		if (!a || !isDesktopNav(a)) {
			return;
		}
		var href = a.getAttribute('href');
		if (href === '#' || href === '' || href === null) {
			e.preventDefault();
		}
	});

	function hideDesktopBack() {
		if (document.body.classList.contains('cws_mobile')) {
			return;
		}
		document.querySelectorAll('.header_nav_part .main-menu li.menu-item.back').forEach(function (el) {
			el.style.setProperty('display', 'none', 'important');
			el.setAttribute('aria-hidden', 'true');
		});
		document.querySelectorAll('.header_nav_part .button_open').forEach(function (el) {
			el.style.setProperty('display', 'none', 'important');
			el.setAttribute('aria-hidden', 'true');
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

	function packHoroscopeMega() {
		if (document.body.classList.contains('cws_mobile')) {
			return;
		}
		document.querySelectorAll('.header_nav_part .main-menu > .menu-item-2009 > ul.sub-menu').forEach(function (ul) {
			if (ul.querySelector(':scope > .lh-mega-col-1')) {
				return;
			}
			unwrapPacked(ul);
			var life = null;
			var rest = [];
			Array.prototype.forEach.call(ul.children, function (li) {
				if (li.classList.contains('back')) {
					return;
				}
				if (li.classList.contains('menu-item-54057')) {
					life = li;
				} else {
					rest.push(li);
				}
			});
			if (!life || !rest.length) {
				return;
			}

			var col2 = [];
			var col3 = [];
			var w2 = 0;
			var w3 = 0;
			rest.forEach(function (li) {
				var w = categoryWeight(li);
				if (w2 <= w3) {
					col2.push(li);
					w2 += w;
				} else {
					col3.push(li);
					w3 += w;
				}
			});

			ul.appendChild(makeCol([life], 'lh-mega-col-1'));
			ul.appendChild(makeCol(col2, 'lh-mega-col-2'));
			ul.appendChild(makeCol(col3, 'lh-mega-col-3'));
			ul.classList.add('lh-mega-packed');
		});
	}

	function init() {
		hideDesktopBack();
		packHoroscopeMega();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
	window.addEventListener('load', init);
	setTimeout(init, 400);
	setTimeout(init, 1200);
})();
