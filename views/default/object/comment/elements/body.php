<?php

namespace hypeJunction\Interactions;

$entity = elgg_extract('entity', $vars);
/* @var $entity Comment */

$body = $entity->description;
$body = filter_tags($body);
$body = elgg_autop($body);

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