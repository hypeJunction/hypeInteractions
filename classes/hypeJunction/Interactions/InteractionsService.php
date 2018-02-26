<?php

namespace hypeJunction\Interactions;

use ElggBatch;
use ElggEntity;
use ElggGroup;
use ElggObject;
use ElggRiverItem;
use ElggUser;

/**
 * @access private
 */
class InteractionsService {

	/**
	 * Replace the default comments block with an interactions component
	 * @see elgg_view_comments()
	 *
	 * @param string $hook        "comments"
	 * @param string $entity_type "all"
	 * @param string $return      View
	 * @param type   $params      Additional parameters
	 * @return string
	 */
	public static function replaceCommentsBlock($hook, $entity_type, $return, $params) {
		return elgg_view('page/components/interactions', $params);
	}

	/**
	 * Creates a commentable object associated with river items whose object is not ElggObject
	 *
	 * @param string        $event "created"
	 * @param string        $type  "river"
	 * @param ElggRiverItem $river River item
	 * @return true
	 */
	public static function createRiverObject($event, $type, $river) {
		InteractionsService::createActionableRiverObject($river);
	}

	/**
	 * Deletes a commentable object associated with river items whose object is not ElggObject
	 *
	 * @param string        $event "delete:after"
	 * @param string        $type  "river"
	 * @param ElggRiverItem $river River item
	 * @return true
	 */
	public static function deleteRiverObject($event, $type, $river) {
		$ia = elgg_set_ignore_access(true);

		$objects = elgg_get_entities_from_metadata(array(
			'types' => RiverObject::TYPE,
			'subtypes' => array(RiverObject::SUBTYPE, 'hjstream'),
			'metadata_name_value_pairs' => array(
				'name' => 'river_id',
				'value' => $river->id,
			),
			'limit' => 0,
			'batch' => true,
		));

		$objects->setIncrementOffset(false);

		foreach ($objects as $object) {
			$object->delete();
		}

		elgg_set_ignore_access($ia);
	}

	/**
	 * Creates an object associated with a river item for commenting and other purposes
	 * This is a workaround for river items that do not have an object or have an object that is group or user
	 *
	 * @param ElggRiverItem $river River item
	 * @return RiverObject|false
	 */
	public static function createActionableRiverObject(ElggRiverItem $river) {

		if (!$river instanceof ElggRiverItem) {
			return false;
		}

		$object = $river->getObjectEntity();

		$views = self::getActionableViews();
		if (!in_array($river->view, $views)) {
			return $object;
		}

		$access_id = $object->access_id;
		if ($object instanceof ElggUser) {
			$access_id = ACCESS_FRIENDS;
		} else if ($object instanceof ElggGroup) {
			$access_id = $object->group_acl;
		}

		$ia = elgg_set_ignore_access(true);

		$object = new RiverObject();
		$object->owner_guid = $river->subject_guid;
		$object->container_guid = $object->guid;
		$object->access_id = $access_id;
		$object->river_id = $river->id;
		$object->save();

		elgg_set_ignore_access($ia);
		
		return $object;
	}

	/**
	 * Get an actionable object associated with the river item
	 * This could be a river object entity or a special entity that was created for this river item
	 *
	 * @param ElggRiverItem $river River item
	 * @return ElggObject|false
	 */
	public static function getRiverObject(ElggRiverItem $river) {

		if (!$river instanceof ElggRiverItem) {
			return false;
		}

		$object = $river->getObjectEntity();

		$views = self::getActionableViews();

		if (in_array($river->view, $views)) {

			// wrapping this in ignore access so that we do not accidentally create duplicate
			// river objects
			$ia = elgg_set_ignore_access(true);
			$objects = elgg_get_entities_from_metadata([
				'types' => RiverObject::TYPE,
				'subtypes' => [RiverObject::SUBTYPE, 'hjstream'],
				'metadata_name_value_pairs' => [
					'name' => 'river_id',
					'value' => $river->id,
				],
				'limit' => 1,
			]);

			$guid = ($objects) ? $objects[0]->guid : false;

			if (!$guid) {
				$object = InteractionsService::createActionableRiverObject($river);
				$guid = $object->guid;
			}

			elgg_set_ignore_access($ia);

			$object = get_entity($guid);
		}

		if ($object instanceof ElggEntity) {
			$object->setVolatileData('river_item', $river);
		}

		return $object;
	}

	/**
	 * Get interaction statistics
	 *
	 * @param ElggEntity $entity Entity
	 * @return array
	 */
	public static function getStats($entity) {

		if (!$entity instanceof ElggEntity) {
			return array();
		}

		$stats = array(
			'comments' => array(
				'count' => $entity->countComments()
			),
			'likes' => array(
				'count' => $entity->countAnnotations('likes'),
				'state' => (elgg_annotation_exists($entity->guid, 'likes')) ? 'after' : 'before',
			)
		);

		return elgg_trigger_plugin_hook('get_stats', 'interactions', array('entity' => $entity), $stats);
	}

	/**
	 * Get entity URL wrapped in an <a></a> tag
	 * @return string
	 */
	public static function getLinkedEntityName($entity) {
		if (elgg_instanceof($entity)) {
			return elgg_view('output/url', array(
				'text' => $entity->getDisplayName(),
				'href' => $entity->getURL(),
				'is_trusted' => true,
			));
		}
		return '';
	}

	/**
	 * Get configured comments order
	 * @return string
	 */
	public static function getCommentsSort() {
		$user_setting = elgg_get_plugin_user_setting('comments_order', 0, 'hypeInteractions');
		$setting = $user_setting ?: elgg_get_plugin_setting('comments_order', 'hypeInteractions');
		if ($setting == 'asc') {
			$setting = 'time_created::asc';
		} else if ($setting == 'desc') {
			$setting = 'time_created::desc';
		}
		return $setting;
	}

	/**
	 * Get configured loading style
	 * @return string
	 */
	public static function getLoadStyle() {
		$user_setting = elgg_get_plugin_user_setting('comments_load_style', 0, 'hypeInteractions');
		return $user_setting ?: elgg_get_plugin_setting('comments_load_style', 'hypeInteractions');
	}

	/**
	 * Get comment form position
	 * @return string
	 */
	public static function getCommentsFormPosition() {
		$user_setting = elgg_get_plugin_user_setting('comment_form_position', 0, 'hypeInteractions');
		return $user_setting ?: elgg_get_plugin_setting('comment_form_position', 'hypeInteractions');
	}

	/**
	 * Get number of comments to show
	 *
	 * @param string $partial Partial or full view
	 * @return string
	 */
	public static function getLimit($partial = true) {
		if ($partial) {
			$limit = elgg_get_plugin_setting('comments_limit', 'hypeInteractions');
			return $limit ?: 3;
		} else {
			$limit = elgg_get_plugin_setting('comments_load_limit', 'hypeInteractions');
			return min(max((int) $limit, 20), 200);
		}
	}

	/**
	 * Calculate offset till the page that contains the comment
	 *
	 * @param int     $count   Number of comments in the list
	 * @param int     $limit   Number of comments to display
	 * @param Comment $comment Comment entity
	 * @return int
	 */
	public static function calculateOffset($count, $limit, $comment = null) {

		$order = self::getCommentsSort();
		$style = self::getLoadStyle();

		if ($comment instanceof Comment) {
			$thread = new Thread($comment);
			$offset = $thread->getOffset($limit, $order);
		} else if (($order == 'time_created::asc' && $style == 'load_older') || ($order == 'time_created::desc' && $style == 'load_newer')) {
			// show last page
			$offset = $count - $limit;
			if ($offset < 0) {
				$offset = 0;
			}
		} else {
			// show first page
			$offset = 0;
		}

		return (int) $offset;
	}

	/**
	 * Get views, which custom threads should be created for
	 * @return array
	 */
	public static function getActionableViews() {
		static $views;
		if (isset($views)) {
			return $views;
		}

		$views = [];

		$plugin = elgg_get_plugin_from_id('hypeInteractions');
		$settings = $plugin->getAllSettings();
		foreach ($settings as $key => $value) {
			if (!$value) {
				continue;
			}
			list ($prefix, $view) = explode(':', $key);
			if ($prefix !== 'stream_object') {
				continue;
			}
			$views[] = $view;
		}

		return $views;
	}

	/**
	 * Update river object access to match that of the container
	 *
	 * @param string     $event  'update:after'
	 * @param string     $type   'all'
	 * @param ElggEntity $entity The updated entity
	 * @return bool
	 */
	public static function syncRiverObjectAccess($event, $type, $entity) {
		if (!$entity instanceof \ElggObject) {
			// keep user and group entries as is
			return;
		}

		// need to override access in case comments ended up with ACCESS_PRIVATE
		// and to ensure write permissions
		$ia = elgg_set_ignore_access(true);
		$options = array(
			'type' => 'object',
			'subtype' => RiverObject::class,
			'container_guid' => $entity->guid,
			'wheres' => array(
				"e.access_id != {$entity->access_id}"
			),
			'limit' => 0,
		);

		$batch = new ElggBatch('elgg_get_entities', $options, null, 25, false);
		foreach ($batch as $river_object) {
			// Update comment access_id
			$river_object->access_id = $entity->access_id;
			$river_object->save();
		}

		elgg_set_ignore_access($ia);
	}

}
