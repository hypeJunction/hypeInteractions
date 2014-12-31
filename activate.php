<?php

namespace hypeJunction\Interactions;

require_once __DIR__ . '/vendors/autoload.php';

$plugin_id = basename(__DIR__);

$defaults = array(
	'max_comment_depth' => 1,
	'comment_form_position' => 'after',
	'comments_order' => 'asc',
	'comments_load_style' => 'load_older',
	'comments_limit' => 3,
	'comments_load_limit' => 100,
);

foreach ($defaults as $name => $value) {
	if (is_null(elgg_get_plugin_setting($name, $plugin_id))) {
		elgg_set_plugin_setting($name, $value, $plugin_id);
	}
}

$subtypes = array(
	Comment::SUBTYPE => get_class(new Comment()),
	RiverObject::SUBTYPE => get_class(new RiverObject()),
	// legacy subtypes
	'hjcomment' => get_class(new Comment()),
	'hjstream' => get_class(new RiverObject()),
);

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}