<?php

/**
 * A hack to display interactions module, even when blog comments are off
 */
namespace hypeJunction\Interactions;

$entity = elgg_extract('entity', $vars);

if ($entity->comments_on === 'Off' || $entity->comments_on === false) {
	echo elgg_view_comments($entity, true, $vars);
}