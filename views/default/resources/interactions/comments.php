<?php

namespace hypeJunction\Interactions;

$entity_guid = get_input('entity_guid', $vars);
$comment_guid = get_input('comment_guid', $vars);

$entity = get_entity($entity_guid);
/* @var $entity \ElggEntity */

if (!elgg_instanceof($entity)) {
	return true;
}

$comment = get_entity($comment_guid);
/* @var $comment Comment */

if (elgg_is_xhr()) {
	echo elgg_view('framework/interactions/comments', array(
		'entity' => $entity,
		'comment' => $comment,
		'active_tab' => ($comment_guid) ? 'comments' : false,
	));
} else {
	$title = elgg_echo('interactions:comments:title', array($entity->getDisplayName()));
	$content = elgg_view_entity($entity, array(
		'full_view' => false,
	));
	if (!$entity instanceof Comment) {
		$content .= elgg_view_comments($entity, true, array(
			'entity' => $entity,
			'comment' => $comment,
			'active_tab' => ($comment_guid) ? 'comments' : false,
		));
	}
	$layout = elgg_view_layout('content', array(
		'title' => $title,
		'content' => $content,
		'filter' => false,
		'sidebar' => false,
	));

	echo elgg_view_page($title, $layout);
}

