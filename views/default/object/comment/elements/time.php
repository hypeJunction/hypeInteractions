<?php

use hypeJunction\Interactions\Comment;

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Comment) {
	return;
}

$time = elgg_view_friendly_time($entity->time_created);

echo elgg_format_element('span', array(
	'class' => 'interactions-comment-time',
		), $time);
