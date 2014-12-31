<?php

namespace hypeJunction\Interactions;

$handler = elgg_get_plugin_setting('handler', 'hypeInteractions');
if (!$handler) {
	$handler = 'stream';
}
define('HYPEINTERACTIONS_HANDLER', $handler);

define('HYPEINTERACTIONS_MAX_COMMENT_DEPTH', (int) elgg_get_plugin_setting('max_comment_depth', 'hypeInteractions'));
define('HYPEINTERACTIONS_COMMENT_FORM_POSITION', elgg_get_plugin_setting('comment_form_position', 'hypeInteractions'));
define('HYPEINTERACTIONS_COMMENTS_ORDER', elgg_get_plugin_setting('comments_order', 'hypeInteractions'));
define('HYPEINTERACTIONS_COMMENTS_LOAD_STYLE', elgg_get_plugin_setting('comments_load_style', 'hypeInteractions'));

$limit = elgg_get_plugin_setting('comments_limit', 'hypeInteractions');
if (!$limit || $limit > 100) {
	$limit = 3;
}
define('HYPEINTERACTIONS_COMMENTS_LIMIT', $limit);

$limit = elgg_get_plugin_setting('comments_load_limit', 'hypeInteractions');
if (!$limit || $limit > 100) {
	$limit = 100;
}
define('HYPEINTERACTIONS_COMMENTS_LOAD_LIMIT', $limit);


