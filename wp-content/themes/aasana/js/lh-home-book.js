(function () {
	function section() {
		var head = document.querySelector('.lh-svc-head');
		if (head) {
			return head.closest('.vc_row') || head;
		}
		return document.getElementById('our-services');
	}

	function headerOffset() {
		var bar = document.querySelector('.sticky_header');
		if (!bar) {
			return 96;
		}
		var box = bar.getBoundingClientRect();
		var height = Math.round(box.height);
		if (height < 40) {
			return 96;
		}
		return height + 16;
	}

	function go(event) {
		var el = section();
		if (!el) {
			return;
		}
		if (event) {
			event.preventDefault();
		}
		var top = el.getBoundingClientRect().top + window.pageYOffset - headerOffset();
		window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
	}

	function bind() {
		var btn = document.querySelector('.lh-home-hero__btn--primary');
		if (!btn || btn.getAttribute('data-lh-book') === '1') {
			return;
		}
		btn.setAttribute('data-lh-book', '1');
		btn.setAttribute('href', '#our-services');
		btn.addEventListener('click', go);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', bind);
	} else {
		bind();
	}
})();
