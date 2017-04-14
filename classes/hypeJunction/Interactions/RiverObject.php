<?php

namespace hypeJunction\Interactions;

use ElggObject;
use ElggRiverItem;

class RiverObject extends ElggObject {

	const TYPE = 'object';
	const SUBTYPE = 'river_object';

	private $_river_item;
	
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

		if (isset($this->_river_item)) {
			return $this->_river_item;
		}

		$id = $this->river_id;

		$items = elgg_get_river(array(
			'ids' => $id,
			'limit' => 1,
		));

		$this->_river_item = (is_array($items) && count($items)) ? $items[0] : false;
		return $this->_river_item;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDisplayName() {
		$item = $this->getRiverItem();
		if (!$item) {
			return elgg_echo('interactions:river_object:title');
		}

		$subject = $item->getSubjectEntity();
		return elgg_echo('interactions:river_object:title_subject', [$subject->getDisplayName()]);
	}

}
