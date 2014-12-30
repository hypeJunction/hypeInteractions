<?php

namespace hypeJunction\Interactions;

use ElggObject;
use ElggRiverItem;

class RiverObject extends ElggObject {

	const TYPE = 'object';
	const SUBTYPE = 'river_object';

	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
	 * Get river item
	 * @return ElggRiverItem|false
	 */
	public function getRiverItem() {
		$id = $this->river_id;

		$items = elgg_get_river(array(
			'ids' => $id,
			'limit' => 1,
		));

		return (is_array($items) && count($items)) ? $items[0] : false;
	}

}
