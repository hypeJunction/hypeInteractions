<?php

namespace hypeJunction\Interactions;

use ElggAnnotation;
use ElggMenuItem;

/**
 * Filters river menu
 *
 * @param string $hook   "register"
 * @param string $type   "menu:river"
 * @param array  $return Menu items
 * @param array  $params Hook params
 * @return array
 */
function river_menu_setup($hook, $type, $return, $params) {

	if (!elgg_is_logged_in()) {
		return $return;
	}

	$remove = array('comment');
	foreach ($return as $key => $item) {
		if ($item instanceof \ElggMenuItem && in_array($item->getName(), $remove)) {
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
function interactions_menu_setup($hook, $type, $menu, $params) {

	$entity = elgg_extract('entity', $params, false);
	/* @var ElggEntity $entity */

	if (!elgg_instanceof($entity)) {
		return $menu;
	}

	$handler = HYPEINTERACTIONS_HANDLER;
	$active_tab = elgg_extract('active_tab', $params);

	// Commenting
	$comments_count = $entity->countComments();
	$can_comment = $entity->canComment();

	if ($can_comment) {
		$menu[] = ElggMenuItem::factory(array(
					'name' => 'comments',
					'text' => ($entity instanceof Comment) ? elgg_echo('interactions:reply:create') : elgg_echo('interactions:comment:create'),
					'href' => "$handler/comments/$entity->guid",
					'priority' => 100,
					'data-trait' => 'comments',
					'section' => 'actions',
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
					'href' => "$handler/comments/$entity->guid",
					'selected' => ($active_tab == 'comments'),
					'priority' => 500,
					'data-trait' => 'comments',
					'section' => 'tabs',
		));
	}

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
					'href' => "action/$handler/like?guid=$entity->guid",
					'is_action' => true,
					'priority' => 200,
					'section' => 'actions',
					'link_class' => 'interactions-state-toggler',
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
					'href' => "$handler/likes/$entity->guid",
					'selected' => ($active_tab == 'likes'),
					'data-trait' => 'likes',
					'priority' => 600,
					'section' => 'tabs',
		));
	}

	if ($entity instanceof Comment && elgg_in_context('comments')) {

		if ($entity->canEdit()) {
			$menu[] = ElggMenuItem::factory(array(
						'name' => 'edit',
						'text' => elgg_echo('edit'),
						'href' => "$handler/edit/$entity->guid",
						'priority' => 800,
						'section' => 'actions',
			));

			$menu[] = ElggMenuItem::factory(array(
						'name' => 'delete',
						'text' => elgg_echo('delete'),
						'href' => "action/comment/delete?guid=$entity->guid",
						'is_action' => true,
						'priority' => 900,
						'section' => 'actions',
						'link_class' => 'elgg-requires-confirmation',
			));
		}
	}

	return $menu;
}

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
function comments_view_hook($hook, $entity_type, $return, $params) {
	return elgg_view('page/components/interactions', $params);
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
function url_handler($hook, $type, $url, $params) {

	$entity = elgg_extract('entity', $params);
	/* @var ElggEntity $entity */

	if ($entity instanceof Comment) {
		return elgg_normalize_url(implode('/', array(
					HYPEINTERACTIONS_HANDLER,
					'comments',
					$entity->container_guid,
					$entity->guid,
				))) . "#elgg-object-$entity->guid";
	} else if ($entity instanceof RiverObject) {
		return elgg_normalize_url(implode('/', array(
					'activity',
					'view',
					$entity->river_id,
				))) . "#item-river-$entity->guid";
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
function icon_url_handler($hook, $type, $url, $params) {

	$entity = elgg_extract('entity', $params);
	/* @var ElggEntity $entity */

	$size = elgg_extract('size', $params);

	if ($entity instanceof Comment) {
		return $entity->getOwnerEntity()->getIconURL($size);
	}

	return $url;
}

/**
 * Disallows commenting on comments once a certain depth has been reached
 * 
 * @param string $hook       "permissions_check:comment"
 * @param string $type       "object"
 * @param bool   $permission Current permission
 * @param array  $params     Hook params
 * @param bool
 */
function can_comment($hook, $type, $permission, $params) {

	$entity = elgg_extract('entity', $params);

	if (!$entity instanceof Comment) {
		return $permission;
	}

	return ($entity->getDepthToOriginalContainer() < HYPEINTERACTIONS_MAX_COMMENT_DEPTH);
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
function can_edit_annotation($hook, $type, $permission, $params) {

	$annotation = elgg_extract('annotation', $params);
	$user = elgg_extract('user', $params);

	if ($annotation instanceof ElggAnnotation && $annotation->name == 'likes') {
		// only owners of original annotation (or users who can edit these owners)
		$ann_owner = $annotation->getOwnerEntity();
		return ($ann_owner && $ann_owner->canEdit($user->guid));
	}

	return $permission;
}