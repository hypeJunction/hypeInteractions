<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
}

$owner = $entity->getOwnerEntity();
echo elgg_view_entity_icon($owner, 'small', [
	'use_hover' => false,
]);