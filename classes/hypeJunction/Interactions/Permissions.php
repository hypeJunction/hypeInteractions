<?php

namespace hypeJunction\Interactions;

use ElggAnnotation;

class Permissions {

	/**
	 * Disallows commenting on comments once a certain depth has been reached
	 *
	 * @param string $hook       "permissions_check:comment"
	 * @param string $type       "object"
	 * @param bool   $permission Current permission
	 * @param array  $params     Hook params
	 * @param bool
	 */
	public static function canComment($hook, $type, $permission, $params) {

		$entity = elgg_extract('entity', $params);

		if (!$entity instanceof Comment) {
			return $permission;
		}

		if ($entity->getDepthToOriginalContainer() >= (int) elgg_get_plugin_setting('max_comment_depth', 'hypeInteractions')) {
			return false;
		}
	}

	/**
	 * Fixes editing permissions on likes
	 *
	 * @param string $hook       "permissions_check"
	 * @param string $type       "annotation"
	 * @param bool   $permission Current permission
	 * @param array  $params     Hook params
	 * @return boolean
	 */
	public static function canEditAnnotation($hook, $type, $permission, $params) {

		$annotation = elgg_extract('annotation', $params);
		$user = elgg_extract('user', $params);

		if ($annotation instanceof ElggAnnotation && $annotation->name == 'likes') {
			// only owners of original annotation (or users who can edit these owners)
			$ann_owner = $annotation->getOwnerEntity();
			return ($ann_owner && $ann_owner->canEdit($user->guid));
		}

		return $permission;
	}

	/**
	 * Check if attachments are enabled
	 * @return bool
	 */
	public static function canAttachFiles() {
		if (!elgg_is_active_plugin('hypeAttachments')) {
			return false;
		}
		return (bool) elgg_get_plugin_setting('enable_attachments', 'hypeInteractions', true);
	}

}
