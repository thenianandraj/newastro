(function () {
	function nextVcRow(from) {
		var node = from;
		while (node) {
			var sib = node.nextElementSibling;
			while (sib) {
				if (sib.classList && sib.classList.contains('vc_row')) {
					return sib;
				}
				var inner = sib.querySelector ? sib.querySelector('.vc_row') : null;
				if (inner) {
					return inner;
				}
				sib = sib.nextElementSibling;
			}
			node = node.parentElement;
			if (node && node.id === 'main') {
				break;
			}
		}
		return null;
	}

	function hideOldSlider() {
		var sliders = document.querySelectorAll('ss3-force-full-width, .n2-ss-slider, .n2-section-smartslider, #n2-ss-2, #n2-ss-2-align, .rev_slider_wrapper');
		for (var s = 0; s < sliders.length; s++) {
			if (sliders[s].closest && sliders[s].closest('.lh-why-trust, .lh-why-trust-row')) {
				continue;
			}
			sliders[s].style.setProperty('display', 'none', 'important');
			sliders[s].style.setProperty('height', '0', 'important');
		}
	}

	function expandWhyTrust(mod) {
		var hidden = mod.querySelectorAll('.read_div');
		for (var h = 0; h < hidden.length; h++) {
			hidden[h].style.setProperty('display', 'block', 'important');
			hidden[h].removeAttribute('hidden');
			hidden[h].setAttribute('aria-hidden', 'false');
		}
		var btns = mod.querySelectorAll('.read-link, .rmwr-wrapper > button');
		for (var b = 0; b < btns.length; b++) {
			btns[b].style.setProperty('display', 'none', 'important');
		}
	}

	function mark() {
		hideOldSlider();
		var mods = document.querySelectorAll('.cws_textmodule');
		for (var i = 0; i < mods.length; i++) {
			var title = mods[i].querySelector('.widgettitle');
			if (!title || !/why\s*trust\s*us/i.test(title.textContent || '')) {
				continue;
			}
			mods[i].classList.add('lh-why-trust');
			expandWhyTrust(mods[i]);
			var row = mods[i].closest('.vc_row');
			if (row) {
				row.classList.add('lh-why-trust-row');
				var stats = nextVcRow(row);
				if (stats) {
					stats.classList.add('lh-why-trust-stats');
				}
			}
		}

		var svcMods = document.querySelectorAll('.cws_textmodule');
		for (var j = 0; j < svcMods.length; j++) {
			var st = svcMods[j].querySelector('.widgettitle');
			var label = st ? (st.textContent || '').replace(/\s+/g, ' ').trim() : '';
			if (label !== 'Our Services') {
				continue;
			}
			svcMods[j].classList.add('lh-svc-head');
			var headRow = svcMods[j].closest('.vc_row');
			if (headRow) {
				headRow.classList.add('lh-svc-head-row');
			}
			var grids = document.querySelectorAll('#our-services');
			for (var g = 0; g < grids.length; g++) {
				if (!/our services in detail/i.test(grids[g].textContent || '')) {
					grids[g].classList.add('lh-svc-grid');
				}
			}
			var grid = grids[0] || (headRow ? nextVcRow(headRow) : null);
			var rowEl = grid;
			while (rowEl && rowEl.classList && rowEl.classList.contains('vc_row')) {
				if (/our services in detail/i.test(rowEl.textContent || '')) {
					break;
				}
				if (rowEl.querySelector('.cws_vc_shortcode_button, .wpb_single_image')) {
					rowEl.classList.add('lh-svc-grid');
					rowEl = nextVcRow(rowEl);
					continue;
				}
				break;
			}
		}

		var aboutMods = document.querySelectorAll('.cws_textmodule');
		for (var a = 0; a < aboutMods.length; a++) {
			var at = aboutMods[a].querySelector('.widgettitle');
			var aboutLabel = at ? (at.textContent || '').replace(/\s+/g, ' ').trim() : '';
			if (aboutLabel !== 'About Us') {
				continue;
			}
			aboutMods[a].classList.add('lh-about');
			var titles = aboutMods[a].querySelector('.cws_textmodule_titles');
			if (titles && !aboutMods[a].querySelector('.lh-about-lead')) {
				var lead = document.createElement('p');
				lead.className = 'lh-about-lead';
				lead.innerHTML = 'Guiding Lives with <em>Ancient Wisdom &amp; Modern Insight</em>';
				titles.appendChild(lead);
			}
			var aboutInner = aboutMods[a].closest('.vc_row.vc_inner') || aboutMods[a].closest('.vc_row');
			if (aboutInner) {
				aboutInner.classList.add('lh-about-grid');
				var cols = aboutInner.children;
				for (var c = 0; c < cols.length; c++) {
					if (!cols[c].classList || !cols[c].classList.contains('wpb_column')) {
						continue;
					}
					if (cols[c].querySelector('.lh-about')) {
						cols[c].classList.add('lh-about-copy-col');
					}
					if (cols[c].querySelector('.wpb_single_image')) {
						cols[c].classList.add('lh-about-media-col');
					}
				}
			}
			var aboutOuter = aboutMods[a].closest('.vc_row:not(.vc_inner)');
			if (aboutOuter) {
				aboutOuter.classList.add('lh-about-row');
			}
			var aboutImg = aboutInner ? aboutInner.querySelector('.wpb_single_image') : null;
			if (aboutImg) {
				aboutImg.classList.add('lh-about-media');
			}
			var aboutCol = aboutInner ? aboutInner.closest('.wpb_column') : null;
			if (aboutCol) {
				var sibCol = aboutCol.nextElementSibling;
				while (sibCol) {
					if (/hear from our clients/i.test(sibCol.textContent || '')) {
						break;
					}
					if (sibCol.querySelector && sibCol.querySelector('.wpb_text_column, h2, h3')) {
						sibCol.classList.add('lh-about-follow');
					}
					sibCol = sibCol.nextElementSibling;
				}
			}
			var follow = aboutOuter || aboutInner;
			while (follow) {
				follow = nextVcRow(follow);
				if (!follow) {
					break;
				}
				if (/hear from our clients/i.test(follow.textContent || '') && !/best astrologer in india/i.test(follow.textContent || '')) {
					break;
				}
				var bestBlock = follow.querySelector('.wpb_text_column');
				if (bestBlock && /best astrologer in india/i.test(bestBlock.textContent || '')) {
					bestBlock.classList.add('lh-best-astro');
				}
				if (/hear from our clients/i.test(follow.textContent || '')) {
					break;
				}
				follow.classList.add('lh-about-follow');
			}
		}

		var bestCols = document.querySelectorAll('.wpb_text_column');
		for (var t = 0; t < bestCols.length; t++) {
			if (!/best astrologer in india/i.test(bestCols[t].textContent || '')) {
				continue;
			}
			bestCols[t].classList.add('lh-best-astro');
		}

		markReviews();
		markVideosAndFaqs();
	}

	function hideWidgetWriteReview() {
		var btns = document.querySelectorAll('.ti-header-write-btn, .ti-header-write-btn-container');
		for (var i = 0; i < btns.length; i++) {
			btns[i].style.setProperty('display', 'none', 'important');
		}
	}

	function addReviewsButton() {
		if (document.querySelector('.lh-reviews-write')) {
			return true;
		}
		var href = 'https://admin.trustindex.io/api/googleWriteReview?place-id=ChIJwW-C4q5lUjoRlSBvZ2gV-XA';
		var existing = document.querySelector('a.ti-header-write-btn');
		if (existing && existing.getAttribute('href')) {
			href = existing.getAttribute('href');
		}
		var mount = document.querySelector('[data-template-id="trustindex-google-widget-html"]');
		if (!mount) {
			mount = document.querySelector('.ti-widget');
		}
		if (!mount) {
			return false;
		}
		var host = mount.closest('.wpb_raw_html, .wpb_raw_code');
		if (!host) {
			host = mount.parentElement;
		}
		if (!host) {
			return false;
		}
		var wrap = document.createElement('div');
		wrap.className = 'lh-reviews-write';
		wrap.innerHTML = '<a class="lh-reviews-write__btn" href="' + href + '" target="_blank" rel="noopener">Write a review</a>';
		host.parentNode.insertBefore(wrap, host.nextSibling);
		return true;
	}

	function markReviews() {
		var mods = document.querySelectorAll('.cws_textmodule');
		for (var r = 0; r < mods.length; r++) {
			var rt = mods[r].querySelector('.widgettitle');
			var rlabel = rt ? (rt.textContent || '').replace(/\s+/g, ' ').trim() : '';
			if (rlabel !== 'Hear From Our Clients') {
				continue;
			}
			mods[r].classList.add('lh-reviews');
			var rrow = mods[r].closest('.vc_row');
			if (rrow) {
				rrow.classList.add('lh-reviews-row');
			}
		}
		hideWidgetWriteReview();
		addReviewsButton();
	}

	function markVideosAndFaqs() {
		var mods = document.querySelectorAll('.cws_textmodule');
		for (var i = 0; i < mods.length; i++) {
			var title = mods[i].querySelector('.widgettitle');
			var label = title ? (title.textContent || '').replace(/\s+/g, ' ').trim() : '';
			if (label === 'Our Latest Youtube Videos') {
				mods[i].classList.add('lh-videos');
				var vrow = mods[i].closest('.vc_row');
				if (vrow) {
					vrow.classList.add('lh-videos-row');
				}
				var grid = mods[i].parentElement ? mods[i].parentElement.querySelector('.vc_row.vc_inner') : null;
				if (!grid && vrow) {
					grid = vrow.querySelector('.vc_row.vc_inner');
				}
				if (grid) {
					grid.classList.add('lh-videos-grid');
				}
			}
			if (label === 'FAQs') {
				mods[i].classList.add('lh-faqs');
				var frow = mods[i].closest('.vc_row');
				if (frow) {
					frow.classList.add('lh-faqs-head-row');
					var list = nextVcRow(frow);
					if (list) {
						list.classList.add('lh-faqs-row');
					}
				}
			}
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', mark);
	} else {
		mark();
	}

	var reviewTries = 0;
	var reviewTimer = setInterval(function () {
		reviewTries += 1;
		hideWidgetWriteReview();
		addReviewsButton();
		if (reviewTries > 20) {
			clearInterval(reviewTimer);
		}
	}, 400);
})();
