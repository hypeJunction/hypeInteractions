<?php

namespace hypeJunction\Interactions;

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

		if (!elgg_instanceof($comment, 'object', 'comment')) {
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
		if (!$event instanceof Event) {
			return;
		}

		$object = $event->getObject();
		if (!$object instanceof Comment) {
			return;
		}

		$original_container = $object->getOriginalContainer();
		$subscriptions = elgg_get_subscriptions_for_container($original_container->container_guid);

		$all_subscriptions = array_merge($return, $subscriptions);

		// Notification has already been sent to the owner of the container in the save action
		$container = $object->getContainerEntity();
		unset($all_subscriptions[$container->guid]);
		unset($all_subscriptions[$container->owner_guid]);

		return $all_subscriptions;
	}

}
