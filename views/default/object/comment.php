<?php

/**
 * Display a message
 * @uses $vars['entity']     Comment
 * @uses $vars['active_tab'] Active tab
 */

namespace hypeJunction\Interactions;

$comment = elgg_extract('entity', $vars);
/* @var Comment $comment */

$full = elgg_extract('full_view', $vars, true);

$icon = elgg_view('object/comment/elements/icon', $vars);

$metadata = $content = array();

$body = elgg_view('object/comment/elements/body', $vars);

$attachments = elgg_view('object/comment/elements/attachments', $vars);
if (!$attachments) {
	$attachments = elgg_view('object/comment/elements/embeds', $vars);
}

$comments = elgg_view_comments($comment, true, array(
	'entity' => $comment->getContainerEntity(),
	'comment' => $comment,
	'active_tab' => elgg_extract('active_tab', $vars),
		));

$body = elgg_view_image_block($icon, $body . $attachments . $comments, array(
	'class' => 'interactions-image-block',
		));

$attrs = elgg_format_attributes(array(
	'data-guid' => $comment->guid,
	'class' => implode(' ', array_filter(array(
		elgg_extract('class', $vars, null),
		'interactions-comment',
	))),
		));

echo "<article $attrs>$body</article>";
