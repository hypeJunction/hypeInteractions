<?php

$entity = elgg_extract('entity', $vars);
/* @var $entity ElggPlugin */

$user = elgg_extract('user', $vars);

echo elgg_view_input('select', array(
	'name' => 'params[comment_form_position]',
	'value' => $entity->getUserSetting('comment_form_position', $user->guid, 'after'),
	'options_values' => array(
		'before' => elgg_echo('interactions:settings:comment_form_position:before'),
		'after' => elgg_echo('interactions:settings:comment_form_position:after'),
	),
	'label' => elgg_echo('interactions:settings:comment_form_position'),
	'help' => elgg_echo('interactions:settings:comment_form_position:help'),
));

echo elgg_view_input('select', [
	'name' => 'params[comments_order]',
	'value' => hypeJunction\Interactions\InteractionsService::getCommentsSort(),
	'options_values' => [
		'time_created::desc' => elgg_echo('sort:object:time_created::desc'),
		'time_created::asc' => elgg_echo('sort:object:time_created::asc'),
		'likes_count::desc' => elgg_echo('sort:object:likes_count::desc'),
	],
	'label' => elgg_echo('interactions:settings:comments_order'),
	'help' => elgg_echo('interactions:settings:comments_order:help'),
]);

echo elgg_view_input('select', array(
	'name' => 'params[comments_load_style]',
	'value' => hypeJunction\Interactions\InteractionsService::getLoadStyle(),
	'options_values' => array(
		'load_newer' => elgg_echo('interactions:settings:comments_load_style:load_newer'),
		'load_older' => elgg_echo('interactions:settings:comments_load_style:load_older'),
	),
	'label' => elgg_echo('interactions:settings:comments_load_style'),
));

