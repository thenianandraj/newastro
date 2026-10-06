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

	document.querySelectorAll(".lh-svc-form-col .wcpa_field_wrap").forEach(function (wrap) {
		var label = wrap.querySelector(".wcpa_field_label, label");
		var text = label ? (label.textContent || "").replace(/\s+/g, " ").trim().toLowerCase() : "";
		var kind = "";
		if (/father/.test(text)) {
			kind = "father";
		} else if (/gender/.test(text)) {
			kind = "gender";
		} else if (/date|time of birth/.test(text)) {
			kind = "date";
		} else if (/place/.test(text)) {
			kind = "place";
		} else if (/language/.test(text)) {
			kind = "language";
		} else if (/email/.test(text)) {
			kind = "email";
		} else if (/mobile|phone/.test(text)) {
			kind = "mobile";
		} else if (/^name\b/.test(text)) {
			kind = "name";
		}
		if (kind) {
			wrap.setAttribute("data-lh-field", kind);
		}
	});

	document.querySelectorAll(".lh-svc-form-col .wcpa_checkbox label span:not(.wcpa_checkbox_custom)").forEach(function (span) {
		if (span.querySelector(".lh-terms")) {
			return;
		}
		var text = span.textContent || "";
		if (!/terms/i.test(text)) {
			return;
		}
		span.innerHTML = text.replace(/(terms\s*(?:&amp;|&)\s*conditions)/i, '<span class="lh-terms">$1</span>');
	});

	/**
	 * WCPA time fields: type hh:mm and pick AM / PM inside the same box.
	 * Drives WCPA's own flatpickr instance, so the submitted value is unchanged.
	 */
	function maskTime(raw, deleting) {
		if (deleting) {
			return raw.replace(/[^\d:]/g, "");
		}
		var colon = raw.indexOf(":");
		var h;
		var m;
		if (colon !== -1) {
			h = raw.slice(0, colon).replace(/\D/g, "").slice(0, 2);
			m = raw.slice(colon + 1).replace(/\D/g, "").slice(0, 2);
			return h + ":" + m;
		}
		var digits = raw.replace(/\D/g, "").slice(0, 4);
		if (!digits || digits === "1") {
			return digits;
		}
		var hourLen = digits[0] === "0" || (digits[0] === "1" && /[0-2]/.test(digits[1])) ? 2 : 1;
		h = digits.slice(0, hourLen);
		m = digits.slice(hourLen, hourLen + 2);
		return h + ":" + m;
	}

	function parseTime(value) {
		var match = /^(\d{1,2}):(\d{2})$/.exec(value);
		if (!match) {
			return null;
		}
		var h = parseInt(match[1], 10);
		var m = parseInt(match[2], 10);
		return h >= 1 && h <= 12 && m <= 59 ? { h: h, m: m } : null;
	}

	function enhanceTimeField(box) {
		var native = box.querySelector("input.wcpa_field");
		var fp = native && native._flatpickr;
		if (!fp || box.querySelector(".lh-time-text")) {
			return;
		}

		var fieldWrap = box.closest(".wcpa_field_wrap");
		var label = fieldWrap && fieldWrap.querySelector(".wcpa_field_label, label");
		var syncing = false;
		var meridiem = "";

		var text = document.createElement("input");
		text.type = "text";
		text.className = "lh-time-text";
		text.inputMode = "numeric";
		text.autocomplete = "off";
		text.maxLength = 5;
		text.placeholder = "hh:mm";
		text.setAttribute("aria-label", label ? label.textContent.replace(/\s+/g, " ").replace("*", "").trim() : "Time");

		var toggle = document.createElement("div");
		toggle.className = "lh-ampm";
		toggle.setAttribute("role", "group");
		var buttons = {};
		["AM", "PM"].forEach(function (value) {
			var btn = document.createElement("button");
			btn.type = "button";
			btn.textContent = value;
			btn.setAttribute("aria-pressed", "false");
			btn.addEventListener("click", function () {
				setMeridiem(value);
				commit();
			});
			buttons[value] = btn;
			toggle.appendChild(btn);
		});

		function setMeridiem(value) {
			meridiem = value;
			Object.keys(buttons).forEach(function (key) {
				buttons[key].setAttribute("aria-pressed", key === value ? "true" : "false");
			});
			toggle.classList.remove("lh-ampm-missing");
		}

		function commit() {
			var t = parseTime(text.value);
			syncing = true;
			if (t && meridiem) {
				var h24 = (t.h % 12) + (meridiem === "PM" ? 12 : 0);
				var cur = fp.selectedDates[0];
				if (!cur || cur.getHours() !== h24 || cur.getMinutes() !== t.m) {
					fp.setDate(new Date(2022, 0, 1, h24, t.m), true);
				}
			} else if (fp.selectedDates.length) {
				fp.clear();
			}
			syncing = false;
		}

		function pull() {
			var d = fp.selectedDates[0];
			if (!d) {
				text.value = "";
				return;
			}
			setMeridiem(d.getHours() >= 12 ? "PM" : "AM");
			text.value = (d.getHours() % 12 || 12) + ":" + ("0" + d.getMinutes()).slice(-2);
		}

		text.addEventListener("keydown", function (e) {
			var key = (e.key || "").toLowerCase();
			if (key === "a" || key === "p") {
				e.preventDefault();
				setMeridiem(key === "a" ? "AM" : "PM");
				commit();
			}
		});

		text.addEventListener("input", function (e) {
			var deleting = !!e.inputType && e.inputType.indexOf("delete") === 0;
			text.value = maskTime(text.value, deleting);
			text.classList.remove("lh-time-invalid");
			commit();
		});

		text.addEventListener("blur", function (e) {
			commit();
			var t = parseTime(text.value);
			text.classList.toggle("lh-time-invalid", !!text.value && !t);
			if (!toggle.contains(e.relatedTarget)) {
				toggle.classList.toggle("lh-ampm-missing", !!t && !meridiem);
			}
		});

		fp.config.onChange.push(function () {
			if (!syncing) {
				pull();
			}
		});

		box.appendChild(text);
		box.appendChild(toggle);
		box.classList.add("lh-time-ready");
		pull();
	}

	function enhanceTimeFields() {
		document.querySelectorAll(".lh-svc-form-col .wcpa_type_time .wcpa_date_field_wrap").forEach(enhanceTimeField);
	}

	enhanceTimeFields();
	if (window.MutationObserver) {
		var timeQueued = false;
		var timeObserver = new MutationObserver(function () {
			if (timeQueued) {
				return;
			}
			timeQueued = true;
			window.requestAnimationFrame(function () {
				timeQueued = false;
				enhanceTimeFields();
			});
		});
		document.querySelectorAll(".lh-svc-form-col").forEach(function (col) {
			timeObserver.observe(col, { childList: true, subtree: true, attributes: true, attributeFilter: ["class"] });
		});
	}

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
