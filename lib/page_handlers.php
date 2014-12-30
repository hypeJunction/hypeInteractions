<?php

namespace hypeJunction\Interactions;

/**
 * Handles interactions pages
 *
 * <handler>/comments/<entity_guid>/[<comment_guid>]
 * <handler>/likes/<entity_guid>
 * <handler>/edit/<entity_guid>
 *
 * @param array $page URL segments
 * @return boolean
 */
function page_handler($page, $handler) {

	switch ($page[0]) {

		case 'comments' :
			set_input('entity_guid', $page[1]);
			set_input('comment_guid', elgg_extract(2, $page, true));
			$page = elgg_view('resources/interactions/comments');
			break;

		case 'likes' :
			set_input('guid', $page[1]);
			$page = elgg_view('resources/interactions/likes');
			break;

		case 'edit' :
			set_input('guid', $page[1]);
			$page = elgg_view('resources/interactions/edit');
			break;
	}

	if (!$page) {
		return false;
	}

	echo $page;
	return true;
}