define(function (require) {

	var ckeditor = require('elgg/ckeditor');

	$(document).on('click focus', '.elgg-input-comment', function (e) {
		if ($(this).data('ckeditorInstance')) {
			$(this).ckeditorGet().focus();
		} else {
			ckeditor.bind($(this));
		}
		$(this).closest('form').addClass('elgg-state-expanded');
	});

	$(document).on('reset', 'form', function () {
		$(this).find('.elgg-input-comment[data-cke-init]').each(function () {
			if ($(this).data('ckeditorInstance')) {
				$(this).ckeditorGet().destroy();
				$(this).data('ckeditorInstance', null);
			}
		});
	});
});

