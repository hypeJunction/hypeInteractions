<?php

namespace hypeJunction\Interactions;

$item = $vars['item'];

$object = $item->getObjectEntity();
$subject = $item->getSubjectEntity();

$subject_link = elgg_view('output/url', array(
	'href' => $subject->getURL(),
	'text' => $subject->name,
	'class' => 'elgg-river-subject',
	'is_trusted' => true,
		));

$object_link = elgg_view('output/url', array(
	'href' => $object->getURL(),
	'text' => $object->getDisplayName(),
	'class' => 'elgg-river-target',
	'is_trusted' => true,
		));

$type = $object->getType();
$subtype = $object->getSubtype() ? $object->getSubtype() : 'default';
$key = "interactions:like:$type:$subtype";
$summary = elgg_echo($key, array($subject_link, $object_link));
if ($summary == $key) {
	$key = "interactions:like:$type:default";
	$summary = elgg_echo($key, array($subject_link, $object_link));
}

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'summary' => $summary,
	'responses' => false,
));
