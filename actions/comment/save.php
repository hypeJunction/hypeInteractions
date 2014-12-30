<?php

namespace hypeJunction\Interactions;

$user = elgg_get_logged_in_user_entity();

$description = get_input('generic_comment', false);
if (empty($description)) {
	register_error(elgg_echo('generic_comment:blank'));
	forward(REFERER);
}

$comment_guid = get_input('comment_guid', null);
$entity_guid = get_input('entity_guid', null);

if ($comment_guid) {
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
	$comment->owner_guid = $user->guid;
	$comment->container_guid = $entity->guid;
	$comment->access_id = $entity->access_id;
}

$comment->description = $description;

$title = htmlspecialchars(get_input('title', '', false), ENT_QUOTES, 'UTF-8');
if ($title) {
	$comment->title = $title;
}

$upload_guids = get_input('uploads', array());

if ($comment->save()) {

	if (class_exists('hypeJunction\\Filestore\\UploadHandler')) {
		// files being uploaded via $_FILES
		$uploads = \hypeJunction\Filestore\UploadHandler::handle('uploads');
		if ($uploads) {
			foreach ($uploads as $upload) {
				if ($upload->guid) {
					$upload_guids[] = $upload->guid;
				}
			}
		}
	}

	foreach ($upload_guids as $upload_guid) {
		$upload = get_entity($upload_guid);
		if ($upload) {
			$upload->origin = 'comment';
			$upload->access_id = $comment->access_id;
			$upload->container_guid = $comment->guid;
			$upload->save();
		}
		$comment->addRelationship($upload->guid, 'attached');
	}

	if (!$comment_guid) {
		// Notify if poster wasn't owner
		if ($entity->owner_guid != $user->guid) {
			$owner = $entity->getOwnerEntity();

			$comment_text = $comment->description;
			if (elgg_view_exists('output/linkify')) {
				$comment_text = elgg_view('output/linkify', array(
					'value' => $comment_text
				));
			}
			$comment_text .= elgg_view('output/attached', array(
				'entity' => $comment,
			));

			$attachments = $comment->getAttachments(array('limit' => 0));
			if ($attachments && count($attachments)) {
				$attachments = array_map(__NAMESPACE__ . '\\get_linked_entity_name', $attachments);
				$attachments_text = implode(', ', array_filter($attachments));
				if ($attachments_text) {
					$comment_text .= elgg_echo('interactions:attachments:labelled', array($attachments_text));
				}
			}

			$subject = elgg_echo('generic_comment:email:subject', array(), $owner->language);
			$message = elgg_echo('generic_comment:email:body', array(
				$entity->title,
				$user->name,
				$comment_text,
				$entity->getURL(),
				$user->name,
				$user->getURL()
					), $owner->language);

			notify_user($owner->guid, $user->guid, $subject, $message, array(
				'object' => $comment,
				'action' => 'create',
					)
			);
		}

		// Add to river
		elgg_create_river_item(array(
			'view' => 'river/object/comment/create',
			'action_type' => 'comment',
			'subject_guid' => $user->guid,
			'object_guid' => $guid,
			'target_guid' => $entity_guid,
		));
	}
	
	if (elgg_is_xhr()) {
		if ($comment_guid) {
			$view = elgg_view_entity($comment);
		} else {
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
	}

	system_message(elgg_echo('generic_comment:posted'));
	forward($comment->getURL());
} else {
	register_error(elgg_echo('generic_comment:failure'));
	forward(REFERER);
}