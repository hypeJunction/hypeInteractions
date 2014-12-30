<?php

namespace hypeJunction\Interactions;

use ElggEntity;

$entity = elgg_extract('entity', $vars, false);
/* @var $entity ElggEntity */

if (!elgg_instanceof($entity)) {
	return true;
}

$handler = HYPEINTERACTIONS_HANDLER;

$limit = get_input('limit', 20);
$offset_key = "likes_$entity->guid";
$offset = get_input($offset_key, 0);
$count = $entity->countAnnotations('likes');

$options = array(
	'guid' => $entity->guid,
	'annotation_names' => 'likes',
	'list_id' => "interactions-likes-{$entity->guid}",
	'list_class' => 'interactions-likes-list',
	'base_url' => "$handler/likes/$entity->guid",
	'limit' => $limit,
	'offset' => $offset,
	'offset_key' => $offset_key,
	'pagination' => true,
	'pagination_type' => 'infinite',
	'lazy_load' => 3,
	'auto_refresh' => 30,
	'data-selector-delete' => '.elgg-requires-confirmation:has(.elgg-icon-delete)',
);

$options['items'] = elgg_get_annotations($options);
$options['count'] = $count;

if (elgg_view_exists('page/components/ajax_list')) {
	echo elgg_view('page/components/ajax_list', $options);
} else {
	echo elgg_view('page/components/list', $options);
}