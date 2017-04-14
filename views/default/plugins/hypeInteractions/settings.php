<?php

namespace hypeJunction\Interactions;

$entity = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('interactions:settings:max_comment_depth'),
	'#help' => elgg_echo('interactions:settings:max_comment_depth:help'),
	'name' => 'params[max_comment_depth]',
	'value' => $entity->max_comment_depth,
	'options' => array(5, 4, 3, 2, 1),
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('interactions:settings:comment_form_position'),
	'#help' => elgg_echo('interactions:settings:comment_form_position:help'),
	'name' => 'params[comment_form_position]',
	'value' => $entity->comment_form_position,
	'options_values' => array(
		'before' => elgg_echo('interactions:settings:comment_form_position:before'),
		'after' => elgg_echo('interactions:settings:comment_form_position:after'),
	),
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('interactions:settings:comments_order'),
	'#help' => elgg_echo('interactions:settings:comments_order:help'),
	'name' => 'params[comments_order]',
	'value' => $entity->comments_order,
	'options_values' => array(
		'asc' => elgg_echo('interactions:settings:comments_order:chronological'),
		'desc' => elgg_echo('interactions:settings:comments_order:reverse_chronological'),
	),
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('interactions:settings:comments_load_style'),
	'name' => 'params[comments_load_style]',
	'value' => $entity->comments_load_style,
	'options_values' => array(
		'load_newer' => elgg_echo('interactions:settings:comments_load_style:load_newer'),
		'load_older' => elgg_echo('interactions:settings:comments_load_style:load_older'),
	),
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('interactions:settings:comments_limit'),
	'name' => 'params[comments_limit]',
	'value' => $entity->comments_limit,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('interactions:settings:comments_load_limit'),
	'name' => 'params[comments_load_limit]',
	'value' => $entity->comments_load_limit,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('interactions:settings:comments_visual_editor'),
	'name' => 'params[comments_visual_editor]',
	'value' => $entity->comments_visual_editor,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	)
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('interactions:settings:comment_sort'),
	'#help' => elgg_echo('interactions:settings:comment_sort:help'),
	'name' => 'params[comment_sort]',
	'value' => $entity->comment_sort,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	)
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('interactions:settings:default_expand'),
	'#label' => elgg_echo('interactions:settings:default_expand:help'),
	'name' => 'params[default_expand]',
	'value' => $entity->default_expand,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	),
]);


if (elgg_is_active_plugin('hypeAttachments')) {
	echo elgg_view_field([
		'#type' => 'select',
		'#label' => elgg_echo('interactions:settings:enable_attachments'),
		'#help' => elgg_echo('interactions:settings:enable_attachments:help'),
		'name' => 'params[enable_attachments]',
		'value' => $entity->enable_attachments,
		'options_values' => array(
			0 => elgg_echo('option:no'),
			1 => elgg_echo('option:yes'),
		),
	]);
}

if (elgg_is_active_plugin('hypeScraper')) {
	echo elgg_view_field([
		'#type' => 'select',
		'#label' => elgg_echo('interactions:settings:enable_url_preview'),
		'#help' => elgg_echo('interactions:settings:enable_url_preview:help'),
		'name' => 'params[enable_url_preview]',
		'value' => $entity->enable_url_preview,
		'options_values' => array(
			0 => elgg_echo('option:no'),
			1 => elgg_echo('option:yes'),
		),
	]);
}

$dbprefix = elgg_get_config('dbprefix');
$query = "SELECT DISTINCT view FROM {$dbprefix}river";
$data = get_data($query);

$view_fields = [];
foreach ($data as $row) {
	$view = $row->view;
	$view_fields[] = [
		'#type' => 'checkbox',
		'label' => $view,
		'name' => "params[stream_object:$view]",
		'checked' => (bool) $entity->{"stream_object:$view"},
		'value' => 1,
	];
}

echo elgg_view_field([
	'#type' => 'fieldset',
	'#label' => elgg_echo('interactions:settings:actionable_stream_object'),
	'#help' => elgg_echo('interactions:settings:actionable_stream_object:help'),
	'fields' => $view_fields,
]);
