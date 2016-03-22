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
	$comment->owner_guid = $poster->guid;
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
		if ($entity->owner_guid != $poster->guid) {
			$entity_owner = $entity->getOwnerEntity();
			$language = $entity_owner->language;

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

			$poster_url = elgg_view('output/url', array(
				'text' => $poster->name,
				'href' => $poster->getURL(),
			));

			if ($entity instanceof Comment) {
				$target = elgg_echo('interactions:comment');
				$original_entity = $entity->getOriginalContainer();
				$original_entity_owner = $original_entity->getOwnerEntity();
				if (is_callable(array($original_entity, 'getDisplayName'))) {
					$original_entity_title = $original_entity->getDisplayName();
				} else {
					$original_entity_title = $entity->title ? : $entity->name;
				}
				$original_entity_url = elgg_view('output/url', array(
					'text' => $original_entity_title,
					'href' => elgg_http_add_url_query_elements($original_entity->getURL(), array(
						'active_tab' => 'comments',
					)),
				));
				$original_entity_url = elgg_echo('interactions:comment:reply_to', array($original_entity_url));

				if ($poster->guid == $original_entity->owner_guid) {
					$entity_url = elgg_echo('interactions:ownership:own', array($target), $language) . ' ' . $original_entity_url;
				} else if ($poster->guid == $recipient->guid) {
					$entity_url = elgg_echo('interactions:ownership:your', array($target), $language) . ' ' . $original_entity_url;
				} else {
					$entity_url = elgg_echo('interactions:ownership:owner', array($original_entity_owner->name, $target), $language) . ' ' . $original_entity_url;
				}
			} else {
				$target = elgg_echo('interactions:post');
				if (is_callable(array($entity, 'getDisplayName'))) {
					$entity_title = $entity->getDisplayName();
				} else {
					$entity_title = $entity->title ? : $entity->name;
				}
				$entity_url = elgg_view('output/url', array(
					'text' => $entity_title,
					'href' => elgg_http_add_url_query_elements($entity->getURL(), array(
						'active_tab' => 'comments',
					)),
				));
				$entity_url = elgg_echo('interactions:ownership:your', array($target), $language) . ' ' . $entity_url;
			}

			$entity_ownership = elgg_echo('interactions:ownership:your', array($target), $language);
			$entity_ownership_url = elgg_view('output/url', array(
				'text' => $entity_ownership,
				'href' => elgg_http_add_url_query_elements($entity->getURL(), array(
					'active_tab' => 'comments',
				)),
			));

			if ($entity instanceof Comment) {
				$summary = elgg_echo('interactions:reply:email:subject', array($poster_url, $entity_ownership_url), $language);
				$subject = strip_tags($summary);
				$message = elgg_echo('interactions:reply:email:body', array(
					$poster_url,
					$entity_url,
					$comment_text,
					$original_entity->getURL(),
					$poster->name,
					$poster->getURL()
						), $language);
			} else {
				$summary = elgg_echo('interactions:comment:email:subject', array($poster_url, $entity_ownership_url), $language);
				$subject = strip_tags($summary);
				$message = elgg_echo('interactions:comment:email:body', array(
					$poster_url,
					$entity_url,
					$comment_text,
					$entity->getURL(),
					$poster->name,
					$poster->getURL()
						), $language);
			}

			notify_user($entity_owner->guid, $poster->guid, $subject, $message, array(
				'object' => $comment,
				'action' => 'create',
				'summary' => $summary,
			));
		}

		if ($entity instanceof Comment) {
			$original_entity = $entity->getOriginalContainer();
			$river_action_type = 'comment:reply';
			$river_target_guid = $original_entity->guid;
		} else {
			$river_action_type = 'comment';
			$river_target_guid = $entity->guid;
		}

		// Add to river
		elgg_create_river_item(array(
			'view' => 'river/object/comment/create',
			'action_type' => $river_action_type,
			'subject_guid' => $poster->guid,
			'object_guid' => $guid,
			'target_guid' => $river_target_guid,
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