<?php

/**
 * Display a message
 * @uses $vars['entity']     Comment
 * @uses $vars['active_tab'] Active tab
 */

namespace hypeJunction\Interactions;

$comment = elgg_extract('entity', $vars);
/* @var Comment $comment */

if (!$comment instanceof Comment) {
	return true;
}

$entity = $comment->getContainerEntity();
/* @var \ElggEntity $entity */

$full = elgg_extract('full_view', $vars, true);

$icon = elgg_view('object/comment/elements/icon', $vars);

if ($full) {
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

	$poster = $comment->getOwnerEntity();
	/* @var $owner ElggUser */

	$poster_text = elgg_echo('byline', array($poster->name));
	$date = elgg_view_friendly_time($comment->time_created);

	$body = elgg_view('object/elements/summary', array(
		'entity' => $comment,
		'title' => false,
		'subtitle' => "$poster_text $date",
		'content' => $body . $attachments . $comments,
	));

	$body = elgg_view_image_block($icon, $body, array(
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
} else {

	$friendlytime = elgg_view_friendly_time($comment->time_created);
	
	$commenter = $comment->getOwnerEntity();
	$commenter_icon = elgg_view_entity_icon($commenter, 'tiny');
	$commenter_link = "<a href=\"{$commenter->getURL()}\">$commenter->name</a>";

	$entity_title = $entity->title ? $entity->title : elgg_echo('untitled');
	$entity_link = "<a href=\"{$entity->getURL()}\">$entity_title</a>";

	$excerpt = elgg_get_excerpt($comment->description, 80);
	$posted = elgg_echo('generic_comment:on', array($commenter_link, $entity_link));

	$body = <<<HTML
<span class="elgg-subtext">
	$posted ($friendlytime): $excerpt
</span>
HTML;

	echo elgg_view_image_block($commenter_icon, $body);
}
