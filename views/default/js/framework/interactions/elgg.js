require(['jquery', 'elgg'], function ($) {

	if ($('[class~="interactions"]').length) {
		require(['framework/interactions/lib'], function (interactions) {
			interactions.init();
		});
	}

	$(document).ajaxSuccess(function (event, response, settings) {
		var data = '';
		if (settings.dataType === 'json') {
			data = $.parseJSON(response.responseText);
			if (data && data.status >= 0) {
				if (data.output && data.output.guid && data.output.stats) {
					require(['framework/interactions/lib'], function (interactions) {
						interactions.updateStats(data.output.guid, data.output.stats);
					});
				}
			}
		}
	});

});