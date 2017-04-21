<?php

namespace hypeJunction\Interactions;

class Router {

	/**
	 * Handles interactions pages
	 * Provides a uniform endpoint to retrieve comments/likes
	 *
	 * stream/comments/<entity_guid>/[<comment_guid>]
	 * stream/likes/<entity_guid>
	 * stream/edit/<entity_guid>
	 *
	 * @param array $segments URL segments
	 * @return boolean
	 */
	public static function handleStream($segments) {

		$page = array_shift($segments);
		$guid = array_shift($segments);

		switch ($page) {

			case 'comments' :

				$comment_guid = array_shift($segments);
				set_input('entity_guid', $guid);
				set_input('comment_guid', $comment_guid);
				echo elgg_view_resource('interactions/comments', [
					'guid' => $guid,
					'comment_guid' => $comment_guid
				]);
				return true;

			case 'likes' :
				set_input('guid', $guid);
				echo elgg_view_resource('interactions/likes', [
					'guid' => $guid,
				]);
				return true;

			case 'edit' :
				set_input('guid', $guid);
				echo elgg_view_resource('interactions/edit', [
					'guid' => $guid,
				]);
				return true;

			case 'view' :
				set_input('guid', $guid);
				echo elgg_view_resource('interactions/view', [
					'guid' => $guid,
				]);
				return true;
		}

		return false;
	}

	/**
	 * Handles entity URLs
	 *
	 * @param string $hook   "entity:url"
	 * @param string $type   "object"
	 * @param string $url    Current URL
	 * @param array  $params Hook params
	 * @return string Filtered URL
	 */
	public static function urlHandler($hook, $type, $url, $params) {

		$entity = elgg_extract('entity', $params);
		/* @var ElggEntity $entity */

		if ($entity instanceof Comment) {
			$container = $entity->getContainerEntity();
			if ($container instanceof Comment) {
				return $container->getURL();
			}
			return elgg_normalize_url(implode('/', array(
						'stream',
						'comments',
						$entity->container_guid,
						$entity->guid,
					))) . "#elgg-object-$entity->guid";
		} else if ($entity instanceof RiverObject) {
			return elgg_normalize_url(implode('/', array(
				'stream',
				'view',
				$entity->guid
			)));
		}

		return $url;
	}

	/**
	 * Replaces comment icons
	 *
	 * @param string $hook   "entity:icon:url"
	 * @param string $type   "object"
	 * @param string $url    Current URL
	 * @param array  $params Hook params
	 * @return string
	 */
	public static function iconUrlHandler($hook, $type, $url, $params) {

		$entity = elgg_extract('entity', $params);
		/* @var ElggEntity $entity */

		if ($entity instanceof Comment) {
			$owner = $entity->getOwnerEntity();
			if (!$owner) {
				return;
			}
			return $owner->getIconURL($params);
		}

		return $url;
	}

}
