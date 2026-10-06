/**
 * WCPA "Date of Birth Time of Birth" (native datetime-local) fields:
 * typed dd/mm/yyyy date + a separate Time of Birth cell (hh:mm + AM / PM).
 * The original input stays in the form and receives the same
 * YYYY-MM-DDTHH:MM value the native picker would give.
 *
 * Labels: "Date of Birth | Time of Birth" in WCPA admin sets both labels;
 * without "|", "Time of Birth" is split off the admin label.
 */
(function () {
	var valueSetter = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, "value").set;
	var controllers = {};

	function pad(n) {
		return ("0" + n).slice(-2);
	}

	/* Day starting 4-9 and month starting 2-9 can only be one digit, so they are completed right away. */
	function maskDate(raw, deleting) {
		if (deleting) {
			return raw.replace(/[^\d\/]/g, "");
		}
		var parts = [""];
		for (var i = 0; i < raw.length; i++) {
			var ch = raw[i];
			var last = parts.length - 1;
			if (/\d/.test(ch)) {
				if (last === 2) {
					if (parts[2].length < 4) {
						parts[2] += ch;
					}
					continue;
				}
				parts[last] += ch;
				if (parts[last].length === 1 && ch > (last === 0 ? "3" : "1")) {
					parts[last] = "0" + ch;
				}
				if (parts[last].length === 2) {
					parts.push("");
				}
			} else if (/[\/\s.\-]/.test(ch) && last < 2 && parts[last] !== "") {
				parts[last] = pad(parts[last]);
				parts.push("");
			}
		}
		return parts.join("/");
	}

	function parseDate(value) {
		var match = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/.exec(value);
		if (!match) {
			return null;
		}
		var d = parseInt(match[1], 10);
		var m = parseInt(match[2], 10);
		var y = parseInt(match[3], 10);
		var check = new Date(y, m - 1, d);
		if (y < 1900 || y > 2100 || check.getFullYear() !== y || check.getMonth() !== m - 1 || check.getDate() !== d) {
			return null;
		}
		return { d: d, m: m, y: y };
	}

	function maskTime(raw, deleting) {
		if (deleting) {
			return raw.replace(/[^\d:]/g, "");
		}
		var colon = raw.search(/[:.\s]/);
		if (colon !== -1) {
			return raw.slice(0, colon).replace(/\D/g, "").slice(0, 2) + ":" + raw.slice(colon + 1).replace(/\D/g, "").slice(0, 2);
		}
		var digits = raw.replace(/\D/g, "").slice(0, 4);
		if (!digits || digits === "1") {
			return digits;
		}
		var hourLen = digits[0] === "0" || (digits[0] === "1" && /[0-2]/.test(digits[1])) ? 2 : 1;
		if (digits.length <= hourLen) {
			return digits;
		}
		return digits.slice(0, hourLen) + ":" + digits.slice(hourLen, hourLen + 2);
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

	function splitLabel(text) {
		if (text.indexOf("|") !== -1) {
			var parts = text.split("|");
			return { date: parts[0].trim() || "Date of Birth", time: parts.slice(1).join("|").trim() || "Time of Birth" };
		}
		return {
			date: text.replace(/\s*(?:and|&|\/|,)?\s*time\s+of\s+birth\s*/i, " ").trim() || "Date of Birth",
			time: "Time of Birth",
		};
	}

	function hideNative(native, fieldWrap) {
		if (fieldWrap.style.position !== "relative") {
			fieldWrap.style.position = "relative";
		}
		if (!fieldWrap.classList.contains("lh-dt-ready")) {
			fieldWrap.classList.add("lh-dt-ready");
		}
		if (native.style.opacity === "0") {
			return;
		}
		/* Kept focusable (not display:none) so the browser's required check can still point at it. */
		["position:absolute", "left:0", "bottom:0", "width:1px", "height:1px", "min-height:0", "padding:0", "margin:0", "border:0", "opacity:0", "pointer-events:none"].forEach(function (rule) {
			var i = rule.indexOf(":");
			native.style.setProperty(rule.slice(0, i), rule.slice(i + 1), "important");
		});
		native.tabIndex = -1;
	}

	function enhance(native) {
		var fieldWrap = native.closest(".wcpa_field_wrap");
		if (!fieldWrap || native._flatpickr) {
			return;
		}
		if (native._lhBound) {
			hideNative(native, fieldWrap);
			return;
		}

		var key = fieldWrap.id || native.name;
		var previous = controllers[key];
		fieldWrap.querySelectorAll(".lh-dt").forEach(function (old) {
			old.parentNode.removeChild(old);
		});
		document.querySelectorAll('.lh-time-row[data-lh-for="' + key + '"]').forEach(function (old) {
			old.parentNode.removeChild(old);
		});

		var label = fieldWrap.querySelector(".wcpa_field_label, label");
		var labels = splitLabel(label ? label.textContent.replace(/\s+/g, " ").replace("*", "").trim() : "");
		var meridiem = "";
		var syncing = false;

		var box = document.createElement("div");
		box.className = "lh-dt";

		var date = document.createElement("input");
		date.type = "text";
		date.className = "lh-date-text";
		date.inputMode = "numeric";
		date.autocomplete = "off";
		date.maxLength = 10;
		date.placeholder = "dd/mm/yyyy";
		date.setAttribute("aria-label", labels.date + " (dd/mm/yyyy)");

		var dateMsg = document.createElement("div");
		dateMsg.className = "lh-dt-msg";
		dateMsg.setAttribute("aria-live", "polite");

		var timeBox = document.createElement("div");
		timeBox.className = "lh-dt-time lh-time-ready";

		var time = document.createElement("input");
		time.type = "text";
		time.className = "lh-time-text";
		time.inputMode = "numeric";
		time.autocomplete = "off";
		time.maxLength = 5;
		time.placeholder = "hh:mm";
		time.id = "lh-time-" + key;
		time.setAttribute("aria-label", labels.time + " (hh:mm)");

		var timeMsg = document.createElement("div");
		timeMsg.className = "lh-dt-msg";
		timeMsg.setAttribute("aria-live", "polite");

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
				validate(false);
			});
			buttons[value] = btn;
			toggle.appendChild(btn);
		});

		function setMeridiem(value) {
			meridiem = value;
			Object.keys(buttons).forEach(function (k) {
				buttons[k].setAttribute("aria-pressed", k === value ? "true" : "false");
			});
			toggle.classList.remove("lh-ampm-missing");
		}

		function writeNative(value, force) {
			if (!force && native.value === value) {
				return;
			}
			syncing = true;
			valueSetter.call(native, value);
			native.dispatchEvent(new Event("input", { bubbles: true }));
			native.dispatchEvent(new Event("change", { bubbles: true }));
			syncing = false;
		}

		function commit(force) {
			var d = parseDate(date.value);
			var t = parseTime(time.value);
			if (d && t && meridiem) {
				var h24 = (t.h % 12) + (meridiem === "PM" ? 12 : 0);
				writeNative(d.y + "-" + pad(d.m) + "-" + pad(d.d) + "T" + pad(h24) + ":" + pad(t.m), force);
				native.setCustomValidity("");
				return;
			}
			writeNative("", force);
			native.setCustomValidity(date.value || time.value ? "Enter date as dd/mm/yyyy, time as hh:mm and choose AM or PM." : "");
		}

		/* strict = on Add to Cart: also flag a missing time when the date is filled (and vice versa). */
		function validate(strict) {
			var dateBad = !!date.value && !parseDate(date.value);
			var t = parseTime(time.value);
			var timeBad = !!time.value && !t;
			var noMeridiem = !!t && !meridiem;
			var timeEmpty = strict && !time.value && !!date.value;

			date.classList.toggle("lh-date-invalid", dateBad || (strict && !date.value && !!time.value));
			dateMsg.textContent = dateBad ? "Enter a valid date as dd/mm/yyyy" : strict && !date.value && time.value ? "Enter the date of birth" : "";

			time.classList.toggle("lh-time-invalid", timeBad || timeEmpty);
			toggle.classList.toggle("lh-ampm-missing", noMeridiem);
			timeMsg.textContent = timeBad ? "Enter time as hh:mm (01:00 to 12:59)" : noMeridiem ? "Choose AM or PM" : timeEmpty ? "Enter the time of birth" : "";
		}

		function pull() {
			var match = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})/.exec(native.value);
			if (!match) {
				return;
			}
			var h = parseInt(match[4], 10);
			date.value = match[3] + "/" + match[2] + "/" + match[1];
			time.value = (h % 12 || 12) + ":" + match[5];
			setMeridiem(h >= 12 ? "PM" : "AM");
		}

		date.addEventListener("input", function (e) {
			var deleting = !!e.inputType && e.inputType.indexOf("delete") === 0;
			date.value = maskDate(date.value, deleting);
			date.classList.remove("lh-date-invalid");
			dateMsg.textContent = "";
			commit();
		});

		date.addEventListener("blur", function () {
			commit();
			validate(false);
		});

		time.addEventListener("keydown", function (e) {
			var k = (e.key || "").toLowerCase();
			if (k === "a" || k === "p") {
				e.preventDefault();
				setMeridiem(k === "a" ? "AM" : "PM");
				commit();
				validate(false);
			}
		});

		time.addEventListener("input", function (e) {
			var deleting = !!e.inputType && e.inputType.indexOf("delete") === 0;
			time.value = maskTime(time.value, deleting);
			time.classList.remove("lh-time-invalid");
			timeMsg.textContent = "";
			commit();
		});

		time.addEventListener("blur", function (e) {
			commit();
			if (!toggle.contains(e.relatedTarget)) {
				validate(false);
			}
		});

		native.addEventListener("input", function () {
			if (!syncing) {
				pull();
			}
		});

		timeBox.appendChild(time);
		timeBox.appendChild(toggle);
		box.appendChild(date);
		box.appendChild(dateMsg);
		native.insertAdjacentElement("afterend", box);

		if (label && label.firstChild && label.firstChild.nodeType === 3) {
			label.firstChild.nodeValue = labels.date;
		}

		/* Time of Birth gets its own field cell (a sibling row), styled like any other WCPA field. */
		var timeWrap = document.createElement("div");
		timeWrap.className = "wcpa_field_wrap lh-time-split wcpa_label_pos_above";
		timeWrap.setAttribute("data-lh-field", "time");
		var timeLabelEl = document.createElement("label");
		timeLabelEl.className = "wcpa_field_label";
		timeLabelEl.htmlFor = time.id;
		timeLabelEl.textContent = labels.time;
		var ast = label && label.querySelector(".wcpa_required_ast");
		if (ast) {
			timeLabelEl.appendChild(ast.cloneNode(true));
		}
		timeWrap.appendChild(timeLabelEl);
		timeWrap.appendChild(timeBox);
		timeWrap.appendChild(timeMsg);

		var timeRow = document.createElement("div");
		timeRow.className = "wcpa_row lh-time-row";
		timeRow.setAttribute("data-lh-for", key);
		timeRow.appendChild(timeWrap);
		(fieldWrap.closest(".wcpa_row") || fieldWrap).insertAdjacentElement("afterend", timeRow);

		native._lhBound = true;
		hideNative(native, fieldWrap);

		if (previous) {
			date.value = previous.date.value;
			time.value = previous.time.value;
			if (previous.meridiem()) {
				setMeridiem(previous.meridiem());
			}
			commit(true);
		} else {
			pull();
		}

		controllers[key] = {
			native: native,
			date: date,
			time: time,
			meridiem: function () {
				return meridiem;
			},
			sync: function () {
				commit(true);
				validate(true);
			},
		};
	}

	function enhanceAll() {
		document.querySelectorAll('form.cart .wcpa_type_datetime-local input.wcpa_field[type="datetime-local"]').forEach(enhance);
	}

	/* WCPA re-renders can reset the original input; push the typed values back right before it validates. */
	function syncAll() {
		enhanceAll();
		Object.keys(controllers).forEach(function (key) {
			if (controllers[key].native.isConnected) {
				controllers[key].sync();
			}
		});
	}

	function start() {
		enhanceAll();
		document.addEventListener(
			"click",
			function (e) {
				if (e.target.closest && e.target.closest("form.cart button[type='submit'], form.cart .single_add_to_cart_button")) {
					syncAll();
				}
			},
			true
		);
		document.addEventListener(
			"submit",
			function (e) {
				if (e.target.matches && e.target.matches("form.cart")) {
					syncAll();
				}
			},
			true
		);
		if (!window.MutationObserver) {
			return;
		}
		var queued = false;
		var observer = new MutationObserver(function () {
			if (queued) {
				return;
			}
			queued = true;
			window.requestAnimationFrame(function () {
				queued = false;
				enhanceAll();
			});
		});
		document.querySelectorAll("form.cart").forEach(function (form) {
			observer.observe(form, { childList: true, subtree: true, attributes: true, attributeFilter: ["class"] });
		});
	}

	if (document.readyState === "complete") {
		start();
	} else {
		window.addEventListener("load", start);
	}
})();
