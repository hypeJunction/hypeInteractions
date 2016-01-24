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

	$language = $user->language;
	$user_url = elgg_view('output/url', array(
		'text' => $user->name,
		'href' => $user->getURL(),
	));

	if ($entity instanceof Comment) {
		$target = elgg_echo('interactions:comment');
	} else {
		$target = elgg_echo('interactions:post');
	}
	if (is_callable(array($entity, 'getDisplayName'))) {
		$entity_title = $entity->getDisplayName();
	} else {
		$entity_title = $entity->title ? : $entity->name;
	}

	$entity_url = elgg_view('output/url', array(
		'text' => $entity_title,
		'href' => elgg_http_add_url_query_elements($entity->getURL(), array(
			'active_tab' => 'likes',
		)),
	));
	$entity_url = elgg_echo('interactions:ownership:your', array($target), $language) . ' ' . $entity_url;

	$entity_ownership = elgg_echo('interactions:ownership:your', array($target), $language);
	$entity_ownership_url = elgg_view('output/url', array(
		'text' => $entity_ownership,
		'href' => elgg_http_add_url_query_elements($entity->getURL(), array(
			'active_tab' => 'likes',
		)),
	));

	$summary = elgg_echo('interactions:likes:notifications:subject', array(
		$user_url,
		$entity_ownership_url,
			), $language);

	$subject = strip_tags($summary);

	$site_url = elgg_view('output/url', array(
		'text' => elgg_get_site_entity()->name,
		'href' => elgg_get_site_url(),
	));

	$owner = $entity->getOwnerEntity();
	$owner_name = $owner->first_name ? : $owner->name;

	$body = elgg_echo('interactions:likes:notifications:body', array(
		$user_url,
		$entity_url,
		$entity->getURL(),
		$user->getURL()
			), $owner->language);

	notify_user($entity->owner_guid, $user->guid, $subject, $body, array(
		'action' => 'create',
		'object' => $annotation,
		'summary' => $summary,
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
