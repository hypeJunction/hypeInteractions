<?php

if (!elgg_is_active_plugin('hypeAttachments')) {
	return;
}

$full = elgg_extract('full_view', $vars, true);
if (!$full) {
	return;
}

echo elgg_view('output/attachments', $vars);