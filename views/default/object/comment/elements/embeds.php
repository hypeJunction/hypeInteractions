<?php

$entity = elgg_extract('entity', $vars);
/* @var $entity Comment */

$full = elgg_extract('full_view', $vars, false);
if (!$full) {
	return;
}

echo elgg_view('output/url_preview', [
	'value' => $entity->description,
]);