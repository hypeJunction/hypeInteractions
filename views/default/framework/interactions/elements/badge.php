<?php

namespace hypeJunction\Interactions;

$entity = elgg_extract('entity', $vars);
$type = elgg_extract('type', $vars, 'default');
$text = elgg_extract('text', $vars, '');
$icon = elgg_extract('icon', $vars, '');
$count = elgg_extract('count', $vars, 0);

if ($text) {
	$text = elgg_format_element('span', array(), $text);
}

if ($icon) {
	$text = elgg_format_element('span', array(
				'class' => "interactions-icon interactions-icon-$icon"
			)) . $text;
}

$badge = elgg_format_element('span', array(
	'class' => 'interactions-badge-text',
		), $text);

if ($count !== false) {
	if ($count > 999) {
		$size = array('', 'k', 'mil');
		$factor = floor((strlen($count) - 1) / 3);
		$str = ($count < 5000) ? "%.1f" : "%f";
		$count = sprintf($str, $count / pow(1000, $factor)) . $size[$factor];
	}
	$badge .= elgg_format_element('span', array(
		'class' => 'interactions-badge-indicator'
			), $count);
}

echo elgg_format_element('span', array(
	'class' => "interactions-badge interactions-badge-$type",
	'data-guid' => $entity->guid,
	'data-trait' => $type,
		), $badge);
