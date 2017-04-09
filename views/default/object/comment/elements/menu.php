<?php

echo elgg_view_menu('entity', $vars + [
	'handler' => 'messages',
	'sort_by' => 'priority',
	'class' => 'interactions-menu elgg-menu-hz',
]);
