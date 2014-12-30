<?php

namespace hypeJunction\Inbox;

$params = array(
	'handler' => 'messages',
	'sort_by' => 'priority',
	'class' => 'interactions-menu elgg-menu-hz',
);
$params = array_merge($vars, $params);
echo elgg_view_menu('entity', $params);
