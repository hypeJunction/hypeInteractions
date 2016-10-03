<?php

namespace hypeJunction\Interactions;

$entity = elgg_extract('entity', $vars);
/* @var $entity Comment */

if (elgg_is_active_plugin('search') && get_input('query')) {
	if ($entity->getVolatileData('search_matched_description')) {
		$body = $entity->getVolatileData('search_matched_description');
	} else {
		$body = search_get_highlighted_relevant_substrings($entity->description, get_input('query'), 5, 5000);
	}
} else {
	$body = elgg_view('output/longtext', [
		'value' => $entity->description,
	]);
}

if (elgg_view_exists('output/linkify')) {
	$body = elgg_view('output/linkify', array(
		'value' => $body
	));
}

$body = elgg_format_element('span', array(
	'class' => 'interactions-comment-text',
	'data-role' => 'comment-text',
), $body);

//$body = elgg_echo('interactions:comment:body', array($owner_link, $body));

echo elgg_format_element('div', array(
	'class' => 'interactions-comment-body',
), $body);
