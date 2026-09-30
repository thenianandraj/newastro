/**
 * Keep "Enter Your Birth Details" + price stacked above the form.
 * Does not change page content in WordPress.
 */
(function () {
	if (!document.body.classList.contains("lh-service-page")) {
		return;
	}

	function moduleTitle(el) {
		var t = el.querySelector(".widgettitle");
		return t ? (t.textContent || "").replace(/\s+/g, " ").trim() : "";
	}

	function formMetaKind(el) {
		if (!el || !el.classList.contains("cws_textmodule")) {
			return "";
		}
		var title = moduleTitle(el);
		var text = (el.textContent || "").replace(/\s+/g, " ").trim();
		if (/enter your birth details/i.test(title) || /enter your birth details/i.test(text)) {
			return "heading";
		}
		if (/incl\.?\s*gst/i.test(title) || /incl\.?\s*gst/i.test(text) || /[₹]\s*\d/i.test(title) || /^\s*\d+\s*incl/i.test(title)) {
			return "price";
		}
		return "";
	}

	document.querySelectorAll(".wpb_raw_code form.cart, .wpb_raw_html form.cart").forEach(function (form) {
		var wrap = form.closest(".wpb_raw_code, .wpb_raw_html");
		if (!wrap || wrap.closest(".lh-svc-form-col")) {
			return;
		}

		var pack = [];
		var el = wrap.previousElementSibling;
		while (el && formMetaKind(el)) {
			pack.unshift(el);
			el = el.previousElementSibling;
		}

		var col = document.createElement("div");
		col.className = "lh-svc-form-col";
		var insertBefore = pack.length ? pack[0] : wrap;
		wrap.parentNode.insertBefore(col, insertBefore);

		pack.forEach(function (mod) {
			var kind = formMetaKind(mod);
			if (kind === "heading") {
				mod.classList.add("lh-form-heading");
			} else if (kind === "price") {
				mod.classList.add("lh-form-price");
			}
			col.appendChild(mod);
		});

		col.appendChild(wrap);
	});

	/**
	 * DEV/prod sometimes strip <a class="hr_btn"> from sample PDF links,
	 * leaving plain "TamilEnglish" text. Rebuild gold pill links in the DOM.
	 */
	function restoreSampleReportLinks() {
		var cfg = window.lhServicePage || {};
		var tamilUrl = cfg.sampleTamilUrl || "/wp-content/uploads/2024/05/Tamil-Selvi-Life-Horoscope-2024-2033.pdf";
		var englishUrl = cfg.sampleEnglishUrl || "/wp-content/uploads/2024/05/Tamil-Selvi-Life-Horoscope-2024-2033-English.pdf";

		document.querySelectorAll(".btn-hr").forEach(function (box) {
			if (box.querySelector("a.hr_btn, a.hr_btn1")) {
				return;
			}
			var p = box.querySelector("p") || box;
			var text = (p.textContent || "").replace(/\s+/g, " ").trim();
			if (!/tamil/i.test(text) || !/english/i.test(text)) {
				return;
			}
			p.innerHTML = "";
			var aTamil = document.createElement("a");
			aTamil.className = "hr_btn";
			aTamil.href = tamilUrl;
			aTamil.target = "_blank";
			aTamil.rel = "noopener noreferrer";
			aTamil.textContent = "Tamil";
			var aEng = document.createElement("a");
			aEng.className = "hr_btn1";
			aEng.href = englishUrl;
			aEng.target = "_blank";
			aEng.rel = "noopener noreferrer";
			aEng.textContent = "English";
			p.appendChild(aTamil);
			p.appendChild(aEng);
		});
	}

	restoreSampleReportLinks();
})();
