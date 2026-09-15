/**
 * Keep "Enter Your Birth Details" stacked above the form.
 * Does not change page content in WordPress.
 */
(function () {
	if (!document.body.classList.contains("lh-service-page")) {
		return;
	}

	document.querySelectorAll(".wpb_raw_code form.cart, .wpb_raw_html form.cart").forEach(function (form) {
		var wrap = form.closest(".wpb_raw_code, .wpb_raw_html");
		if (!wrap || wrap.closest(".lh-svc-form-col")) {
			return;
		}

		var prev = wrap.previousElementSibling;
		var col = document.createElement("div");
		col.className = "lh-svc-form-col";

		if (prev && prev.classList.contains("cws_textmodule")) {
			prev.classList.add("lh-form-heading");
			wrap.parentNode.insertBefore(col, prev);
			col.appendChild(prev);
		} else {
			wrap.parentNode.insertBefore(col, wrap);
		}

		col.appendChild(wrap);
	});
})();
