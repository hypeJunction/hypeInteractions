<?php

namespace hypeJunction\Interactions;

use ElggUser;

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

$owner = $entity->getOwnerEntity();
/* @var $owner ElggUser */

$owner_link = '';
if ($owner) {
	$owner_link = elgg_view('output/url', array(
		'href' => $owner->getURL(),
		'text' => $owner->getDisplayName(),
		'class' => 'interactions-comment-author-link',
	));
}

$body = elgg_echo('interactions:comment:body', array($owner_link, $body));

$time = elgg_view('object/comment/elements/time', $vars);

echo elgg_format_element('div', array(
	'class' => 'interactions-comment-body',
), $body . $time);