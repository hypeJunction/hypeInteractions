<?php

namespace hypeJunction\Interactions;

use Elgg\Cache\Pool;
use Flintstone\Flintstone;

class FileCache implements Pool {

	/**
	 * @var Flintstone
	 */
	private $cache;

	public function __construct() {
		$this->cache = new Flintstone('river_id_cache', [
			'dir' => elgg_get_config('dataroot'),
		]);
	}

	public function get($key, callable $callback = null, $default = null) {
		$value = $this->cache->get($key);
		if (!isset($value)) {
			$value = $default;
		}
		if (is_callable($callback)) {
			return call_user_func($callback, $value);
		}
		return $value;
	}

	public function invalidate($key) {
		$this->cache->delete($key);
	}

	public function put($key, $value) {
		$this->cache->set($key, $value);
	}

}
