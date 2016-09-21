<?php

namespace hypeJunction\Interactions;

use ElggEntity;
use ElggObject;
use ElggRiverItem;

/**
 * Creates an object associated with a river item for commenting and other purposes
 * This is a workaround for river items that do not have an object or have an object that is group or user
 * 
 * @param ElggRiverItem $river River item
 * @return RiverObject|false
 * @deprecated 4.2
 */
function create_actionable_river_object(ElggRiverItem $river) {
	return InteractionsService::createActionableRiverObject($river);
}

/**
 * Get an actionable object associated with the river item
 * This could be a river object entity or a special entity that was created for this river item
 *
 * @param ElggRiverItem $river River item
 * @return ElggObject|false
 * @deprecated 4.2
 */
function get_river_object(ElggRiverItem $river) {
	return InteractionsService::getRiverObject($river);
}

/**
 * Get interaction statistics
 *
 * @param ElggEntity $entity Entity
 * @return array
 * @deprecated 4.2
 */
function get_stats($entity) {
	return InteractionsService::getStats($entity);
}

/**
 * Get entity URL wrapped in an <a></a> tag
 * @return string
 * @deprecated 4.2
 */
function get_linked_entity_name($entity) {
	return InteractionsService::getLinkedEntityName($entity);
}

/**
 * Check if attachments are enabled
 * @return bool
 * @deprecated 4.2
 */
function can_attach_files() {
	return Permissions::canAttachFiles();
}
