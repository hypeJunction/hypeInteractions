<?php

namespace hypeJunction\Interactions;

use ElggMenuItem;

class Menus {

	/**
	 * Filters river menu
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:river"
	 * @param array  $return Menu items
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function riverMenuSetup($hook, $type, $return, $params) {

		if (!elgg_is_logged_in()) {
			return $return;
		}

		$remove = array('comment');
		foreach ($return as $key => $item) {
			if ($item instanceof ElggMenuItem&& in_array($item->getName(), $remove)) {
				unset($return[$key]);
			}
		}

		return $return;
	}

	/**
	 * Setups entity interactions menu
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:interactions"
	 * @param array  $menu   Menu
	 * @param array  $params Hook parameters
	 * @uses $params['entity'] An entity that we are interacting with
	 * @uses $params['active_tab'] Currently active tab, default to 'comments'
	 * @return array
	 */
	public static function interactionsMenuSetup($hook, $type, $menu, $params) {

		$entity = elgg_extract('entity', $params, false);
		/* @var ElggEntity $entity */

		if (!elgg_instanceof($entity)) {
			return $menu;
		}

		$active_tab = elgg_extract('active_tab', $params);

		// Commenting
		$comments_count = $entity->countComments();
		$can_comment = $entity->canComment();

		if ($can_comment) {
			$menu[] = ElggMenuItem::factory(array(
				'name' => 'comments',
				'text' => ($entity instanceof Comment) ? elgg_echo('interactions:reply:create') : elgg_echo('interactions:comment:create'),
				'href' => "stream/comments/$entity->guid",
				'priority' => 200,
				'data-trait' => 'comments',
				'item_class' => 'interactions-action',
			));
		}

		if ($can_comment || $comments_count) {
			$menu[] = ElggMenuItem::factory(array(
				'name' => 'comments:badge',
				'text' => elgg_view('framework/interactions/elements/badge', array(
					'entity' => $entity,
					'icon' => 'comments',
					'type' => 'comments',
					'count' => $comments_count,
				)),
				'href' => "stream/comments/$entity->guid",
				'selected' => ($active_tab == 'comments'),
				'priority' => 100,
				'data-trait' => 'comments',
				'item_class' => 'interactions-tab',
			));
		}

		if (elgg_is_active_plugin('likes')) {
			// Liking and unliking
			$likes_count = $entity->countAnnotations('likes');
			$can_like = $entity->canAnnotate(0, 'likes');
			$does_like = elgg_annotation_exists($entity->guid, 'likes');

			if ($can_like) {

				$before_text = elgg_echo('interactions:likes:before');
				$after_text = elgg_echo('interactions:likes:after');

				$menu[] = ElggMenuItem::factory(array(
					'name' => 'likes',
					'text' => ($does_like) ? $after_text : $before_text,
					'href' => "action/stream/like?guid=$entity->guid",
					'is_action' => true,
					'priority' => 400,
					'link_class' => 'interactions-state-toggler',
					'item_class' => 'interactions-action',
					// Attrs for JS toggle
					'data-guid' => $entity->guid,
					'data-trait' => 'likes',
					'data-state' => ($does_like) ? 'after' : 'before',
				));
			}

			if ($can_like || $likes_count) {
				$menu[] = ElggMenuItem::factory(array(
					'name' => 'likes:badge',
					'text' => elgg_view('framework/interactions/elements/badge', array(
						'entity' => $entity,
						'icon' => 'likes',
						'type' => 'likes',
						'count' => $likes_count,
					)),
					'href' => "stream/likes/$entity->guid",
					'selected' => ($active_tab == 'likes'),
					'data-trait' => 'likes',
					'priority' => 300,
					'item_class' => 'interactions-tab',
				));
			}
		}

		return $menu;
	}

	/**
	 * Setups comment menu
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:interactions"
	 * @param array  $menu   Menu
	 * @param array  $params Hook parameters
	 * @return array
	 */
	public static function entityMenuSetup($hook, $type, $menu, $params) {

		$entity = elgg_extract('entity', $params);

		if (!$entity instanceof Comment) {
			return;
		}

		if ($entity->canEdit()) {
			$menu[] = ElggMenuItem::factory(array(
				'name' => 'edit',
				'text' => elgg_echo('edit'),
				'href' => "stream/edit/$entity->guid",
				'priority' => 800,
				'data' => [
					'icon' => 'pencil',
				]
			));
		}

		if ($entity->canDelete()) {
			$menu[] = ElggMenuItem::factory(array(
				'name' => 'delete',
				'text' => elgg_echo('delete'),
				'href' => "action/comment/delete?guid=$entity->guid",
				'is_action' => true,
				'priority' => 900,
				'confirm' => true,
				'data' => [
					'icon' => 'delete',
				]
			));
		}

		return $menu;
	}

}
