<?php

use hypeJunction\Interactions\RiverObject;

$guid = elgg_extract('guid', $vars);
$entity = get_entity($guid);

if (!$entity instanceof RiverObject) {
	forward('', '404');
}

$container = $entity->getContainerEntity();
elgg_set_page_owner_guid($container->guid);

if ($container instanceof ElggGroup) {
	elgg_push_breadcrumb($container->getDisplayName(), $container->getURL());
	elgg_push_breadcrumb(elgg_echo('activity'), "activity/group/$container->guid");
} else if ($container instanceof ElggUser) {
	elgg_push_breadcrumb($container->getDisplayName(), $container->getURL());
	elgg_push_breadcrumb(elgg_echo('activity'), "activity/owner/$container->username");
}

$title = $entity->getDisplayName();
elgg_push_breadcrumb($title);

$river = elgg_get_river([
	'ids' => (int) $entity->river_id,
	'limit' => 1,
]);

if (!$river) {
	forward('', '404');
}

$item = array_shift($river);

$content = elgg_view($item->getView(), [
	'item' => $item,
	'responses' => false,
]);

if (!preg_match('/elgg-comments/', $content)) {
	$content .= elgg_view_comments($entity);
}

$layout = elgg_view_layout('content', [
	'title' => $title,
	'content' => $content,
	'filter' => '',
]);

echo elgg_view_page($title, $layout);
