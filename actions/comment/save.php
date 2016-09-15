<?php

namespace hypeJunction\Interactions;

$poster = elgg_get_logged_in_user_entity();

$description = get_input('generic_comment', false);
if (empty($description)) {
	register_error(elgg_echo('generic_comment:blank'));
	forward(REFERER);
}

$comment_guid = get_input('comment_guid', null);
$entity_guid = get_input('entity_guid', null);
$new_comment = !$comment_guid;

if (!$new_comment) {
	$comment = get_entity($comment_guid);

	if (!$comment instanceof Comment) {
		register_error(elgg_echo('generic_comment:notfound'));
		forward(REFERER);
	}

	if (!$comment->canEdit()) {
		register_error(elgg_echo('actionunauthorized'));
		forward(REFERER);
	}

	$entity = $comment->getContainerEntity();
} else {
	$entity = get_entity($entity_guid);
	if (!$entity) {
		register_error(elgg_echo('generic_comment:notfound'));
		forward(REFERER);
	}

	if (!$entity->canComment()) {
		register_error(elgg_echo('actionunauthorized'));
		forward(REFERER);
	}

	$comment = new Comment();
	$comment->owner_guid = $poster->guid;
	$comment->container_guid = $entity->guid;
	$comment->access_id = $entity->access_id;
}

$comment->description = $description;

$title = htmlspecialchars(get_input('title', '', false), ENT_QUOTES, 'UTF-8');
if ($title) {
	$comment->title = $title;
}

if (!$comment->save()) {
	register_error(elgg_echo('generic_comment:failure'));
	forward(REFERER);
}

if (elgg_is_active_plugin('hypeAttachments')) {
	hypeapps_attach_uploaded_files($comment, 'uploads', [
		'origin' => 'comment',
		'container_guid' => $comment->guid,
		'access_id' => $comment->access_id,
	]);
}

if ($new_comment) {
	
	if ($entity->owner_guid != $poster->guid) {
		// Send a notification to the content owner

		$recipient = $entity->getOwnerEntity();
		$language = $recipient->language;

		$messages = (new NotificationFormatter($comment, $recipient, $language))->prepare();
		
		notify_user($recipient->guid, $poster->guid, $messages->subject, $messages->body, array(
			'object' => $comment,
			'action' => 'create',
			'summary' => $messages->summary,
		));
	}

	// Add to river
	elgg_create_river_item(array(
		'view' => 'river/object/comment/create',
		'action_type' => 'create',
		'subject_guid' => $poster->guid,
		'object_guid' => $comment->guid,
		'target_guid' => $entity->guid,
	));
}

if (elgg_is_xhr()) {
	elgg_push_context('comments');
	if ($comment_guid) {
		// editing a comment
		$view = elgg_view_entity($comment, [
			'full_view' => true,
		]);
	} else {
		// new comment
		$view = elgg_view('framework/interactions/comments', array(
			'entity' => $entity,
			'comment' => $comment,
		));
	}
	$output = array(
		'guid' => $entity->guid,
		'view' => $view,
		'stats' => get_stats($entity),
	);

	echo json_encode($output);
	elgg_pop_context();
}

system_message(elgg_echo('generic_comment:posted'));
forward($comment->getURL());
