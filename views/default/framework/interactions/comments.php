<?php

/**
 * @uses $vars['entity']        Entity whose comments thread is being displayed
 * @uses $vars['comment']       Comment entity being deep linked
 * @uses $vars['show_add_form'] Display a form to add a new comment
 * @uses $vars['expand_form']   Collapse/expand the form
 */
namespace hypeJunction\Interactions;

use ElggEntity;

$entity = elgg_extract('entity', $vars, false);
/* @var $entity ElggEntity */

$comment = elgg_extract('comment', $vars, false);
/* @var $comment Comment */

if (!elgg_instanceof($entity)) {
	return;
}

$full_view = elgg_extract('full_view', $vars, false);
$show_form = elgg_extract('show_add_form', $vars, true) && $entity->canComment();
$expand_form = elgg_extract('expand_form', $vars, !elgg_in_context('widgets'));

$sort = InteractionsService::getCommentsSort();
$style = InteractionsService::getLoadStyle();
$form_position = InteractionsService::getCommentsFormPosition();
$limit = elgg_extract('limit', $vars, InteractionsService::getLimit(!$full_view));

$offset_key = "comments_$entity->guid";
$offset = get_input($offset_key, null);

$count = $entity->countComments();

if (!isset($offset)) {
	$offset = InteractionsService::calculateOffset($count, $limit, $comment);
}

$level = elgg_extract('level', $vars) ? : 1;

$options = array(
	'types' => 'object',
	'subtypes' => array(Comment::SUBTYPE, 'hjcomment'),
	'container_guid' => $entity->guid,
	'list_id' => "interactions-comments-{$entity->guid}",
	'list_class' => 'interactions-comments-list elgg-comments',
	'base_url' => elgg_normalize_url("stream/comments/$entity->guid"),
	'limit' => $limit,
	'offset' => $offset,
	'offset_key' => $offset_key,
	'full_view' => true,
	'pagination' => true,
	'pagination_type' => 'infinite',
	'lazy_load' => 0,
	'reversed' => $sort == 'time_created::asc',
	'auto_refresh' => 90,
	'no_results' => elgg_echo('interactions:comments:no_results'),
	'data-guid' => $entity->guid,
	'data-trait' => 'comments',
	'level' => $level,
);
	
elgg_push_context('comments');
$allow_sort = $level == 1 && (bool) elgg_get_plugin_setting('comment_sort', 'hypeInteractions');
$list = elgg_view('lists/objects', [
	'options' => $options,
	'show_filter' => $allow_sort,
	'show_sort' => $allow_sort,
	'show_search' => $allow_sort,
	'expand_form' => false,
	'sort_options' => [
		'time_created::desc',
		'time_created::asc',
		'likes_count::desc',
	],
	'sort' => get_input('sort', $sort),
]);
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

if ($form_position == 'before') {
	echo $form . $list;
} else {
	echo $list . $form;
}