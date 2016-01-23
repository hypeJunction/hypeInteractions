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

$handler = HYPEINTERACTIONS_HANDLER;

$show_form = elgg_extract('show_add_form', $vars, true) && $entity->canComment();

$limit = (elgg_is_xhr()) ? HYPEINTERACTIONS_COMMENTS_LOAD_LIMIT : HYPEINTERACTIONS_COMMENTS_LIMIT;
$offset_key = "comments_$entity->guid";
$offset = get_input($offset_key, null);
$count = $entity->countComments();

if (is_null($offset)) {
	if ($comment instanceof Comment) {
		$thread = new Thread($comment);
		$offset = $thread->getOffset($limit);
	} else {
		if ((HYPEINTERACTIONS_COMMENTS_ORDER == 'asc' && HYPEINTERACTIONS_COMMENTS_LOAD_STYLE == 'load_older') || (HYPEINTERACTIONS_COMMENTS_ORDER == 'desc' && HYPEINTERACTIONS_COMMENTS_LOAD_STYLE == 'load_newer')) {
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

if (HYPEINTERACTIONS_COMMENTS_ORDER == 'asc') {
	$order_by = 'e.time_created ASC';
	$reversed = true;
} else {
	$order_by = 'e.time_created DESC';
	$reversed = true;
}

$options = array(
	'types' => 'object',
	'subtypes' => array(Comment::SUBTYPE, 'hjcomment'),
	'container_guid' => $entity->guid,
	'list_id' => "interactions-comments-{$entity->guid}",
	'list_class' => 'interactions-comments-list',
	'base_url' => "$handler/comments/$entity->guid",
	'order_by' => $order_by,
	'limit' => $limit,
	'offset' => $offset,
	'offset_key' => $offset_key,
	'full_view' => true,
	'pagination' => true,
	'pagination_type' => 'infinite',
	'lazy_load' => 3,
	'reversed' => $reversed,
	'auto_refresh' => 30,
	'no_results' => elgg_echo('interactions:comments:no_results'),
	'data-guid' => $entity->guid,
	'data-trait' => 'comments',
);

elgg_push_context('comments');
$list = elgg_list_entities($options);
elgg_pop_context();

$form = '';
if ($show_form) {
	$form = elgg_view_form('comment/save', array(
		'class' => 'interactions-form interactions-add-comment-form',
		'data-guid' => $entity->guid,
		'enctype' => 'multipart/form-data',
			), array(
		'entity' => $entity,
	));
}

if (HYPEINTERACTIONS_COMMENT_FORM_POSITION == 'before') {
	echo $form . $list;
} else {
	echo $list . $form;
}