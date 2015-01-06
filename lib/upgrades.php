<?php

run_function_once('interactions_20141227a');
run_function_once('interactions_20141231a');
run_function_once('interactions_20150106a');

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

/**
 * Reverses 'attached' relationship for old comments
 * @return void
 */
function interactions_20141231a() {

	$comments = new \ElggBatch('elgg_get_entities', array(
		'types' => 'object',
		'subtypes' => 'hjcomment',
		'limit' => 0,
		'callback' => false,
	));

	foreach ($comments as $comment) {

		$attachments = new \ElggBatch('elgg_get_entities_from_relationship', array(
			'relationship' => 'attached',
			'relationship_guid' => $comment->guid,
			'inverse_relationship' => true,
			'limit' => 0,
			'callback' => false,
		));

		foreach ($attachments as $attachment) {
			add_entity_relationship($comment->guid, 'attached', $attachment->guid);
		}
	}
}

/**
 * Make sure river items have targets
 * @return void
 */
function interactions_20150106a() {

	$dbprefix = elgg_get_config('dbprefix');

	$river = new \ElggBatch('elgg_get_river', array(
		'action_type' => 'stream:comment',
		'limit' => 0,
		'callback' => false,
	));

	foreach ($river as $r) {
		$id = $r->id;
		$comment = get_entity($r->object_guid);
		$target_guid = 0;
		if ($comment) {
			$target_guid = (int) $comment->container_guid;
		}

		$query = "UPDATE {$dbprefix}river SET target_guid=$target_guid WHERE id=$id";
		update_data($query);
	}
}
