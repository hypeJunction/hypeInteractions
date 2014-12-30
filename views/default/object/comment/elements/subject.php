<?php

namespace hypeJunction\Inbox;

$entity = elgg_extract('entity', $vars);
/* @var Comment $comment */

echo elgg_format_element('span', array(
	'class' => 'interactions-comment-subject',
		), $entity->getDisplayName());
