<?php

namespace hypeJunction\Interactions;

$guid = get_input('guid', $vars);

$entity = get_entity($guid);
/* @var $entity \ElggEntity */

if (!elgg_instanceof($entity)) {
	return true;
}

if (elgg_is_xhr()) {
	echo elgg_view('framework/interactions/likes', array(
		'entity' => $entity,
		'active_tab' => ($comment) ? 'likes' : false,
	));
} else {
	$title = elgg_echo('interactions:likes:title', array($entity->getDisplayName()));
	$content = elgg_view_entity($entity, array(
		'full_view' => false,
	));
	if (!$entity instanceof Comment) {
		$content .= elgg_view_comments($entity, true, array(
			'entity' => $entity,
			'active_tab' => 'likes',
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

