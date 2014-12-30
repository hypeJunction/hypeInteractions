<?php

namespace hypeJunction\Interactions;

$comment_guid = get_input('guid');
$comment = get_entity($comment_guid);
/* @var Comment $comment */

if ($comment instanceof Comment && $comment->canEdit()) {
	$entity = $comment->getContainerEntity();
	
	if ($comment->delete()) {
		if (elgg_is_xhr()) {
			$output = array(
				'guid' => $comment_guid,
				'stats' => get_stats($entity),
			);
			echo json_encode($output);
		}
		system_message(elgg_echo('generic_comment:deleted'));
	} else {
		register_error(elgg_echo('generic_comment:notdeleted'));
	}
} else {
	register_error(elgg_echo('generic_comment:notfound'));
}

forward(REFERER);
