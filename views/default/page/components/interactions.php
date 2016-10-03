<?php

/**
 * @uses $vars['entity'] Entity whose interactions are being displayed
 * @uses $vars['full_view'] Is this is full entity listing
 * @uses $vars['level'] Threading level that this view is being called from
 * @uses $vars['active_tab'] Current active tab
 * @uses $vars['limit'] Number of items to show in a tab
 * @uses $vars['expand_form'] Expand add form (if any)
 */
namespace hypeJunction\Interactions;

$entity = elgg_extract('entity', $vars, false);
/* @var $entity ElggEntity */

$full_view = elgg_extract('full_view', $vars, true);
$partial = elgg_in_context('activity') || elgg_in_context('widgets') || !$full_view;

$expand_form = $full_view;

$level = elgg_extract('level', $vars) + 1;
$vars['level'] = $level;

$active_tab = elgg_extract('active_tab', $vars, get_input('active_tab'));

if (!isset($active_tab)) {
	if ($partial && $level > 1) {
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

$class = ['interactions'];

if ($active_tab) {
	if (!isset($vars['expand_form'])) {
		$vars['expand_form'] = $expand_form;
	}
	if (!isset($vars['limit'])) {
		$vars['limit'] = InteractionsService::getLimit($partial);
	}
	$content = elgg_view("framework/interactions/$active_tab", $vars);
	$component = elgg_format_element('div', array(
		'class' => 'interactions-component elgg-state-selected',
		'data-trait' => $active_tab,
			), $content);

	$class[] = 'interactions-has-active-tab';
}

echo elgg_format_element('div', array(
	'class' => $class,
	'id' => 'comments',
		), $controls . $component);
?>

<script>require(['page/components/interactions']);</script>