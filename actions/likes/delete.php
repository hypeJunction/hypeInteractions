<?php

namespace hypeJunction\Interactions;

$id = (int) get_input('id');

$like = NULL;
if ($id) {
	$like = elgg_get_annotation_from_id($id);
	$entity = get_entity($like->entity_guid);
}

if (!$like) {
	$guid = get_input('guid');
	$entity = get_entity($guid);
	if ($entity) {
		$likes = elgg_get_annotations(array(
			'guid' => $entity->guid,
			'annotation_owner_guid' => elgg_get_logged_in_user_guid(),
			'annotation_name' => 'likes',
		));
		$like = $likes[0];
	}
}

if ($like && $like->canEdit()) {
	$like->delete();

	if (elgg_is_xhr()) {
		echo json_encode(array(
			'guid' => $entity->guid,
			'stats' => get_stats($entity)
		));
	}

	system_message(elgg_echo('likes:deleted'));
	forward(REFERER);
}

register_error(elgg_echo('likes:notdeleted'));
forward(REFERER);
