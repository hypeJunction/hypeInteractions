<?php

/**
 * @todo: reverse attached relationship
 * @todo: change river view 'framework/river/interactions/comment' to 'river/object/comment/create'
 * @todo: change river action_type 'stream:reply', 'stream:comment' to 'comment'
 * @todo: subscribed relationship to elgg_add_subscription()
 */

run_function_once('interactions_20141227a');

/**
 * Import relevant hypeAlive plugin settings
 * @return void
 */
function interactions_20141227a() {

	$settings = array(
		'max_comment_depth',
		'comment_form_position',
		'comments_order',
		'comments_load_style',
		'comments_limit',
		'comments_load_limit',
	);

	foreach ($settings as $setting) {
		if (is_null(elgg_get_plugin_setting($setting, 'hypeInteractions'))) {
			$value = elgg_get_plugin_setting($setting, 'hypeAlive');
			if ($value) {
				elgg_set_plugin_setting($setting, $value, 'hypeInteractions');
			}
		}
	}
}