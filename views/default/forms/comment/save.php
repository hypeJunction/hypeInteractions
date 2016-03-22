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
/* @var $entity ElggEntity */

$comment = elgg_extract('comment', $vars);
/* @var $comment Comment */

$owner = ($comment instanceof Comment) ? $comment->getOwnerEntity() : elgg_get_logged_in_user_entity();
/* @var $owner ElggUser */

$icon = elgg_view_entity_icon($owner, 'small', array(
	'use_hover' => false,
	'use_link' => false,
		));

$body = elgg_view('input/plaintext', array(
	'name' => 'generic_comment',
	'value' => $comment->description,
	'rows' => 2,
	'placeholder' => elgg_echo('generic_comments:add'),
		));

$footer_controls = array();

if (elgg_view_exists('input/dropzone')) {
	$uploads_form = elgg_view('input/dropzone', array(
		'name' => 'uploads',
		'max' => 25,
		'multiple' => true,
	));

	$body .= elgg_format_element('fieldset', array(
		'class' => 'interactions-form-fieldset-uploads',
			), $uploads_form);

	$footer_controls['uploads'] = elgg_view('output/url', array(
		'text' => elgg_echo('interactions:comment:upload'),
		'href' => 'javascript:void(0);',
	));
}

if ($comment instanceof Comment) {
	$footer_controls['cancel'] = elgg_view('input/button', array(
		'value' => elgg_echo('cancel'),
		'class' => 'elgg-button-cancel',
		'href' => $comment->getURL(),
	));
}

$footer_controls['submit'] = elgg_view('input/submit', array(
	'value' => $entity instanceof Comment ? elgg_echo('interactions:reply:create') : elgg_echo('generic_comments:post'),
		));

$controls = '';
foreach ($footer_controls as $name => $footer_control) {
	$controls .= elgg_format_element('li', array(
		'class' => "interactions-form-control-$name",
			), $footer_control);
}

$footer .= elgg_format_element('ul', array(
	'class' => 'interactions-form-controls',
		), $controls);

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

