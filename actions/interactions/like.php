<?php

namespace hypeJunction\Interactions;

$guid = get_input('guid');
$entity = get_entity($guid);

if (elgg_annotation_exists($entity->guid, 'likes')) {
	action('likes/delete');
} else {
	action('likes/add');
}