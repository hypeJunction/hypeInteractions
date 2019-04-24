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

$commenter = $comment->getOwnerEntity();
$entity = $comment->getContainerEntity();
/* @var \ElggEntity $entity */

if (!$entity || !$commenter) {
	return;
}

$full = elgg_extract('full_view', $vars, true);

$icon = elgg_view('object/comment/elements/icon', $vars);

if ($full) {
	$body = elgg_view('object/comment/elements/body', $vars);

	$attachments = elgg_view('object/comment/elements/attachments', $vars);

	if (elgg_get_plugin_setting('enable_url_preview', 'hypeInteractions')) {
		$attachments .= elgg_view('object/comment/elements/embeds', $vars);
	}

	$comments = elgg_view_comments($comment, true, [
		'entity' => $comment->getContainerEntity(),
		'comment' => $comment,
		'active_tab' => elgg_extract('active_tab', $vars),
		'level' => elgg_extract('level', $vars),
	]);

	$poster = $comment->getOwnerEntity();
	/* @var $owner ElggUser */

	$poster_text = elgg_echo('byline', [$poster->name]);
	$posted = elgg_view_friendly_time($comment->time_created);
	$date = elgg_view('output/url', [
		'text' => $posted,
		'href' => $comment->getURL(),
	]);

	$metadata = '';
	if (!elgg_is_active_plugin('hypeUI')) {
		if (!elgg_in_context('widgets')) {
			$metadata = elgg_view_menu('entity', [
				'entity' => $comment,
				'sort_by' => 'priority',
				'class' => 'elgg-menu-hz',
			]);
		}
		$body = elgg_view('object/elements/summary', $vars + [
				'title' => false,
				'subtitle' => "$poster_text $date",
				'content' => $body . $attachments . $comments,
				'metadata' => $metadata,
			]);
		$body = elgg_view_image_block($icon, $body, [
			'class' => 'interactions-image-block',
		]);
	} else {
		$body = elgg_view('object/elements/summary', array_merge($vars, [
			'entity' => $comment,
			'icon' => elgg_view_entity_icon($commenter, 'small'),
			'access' => false,
			'title' => false,
			'content' => false,
			'inline_content' => $body . $attachments . $comments,
			'social' => false,
			'class' => 'elgg-comment',
		]));
	}

	$attrs = [
		'data-guid' => $comment->guid,
		'class' => elgg_extract_class($vars, 'interactions-comment'),
	];

	echo elgg_format_element('div', $attrs, $body);
} else {

	$friendlytime = elgg_view_friendly_time($comment->time_created);

	$commenter_icon = elgg_view_entity_icon($commenter, 'tiny');
	$commenter_link = "<a href=\"{$commenter->getURL()}\">$commenter->name</a>";

	$entity_title = $entity->title ? $entity->title : elgg_echo('untitled');
	$entity_link = "<a href=\"{$entity->getURL()}\">$entity_title</a>";

	$excerpt = elgg_get_excerpt($comment->description, 80);
	$posted = elgg_echo('generic_comment:on', [$commenter_link, $entity_link]);

	$body = elgg_format_element('span', ['class' => 'elgg-subtext'], "$posted ($friendlytime): $excerpt");
	echo elgg_view_image_block($commenter_icon, $body);
}
