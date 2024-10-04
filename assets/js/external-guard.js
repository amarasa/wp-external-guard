document.addEventListener("DOMContentLoaded", function () {
	// Retrieve settings from PHP
	var excludes = ela_settings.excludes || [];
	var message =
		ela_settings.message || "You are about to leave our site. Continue?";
	var title = ela_settings.title || "External Link";
	var confirmButtonText = ela_settings.confirmButtonText || "Yes, proceed";
	var cancelButtonText = ela_settings.cancelButtonText || "Cancel";
	var confirmButtonColor = ela_settings.confirmButtonColor || "#3085d6"; // Default SweetAlert2 Blue
	var cancelButtonColor = ela_settings.cancelButtonColor || "#d33"; // Default SweetAlert2 Red

	// Convert excludes to lower case for case-insensitive comparison
	excludes = excludes.map(function (domain) {
		return domain.toLowerCase();
	});

	var links = document.querySelectorAll("a[href^='http']");

	links.forEach(function (link) {
		var href = link.href;
		var linkHostname;

		try {
			linkHostname = new URL(href).hostname.toLowerCase();
		} catch (e) {
			// If URL parsing fails, skip this link
			return;
		}

		// Check if the link is internal
		if (
			linkHostname === location.hostname.toLowerCase() ||
			linkHostname.endsWith("." + location.hostname.toLowerCase())
		) {
			return;
		}

		// Check if the link is excluded
		var isExcluded = excludes.some(function (domain) {
			domain = domain.trim().toLowerCase();
			return (
				linkHostname === domain || linkHostname.endsWith("." + domain)
			);
		});

		if (isExcluded) {
			return;
		}

		// Attach click event handler
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Prevent the default action

			Swal.fire({
				title: title,
				text: message,
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: confirmButtonText,
				cancelButtonText: cancelButtonText,
				confirmButtonColor: confirmButtonColor, // User-selected Confirm Button Color
				cancelButtonColor: cancelButtonColor, // User-selected Cancel Button Color
				reverseButtons: true, // Optional: Swap the positions of Confirm and Cancel buttons
			}).then((result) => {
				if (result.isConfirmed) {
					window.open(href, "_blank");
				}
				// If cancelled, do nothing
			});
		});
	});
});
