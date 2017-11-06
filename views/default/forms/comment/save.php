<?php

/**
 * Form for adding and editing comments
 *
 * @package Elgg
 *
 * @uses ElggEntity  $vars['entity']       The entity being commented
 * @uses ElggComment $vars['comment']      The comment being edited
 * @uses bool        $vars['inline']       Show a single line version of the form?
 * @uses bool        $vars['is_edit_page'] Is this form on its own page?
 */

namespace hypeJunction\Interactions;

if (!elgg_is_logged_in()) {
	return;
}


$entity = elgg_extract('entity', $vars);
/* @var $entity \ElggEntity */

$comment = elgg_extract('comment', $vars);
/* @var $comment Comment */

$owner = ($comment instanceof Comment) ? $comment->getOwnerEntity() : elgg_get_logged_in_user_entity();
/* @var $owner \ElggUser */

$icon = elgg_view_entity_icon($owner, 'small', array(
	'use_hover' => false,
	'use_link' => false,
		));

$body = elgg_view_field([
	'#type' => 'interactions/comment',
	'name' => 'generic_comment',
	'value' => $comment->description,
		]);

if (can_attach_files()) {
	$params = $vars;
	$params['expand'] = false;
	$params['#type'] = 'attachments';
	$body .= elgg_view_field($params);
}

$footer = '';
$footer .= elgg_view('input/submit', array(
	'value' => $comment ? elgg_echo('interactions:reply:create') : elgg_echo('generic_comments:post'),
		));

if ($comment instanceof Comment) {
	$footer .= elgg_view('input/button', array(
		'value' => elgg_echo('cancel'),
		'class' => 'elgg-button-cancel mll',
		'href' => $comment->getURL(),
	));
}

$footer = elgg_format_element('div', array(
	'class' => 'elgg-foot text-right',
		), $footer);

echo elgg_view_image_block($icon, $body . $footer, array(
	'class' => 'interactions-image-block',
));

echo elgg_view('input/hidden', array(
	'name' => 'comment_guid',
	'value' => $comment->guid,
));

echo elgg_view('input/hidden', array(
	'name' => 'entity_guid',
	'value' => ($comment) ? $comment->container_guid : $entity->guid,
));

