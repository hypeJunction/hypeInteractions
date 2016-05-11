<?php

namespace hypeJunction\Interactions;

$entity = elgg_extract('entity', $vars, false);
/* @var $entity ElggEntity */

$full_view = elgg_extract('full_view', $vars, false);

$expand_form = $full_view;
$level = elgg_extract('level', $vars) + 1;
$vars['level'] = $level;
$active_tab = elgg_extract('active_tab', $vars, get_input('active_tab'));
if (!isset($active_tab)) {
	if (elgg_in_context('activity') && $level > 1) {
		$active_tab = false;
	} else if ($entity->countComments()) {
		if ($full_view || elgg_get_plugin_setting('default_expand', 'hypeInteractions')) {
			$active_tab = 'comments';
			$expand_form = true;
		}
	}
}

$menu = elgg_view_menu('interactions', array(
	'entity' => $entity,
	'class' => 'elgg-menu-hz',
	'sort_by' => 'priority',
	'active_tab' => $active_tab,
		));

if (empty($menu)) {
	return;
}

$controls = elgg_format_element('div', array(
	'class' => 'interactions-controls',
		), $menu);

$modules = array_filter(array(
	'comments',
	'likes',
		));

if ($active_tab) {
	if (!isset($vars['expand_form'])) {
		$vars['expand_form'] = $expand_form;
	}
	$content = elgg_view("framework/interactions/$active_tab", $vars);
	$component = elgg_format_element('div', array(
		'class' => 'interactions-component elgg-state-selected',
		'data-trait' => $active_tab,
			), $content);
}


echo elgg_format_element('div', array(
	'class' => 'interactions' . (($active_tab) ? ' interactions-has-active-tab' : ''),
		), $controls . $component);
?>
<script>require(['page/components/interactions']);</script>