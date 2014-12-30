<?php

namespace hypeJunction\Interactions;

/**
 * Creates a commentable object associated with river items whose object is not ElggObject
 * 
 * @param string $event
 * @param string $type
 * @param ElggRiverItem $river
 * @return true
 */
function created_river_event($event, $type, $river) {
	create_actionable_river_object($river);
	return true;
}