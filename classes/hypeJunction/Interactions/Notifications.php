<?php

namespace hypeJunction\Interactions;

use Elgg\Notifications\NotificationEvent;

class Notifications {

	/**
	 * Prepare a notification for when comment is created
	 *
	 * @param string       $hook         Equals 'prepare'
	 * @param string       $type         Equals ''notification:create:object:comment'
	 * @param Notification $notification Notification object
	 * @param array        $params       Additional params
	 *
	 * @return Notification
	 */
	public static function format($hook, $type, $notification, $params) {

		$event = elgg_extract('event', $params);
		$comment = $event->getObject();
		$recipient = elgg_extract('recipient', $params);
		$language = elgg_extract('language', $params);

		if (!$comment instanceof Comment) {
			return;
		}

		$entity = $comment->getContainerEntity();
		if (!$entity) {
			return;
		}

		$messages = (new NotificationFormatter($comment, $recipient, $language))->prepare();

		$notification->summary = $messages->summary;
		$notification->subject = $messages->subject;
		$notification->body = $messages->body;

		return $notification;
	}

	/**
	 * Subscribe users to comments based on original entity
	 *
	 * @param string $hook   "get"
	 * @param string $type   "subscriptions"
	 * @param array  $return Subscriptions
	 * @param array  $params Hook params
	 *
	 * @return array
	 */
	public static function getSubscriptions($hook, $type, $return, $params) {

		$event = elgg_extract('event', $params);
		if (!$event instanceof NotificationEvent) {
			return;
		}

		$object = $event->getObject();
		if (!$object instanceof Comment) {
			return;
		}

		$subscriptions = [];
		$actor_subscriptions = [];
		$group_subscriptions = [];

		$original_container = $object->getOriginalContainer();

		if ($original_container instanceof \ElggObject) {
			// Users subscribed to the original post in the thread
			$subscriptions = elgg_get_subscriptions_for_container($original_container->guid);
			$group = $original_container->getContainerEntity();
			if ($group instanceof \ElggGroup) {
				// Users subscribed to group notifications the thread was started in
				$group_subscriptions = elgg_get_subscriptions_for_container($group->guid);
			}
			// @todo: Do we need to notify users subscribed to a thread within user container?
			// 		  It doesn't seem that such notifications would make sense, because they are not performed by the user container
		} else if ($original_container instanceof \ElggGroup) {
			$group_subscriptions = elgg_get_subscriptions_for_container($original_container->guid);
		}

		$actor = $event->getActor();
		if ($actor instanceof \ElggUser) {
			$actor_subscriptions = elgg_get_subscriptions_for_container($actor->guid);
		}

		$all_subscriptions = $return + $subscriptions + $group_subscriptions + $actor_subscriptions;

		// Get user GUIDs that have subscribed to this entity via comment tracker
		$user_guids = elgg_get_entities_from_relationship(array(
			'type' => 'user',
			'relationship_guid' => $original_container->guid,
			'relationship' => 'comment_subscribe',
			'inverse_relationship' => true,
			'limit' => false,
			'callback' => function($row) {
				return (int) $row->guid;
			},
		));

		/* @var int[] $user_guids */

		if ($user_guids) {
			// Get a comma separated list of the subscribed users
			$user_guids_set = implode(',', $user_guids);

			$dbprefix = elgg_get_config('dbprefix');
			$site_guid = elgg_get_site_entity()->guid;

			// Get relationships that are used to explicitly block specific notification methods
			$blocked_relationships = get_data("
				SELECT *
				FROM {$dbprefix}entity_relationships
				WHERE relationship LIKE 'block_comment_notify%'
				AND guid_one IN ($user_guids_set)
				AND guid_two = $site_guid
			");

			// Get the methods from the relationship names
			$blocked_methods = array();
			foreach ($blocked_relationships as $row) {
				$method = str_replace('block_comment_notify', '', $row->relationship);
				$blocked_methods[$row->guid_one][] = $method;
			}

			$registered_methods = _elgg_services()->notifications->getMethods();

			foreach ($user_guids as $user_guid) {
				// All available notification methods on the site
				$methods = $registered_methods;

				// Remove the notification methods that user has explicitly blocked
				if (isset($blocked_methods[$user_guid])) {
					$methods = array_diff($methods, $blocked_methods[$user_guid]);
				}

				if ($methods) {
					$all_subscriptions[$user_guid] = $methods;
				}
			}
		}

		// Do not send any notifications, if user has explicitly unsubscribed
		foreach ($all_subscriptions as $guid => $methods) {
			if (check_entity_relationship($guid, 'comment_tracker_unsubscribed', $original_container->guid)) {
				unset($all_subscriptions[$guid]);
			}
		}

		// Notification has already been sent to the owner of the container in the save action
		$container = $object->getContainerEntity();
		unset($all_subscriptions[$container->guid]);
		unset($all_subscriptions[$container->owner_guid]);
		unset($all_subscriptions[$actor->guid]);

		return $all_subscriptions;
	}

	/**
	 * Subscribe users to notifications about the thread
	 *
	 * @param string      $event  "create"
	 * @param string      $type   "object"
	 * @param \ElggEntity $entity Object
	 *
	 * @return void
	 */
	public static function subscribe($event, $type, $entity) {

		if (!$entity instanceof Comment) {
			return;
		}

		$original_container = $entity->getOriginalContainer();
		if (!$original_container instanceof \ElggObject) {
			// Let core subscriptions deal with it
			return;
		}

		if (check_entity_relationship($entity->owner_guid, 'comment_tracker_unsubscribed', $original_container->guid)) {
			// User unsubscribed from notifications about this container
			return;
		}

		add_entity_relationship($entity->owner_guid, 'comment_subscribe', $original_container->guid);
	}
}
