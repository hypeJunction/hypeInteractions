<?php

namespace hypeJunction\Inbox;

$entity = elgg_extract('entity', $vars);

$time = elgg_view_friendly_time($entity->time_created);

echo elgg_format_element('span', array(
	'class' => 'interactions-comment-time',
		), $time);
