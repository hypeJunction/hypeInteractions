<?php

$entity = elgg_extract('entity', $vars);
/* @var $entity ElggPlugin */

$user = elgg_extract('user', $vars);

echo '<div>';
echo '<label>' . elgg_echo('interactions:settings:comment_form_position') . '</label>';
echo '<div class="elgg-text-help">' . elgg_echo('interactions:settings:comment_form_position:help') . '</div>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[comment_form_position]',
	'value' => $entity->getUserSetting('comment_form_position', $user->guid, 'after'),
	'options_values' => array(
		'before' => elgg_echo('interactions:settings:comment_form_position:before'),
		'after' => elgg_echo('interactions:settings:comment_form_position:after'),
	),
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('interactions:settings:comments_order') . '</label>';
echo '<div class="elgg-text-help">' . elgg_echo('interactions:settings:comments_order:help') . '</div>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[comments_order]',
	'value' => $entity->getUserSetting('comments_order', $user->guid, 'asc'),
	'options_values' => array(
		'asc' => elgg_echo('interactions:settings:comments_order:chronological'),
		'desc' => elgg_echo('interactions:settings:comments_order:reverse_chronological'),
	),
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('interactions:settings:comments_load_style') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[comments_load_style]',
	'value' => $entity->getUserSetting('comments_load_style', $user->guid, 'load_older'),
	'options_values' => array(
		'load_newer' => elgg_echo('interactions:settings:comments_load_style:load_newer'),
		'load_older' => elgg_echo('interactions:settings:comments_load_style:load_older'),
	),
));
echo '</div>';
