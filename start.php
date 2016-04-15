<?php

/**
 * hypeInteractions
 * Feature-rich social interactions for Elgg
 *
 * @package Elgg
 * @subpackage hypeJunction\Interactions
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2011-2014, Ismayil Khayredinov
 */

namespace hypeJunction\Interactions;

require_once __DIR__ . '/autoloader.php';

require_once __DIR__ . "/lib/settings.php";
require_once __DIR__ . "/lib/functions.php";
require_once __DIR__ . "/lib/events.php";
require_once __DIR__ . "/lib/hooks.php";
require_once __DIR__ . "/lib/page_handlers.php";

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

/**
 * Initialize the plugin
 * @return void
 */
function init() {

	$handler = HYPEINTERACTIONS_HANDLER;

	/**
	 * PAGE HANDLERS
	 */
	elgg_register_page_handler($handler, __NAMESPACE__ . '\\page_handler');

	/**
	 * ACTIONS
	 */
	$actions_path = __DIR__ . '/actions/';

	elgg_register_action('comment/save', $actions_path . 'comment/save.php');
	elgg_register_action('comment/delete', $actions_path . 'comment/delete.php');

	elgg_register_action("$handler/like", $actions_path . 'interactions/like.php');
	elgg_register_action('likes/add', $actions_path . 'likes/add.php');
	elgg_register_action('likes/delete', $actions_path . 'likes/delete.php');

	/**
	 * HOOKS
	 */
	// COMPONENTS
	elgg_register_plugin_hook_handler('comments', 'all', __NAMESPACE__ . '\\comments_view_hook');

	// URLs
	elgg_register_plugin_hook_handler('entity:url', 'object', __NAMESPACE__ . '\\url_handler');
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', __NAMESPACE__ . '\\icon_url_handler');

	// PERMISSIONS
	elgg_register_plugin_hook_handler('permissions_check:comment', 'object', __NAMESPACE__ . '\\can_comment');
	elgg_register_plugin_hook_handler('permissions_check', 'annotation', __NAMESPACE__ . '\\can_edit_annotation');

	// MENUS
	elgg_register_plugin_hook_handler('register', 'menu:interactions', __NAMESPACE__ . '\\interactions_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:river', __NAMESPACE__ . '\\river_menu_setup');

	elgg_unregister_plugin_hook_handler('register', 'menu:entity', 'likes_entity_menu_setup');
	elgg_unregister_plugin_hook_handler('register', 'menu:river', 'likes_river_menu_setup');

	// NOTIFICATIONS
	elgg_register_notification_event('object', 'comment', array('create'));
	elgg_register_plugin_hook_handler('prepare', 'notification:create:object:comment', __NAMESPACE__ . '\\format_notification');
	elgg_register_plugin_hook_handler('get', 'subscriptions', __NAMESPACE__ . '\\get_subscriptions');

	/**
	 * EVENTS
	 */
	elgg_register_event_handler('created', 'river', __NAMESPACE__ . '\\created_river_event');
	elgg_register_event_handler('upgrade', 'system', __NAMESPACE__ . '\\upgrade');

	/**
	 * CSS & JS
	 */
	elgg_extend_view('css/elgg', 'css/framework/interactions/stylesheet.css');

	elgg_extend_view('object/blog', 'object/blog/interactions');

	// remove default likes behaviour
	elgg_unextend_view('css/elgg', 'likes/css');
	elgg_unextend_view('js/elgg', 'likes/js');
}

/**
 * Run upgrade scripts
 * @return void
 */
function upgrade() {
	if (elgg_is_admin_logged_in()) {
		include_once __DIR__ . '/lib/upgrades.php';
	}
}
