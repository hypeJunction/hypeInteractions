<?php

namespace hypeJunction\Interactions;

use ElggEntity;

$entity = elgg_extract('entity', $vars, false);
/* @var $entity ElggEntity */

$comment = elgg_extract('comment', $vars, false);
/* @var $comment Comment */

if (!elgg_instanceof($entity)) {
	return true;
}

$show_form = elgg_extract('show_add_form', $vars, true) && $entity->canComment();
$expand_form = elgg_extract('expand_form', $vars, true);

$order = elgg_get_plugin_user_setting('comments_order', 0, 'hypeInteractions') ? : elgg_get_plugin_setting('comments_order', 'hypeInteractions');
$style = elgg_get_plugin_user_setting('comments_load_style', 0, 'hypeInteractions') ? : elgg_get_plugin_setting('comments_load_style', 'hypeInteractions');
if (elgg_is_xhr()) {
	$limit = elgg_get_plugin_setting('comments_limit', 'hypeInteractions');
	if (!$limit || $limit > 100) {
		$limit = 3;
	}
} else {
	$limit = elgg_get_plugin_setting('comments_load_limit', 'hypeInteractions');
	if (!$limit || $limit > 100) {
		$limit = 100;
	}
}

$offset_key = "comments_$entity->guid";
$offset = get_input($offset_key, null);
$count = $entity->countComments();

if (is_null($offset)) {
	if ($comment instanceof Comment) {
		$thread = new Thread($comment);
		$offset = $thread->getOffset($limit);
	} else {
		if (($order == 'asc' && $style == 'load_older') || ($order == 'desc' && $style == 'load_newer')) {
			// show last page
			$offset = $count - $limit;
			if ($offset < 0) {
				$offset = 0;
			}
		} else {
			// show first page
			$offset = 0;
		}
	}
}

if ($order == 'asc') {
	$order_by = 'e.time_created ASC';
	$reversed = true;
} else {
	$order_by = 'e.time_created DESC';
	$reversed = false;
}

$options = array(
	'types' => 'object',
	'subtypes' => array(Comment::SUBTYPE, 'hjcomment'),
	'container_guid' => $entity->guid,
	'list_id' => "interactions-comments-{$entity->guid}",
	'list_class' => 'interactions-comments-list',
	'base_url' => "stream/comments/$entity->guid",
	'order_by' => $order_by,
	'limit' => $limit,
	'offset' => $offset,
	'offset_key' => $offset_key,
	'full_view' => true,
	'pagination' => true,
	'pagination_type' => 'infinite',
	'lazy_load' => 0,
	'reversed' => $reversed,
	'auto_refresh' => 90,
	'no_results' => elgg_echo('interactions:comments:no_results'),
	'data-guid' => $entity->guid,
	'data-trait' => 'comments',
	'level' => elgg_extract('level', $vars) ? : 1,
);

elgg_push_context('comments');
$list = elgg_list_entities($options);
elgg_pop_context();

$form = '';
if ($show_form) {
	$form_class = [
		'interactions-form',
		'interactions-add-comment-form',
	];
	if (!$expand_form) {
		$form_class[] = 'hidden';
	}
	$form = elgg_view_form('comment/save', array(
		'class' => implode(' ', $form_class),
		'data-guid' => $entity->guid,
		'enctype' => 'multipart/form-data',
			), array(
		'entity' => $entity,
	));
}

$position = elgg_get_plugin_user_setting('comment_form_position', 0, 'hypeInteractions') ? : elgg_get_plugin_setting('comment_form_position', 'hypeInteractions');
if ($position == 'before') {
	echo $form . $list;
} else {
	echo $list . $form;
}