<?php

namespace hypeJunction\Interactions;

$guid = get_input('guid', $vars);

$comment = get_entity($guid);
/* @var Comment $comment */

if (!$comment instanceof Comment) {
	return true;
}

$entity = $comment->getContainerEntity();

$content = elgg_view_form('comment/save', array(
	'class' => 'interactions-form interactions-edit-comment-form',
	'data-guid' => $entity->guid,
	'data-comment-guid' => $comment->guid,
	'enctype' => 'multipart/form-data',
		), array(
	'entity' => $entity,
	'comment' => $comment,
));

if (elgg_is_xhr()) {
	echo $content;
} else {
	$title = elgg_echo('interactions:comments:edit:title');
	$layout = elgg_view_layout('content', array(
		'title' => $title,
		'content' => $content,
		'filter' => false,
		'sidebar' => false,
	));

	echo elgg_view_page($title, $layout);
}

