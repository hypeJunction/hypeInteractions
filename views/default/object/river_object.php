<?php

namespace hypeJunction\Interactions;

use ElggRiverItem;

$entity = elgg_extract('entity', $vars);
/* @var RiverObject $entity */

if (!$entity instanceof RiverObject) {
	return false;
}

$river_item = $entity->getRiverItem();
echo elgg_view_river_item($river_item);
