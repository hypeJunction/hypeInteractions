<?php

namespace hypeJunction\Interactions;

use Elgg\Cache\Pool;
use ElggEntity;
use ElggUser;
use stdClass;

/**
 * @access private
 */
class InteractionsCache {

	/**
	 * @var self
	 */
	static $_instance;

	/**
	 * @var Pool
	 */
	private $id_cache;

	/**
	 * Constructor
	 *
	 * @param Pool $routes_cache Cache
	 */
	public function __construct(Pool $routes_cache) {
		$this->id_cache = $routes_cache;
	}

	/**
	 * Returns a singleton
	 * @return self
	 */
	public static function getInstance() {
		if (is_null(self::$_instance)) {
			$id_cache = is_memcache_available() ? new Memcache() : new FileCache();
			self::$_instance = new self($id_cache);
		}
		return self::$_instance;
	}

	/**
	 * Return value of the entity guid that corresponds to river_id
	 *
	 * @param int $river_id River item ID
	 * @return int|false
	 */
	public function getGuidFromRiverId($river_id = 0) {

		$river_id = (int) $river_id;

		$guid = $this->id_cache->get($river_id);
		if ($guid) {
			return $guid;
		}

		$objects = elgg_get_entities_from_metadata([
			'types' => RiverObject::TYPE,
			'subtypes' => [RiverObject::SUBTYPE, 'hjstream'],
			'metadata_name_value_pairs' => [
				'name' => 'river_id',
				'value' => $river_id,
			],
			'limit' => 1,
			'callback' => false,
		]);

		$guid = ($objects) ? $objects[0]->guid : false;

		if ($guid) {
			$this->id_cache->put($river_id, $guid);
			return $guid;
		}

		return false;
	}
}
