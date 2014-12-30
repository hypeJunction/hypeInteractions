<?php

namespace hypeJunction\Inbox;

use ElggFile;

$entity = elgg_extract('entity', $vars);
/* @var Comment $entity */

$full = elgg_extract('full_view', $vars, true);
if (!$full) {
	return true;
}

$count = $entity->hasAttachments(array());
if (!$count) {
	return true;
}

$handler = HYPEINTERACTIONS_HANDLER;

$attachments = $entity->getAttachments(array('limit' => 0));

elgg_push_context('gallery');
$items = array();
foreach ($attachments as $attachment) {
	$item = elgg_view_entity_icon($attachment, 'large', array(
		'list_type' => 'gallery'
	));
	if ($entity->canEdit()) {
		if ($attachment instanceof ElggFile && $attachment->canEdit() && $attachment->container_guid == $entity->guid) {
			$action = "action/file/delete?guid=$attachment->guid";
			$title = elgg_echo('delete');
		} else {
			$action = "action/$handler/detach?guid_one=$entity->guid&guid_two=$attachment->guid";
			$title = elgg_echo('interactions:detach');
		}
		$item .= elgg_view('output/url', array(
			'text' => elgg_view_icon('delete'),
			'href' => $action,
			'is_action' => true,
			'class' => 'interactions-detach-action',
		));
	}
	$items[] = elgg_format_element('li', array(
		'class' => 'interactions-attachment',
			), $item);
}
elgg_pop_context();

echo '<ul class="interactions-attachments">';
echo implode('', $items);
echo '</ul>';
