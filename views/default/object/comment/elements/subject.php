<?php

use hypeJunction\Interactions\Comment;

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Comment) {
	return;
}

echo elgg_format_element('span', [
	'class' => 'interactions-comment-subject',
		], $entity->getDisplayName());
