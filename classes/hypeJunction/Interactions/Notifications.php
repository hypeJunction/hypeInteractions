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

		// Notification has already been sent to the owner of the container in the save action
		$container = $object->getContainerEntity();
		unset($all_subscriptions[$container->guid]);
		unset($all_subscriptions[$container->owner_guid]);

		var_dump($all_subscriptions);

		return $all_subscriptions;
	}

}
