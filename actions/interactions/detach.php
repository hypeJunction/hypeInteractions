<?php

/**
 * Detach an entity from a comment
 */

namespace hypeJunction\Interactions;

$guid_one = get_input('guid_one');
$comment = get_entity($guid_one);
/* @var Comment $comment */

$guid_two = get_input('guid_two');
$attachment = get_entity($guid_two);
/* @var ElggEntity $attachment */

$result = false;
if ($comment && $attachment && $comment->canEdit()) {
	$result = $comment->removeRelationship($attachment->guid, 'attached');
}

if ($result) {
	system_message(elgg_echo('interactions:detach:success'));
} else {
	register_error(elgg_echo('interactions:detach:error'));
}

forward(REFERER);
