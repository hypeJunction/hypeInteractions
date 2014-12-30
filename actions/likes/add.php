<?php

namespace hypeJunction\Interactions;

$guid = get_input('guid');
$entity = get_entity($guid);

if (!$entity) {
	register_error(elgg_echo('likes:notfound'));
	forward(REFERER);
}

$user = elgg_get_logged_in_user_entity();

if (elgg_annotation_exists($entity->guid, 'likes')) {
	system_message(elgg_echo('likes:alreadyliked'));
	forward(REFERER);
}

$id = create_annotation($entity->guid, 'likes', 1, '', $user->guid, $entity->access_id);

if (!$id) {
	register_error(elgg_echo('likes:failure'));
	forward(REFERER);
}

$annotation = elgg_get_annotation_from_id($id);

$title_str = $entity->getDisplayName();
if (!$title_str) {
	$title_str = elgg_get_excerpt($entity->description);
}

if ($entity->owner_guid != $user->guid) {
	$site = elgg_get_site_entity();

	$subject = elgg_echo('likes:notifications:subject', array(
		$user->name,
		$title_str
			), $owner->language);

	$body = elgg_echo('likes:notifications:body', array(
		$owner->name,
		$user->name,
		$title_str,
		$site->name,
		$entity->getURL(),
		$user->getURL()
			), $owner->language);

	notify_user($entity->owner_guid, $user->guid, $subject, $body, array(
		'action' => 'create',
		'object' => $annotation,
	));
}

elgg_create_river_item(array(
	'view' => 'framework/river/stream/like',
	'action_type' => 'stream:like',
	'subject_guid' => $user->guid,
	'object_guid' => $entity->guid,
	'annotation_id' => $id,
));

if (elgg_is_xhr()) {
	echo json_encode(array(
		'guid' => $entity->guid,
		'stats' => get_stats($entity)
	));
}

system_message(elgg_echo('likes:likes'));

$handler = HYPEINTERACTIONS_HANDLER;
forward("$handler/likes/$entity->guid");
