<?php

/**
 * hypeInteractions
 * Feature-rich social interactions for Elgg
 *
 * @package Elgg
 * @subpackage hypeJunction\Interactions
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2011-2016, Ismayil Khayredinov
 */
use hypeJunction\Interactions\InteractionsService;
use hypeJunction\Interactions\Menus;
use hypeJunction\Interactions\Notifications;
use hypeJunction\Interactions\Permissions;
use hypeJunction\Interactions\Router;

require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', function() {

	elgg_extend_view('elgg.css', 'css/framework/interactions/stylesheet.css');

	// URL and page handling
	elgg_register_page_handler('stream', [Router::class, 'handleStream']);
	elgg_register_plugin_hook_handler('entity:url', 'object', [Router::class, 'urlHandler']);
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', [Router::class, 'iconUrlHandler']);

	// Register/replace actions
	elgg_register_action('comment/save', __DIR__ . '/actions/comment/save.php');
	elgg_register_action('comment/delete', __DIR__ . '/actions/comment/delete.php');
	elgg_register_action("stream/like", __DIR__ . '/actions/interactions/like.php');
	elgg_register_action('likes/add', __DIR__ . '/actions/likes/add.php');
	elgg_register_action('likes/delete', __DIR__ . '/actions/likes/delete.php');

	// Replace comments block
	elgg_register_plugin_hook_handler('comments', 'all', [InteractionsService::class, 'replaceCommentsBlock']);

	// Create an actionable river object
	elgg_register_event_handler('created', 'river', [InteractionsService::class, 'createRiverObject']);
	elgg_register_event_handler('delete:after', 'river', [InteractionsService::class, 'deleteRiverObject']);

	// Configure permissions
	elgg_register_plugin_hook_handler('permissions_check:comment', 'object', [Permissions::class, 'canComment']);
	elgg_register_plugin_hook_handler('permissions_check', 'annotation', [Permissions::class, 'canEditAnnotation']);

	// Setup menus
	elgg_register_plugin_hook_handler('register', 'menu:entity', [Menus::class, 'entityMenuSetup']);
	elgg_register_plugin_hook_handler('register', 'menu:interactions', [Menus::class, 'interactionsMenuSetup']);
	elgg_register_plugin_hook_handler('register', 'menu:river', [Menus::class, 'riverMenuSetup']);

	// Prepare notifications
	elgg_register_notification_event('object', 'comment', array('create'));
	elgg_register_plugin_hook_handler('prepare', 'notification:create:object:comment', [Notifications::class, 'format']);
	elgg_register_plugin_hook_handler('get', 'subscriptions', [Notifications::class, 'getSubscriptions']);

	// Custom logic for blogs
	elgg_extend_view('object/blog', 'object/blog/interactions');

	// Clean up
	elgg_unregister_plugin_hook_handler('register', 'menu:entity', 'likes_entity_menu_setup');
	elgg_unregister_plugin_hook_handler('register', 'menu:river', 'likes_river_menu_setup');
	elgg_unextend_view('elgg.css', 'likes/css');
	elgg_unextend_view('elgg.js', 'likes/js');

	// Actionable river items
	elgg_register_plugin_hook_handler('likes:is_likable', 'object:river_object', [Elgg\Values::class, 'getTrue']);
});
