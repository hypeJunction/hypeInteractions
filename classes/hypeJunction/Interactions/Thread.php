<?php

namespace hypeJunction\Interactions;

use ElggBatch;
use InvalidArgumentException;

class Thread {

	protected $comment;

	const LIMIT = 10;

	/**
	 * Construct a magic comments thread
	 * @param Comment $comment Comment entity
	 */
	public function __construct(Comment $comment) {
		if (!$comment instanceof Comment) {
			throw new InvalidArgumentException(get_class() . ' expects an instance of ' . get_class(new Comment()));
		}
		$this->comment = $comment;
	}

	/**
	 * Get options for {@link elgg_get_entities()}
	 *
	 * @param array $options Default options array
	 * @return array
	 */
	public function getFilterOptions(array $options = array()) {
		$options['types'] = $this->comment->getType();
		$options['subtypes'] = array($this->comment->getSubtype(), 'hjcomment');
		$options['container_guids'] = $this->comment->container_guid;
		if (!isset($options['order_by'])) {
			$options['order_by'] = 'e.guid ASC';
		}
		return $options;
	}

	/**
	 * Calculate a page offset to the given comment
	 *
	 * @param int $limit Items per page
	 * @return int
	 */
	public function getOffset($limit = self::LIMIT) {
		if ($limit === 0) {
			return 0;
		}
		$before = $this->getCommentsBefore(array('count' => true, 'offset' => 0));
		return floor($before / $limit) * $limit;
	}

	/**
	 * Get comments in a thread
	 *
	 * @param array $options Default options array
	 * @return Comment[]|false
	 */
	public function getComments(array $options = array()) {
		return elgg_get_entities_from_metadata($this->getFilterOptions($options));
	}

	/**
	 * Get count of comments in a thread
	 *
	 * @param array $options Default options array
	 * @return int
	 */
	public function getCount(array $options = array()) {
		$options['count'] = true;
		return $this->getComments($options);
	}

	/**
	 * Delete all comments in a thread
	 *
	 * @param bool $recursive Delete recursively
	 * @return bool
	 */
	public function delete($recursive = true) {
		$success = 0;
		$count = $this->getCount();
		$comments = $this->getAll()->setIncrementOffset(false);
		foreach ($comments as $comment) {
			if ($comment->delete($recursive)) {
				$success++;
			}
		}
		return ($success == $count);
	}

	/**
	 * Get all comments as batch
	 *
	 * @param string $getter  Callable getter
	 * @param array  $options Getter options
	 * @return ElggBatch
	 */
	public function getAll($getter = 'elgg_get_entities_from_metadata', $options = array()) {
		$options['limit'] = 0;
		$options = $this->getFilterOptions($options);
		return new ElggBatch($getter, $options);
	}

	/**
	 * Get preceding comments
	 *
	 * @param array $options Additional options
	 * @return mixed
	 */
	public function getCommentsBefore(array $options = array()) {
		$options['wheres'][] = "e.guid < {$this->comment->guid}";
		$options['order_by'] = 'e.guid DESC';
		$comments = elgg_get_entities_from_metadata($this->getFilterOptions($options));
		if (is_array($comments)) {
			return array_reverse($comments);
		}
		return $comments;
	}

	/**
	 * Get succeeding comments
	 *
	 * @param array $options Additional options
	 * @return mixed
	 */
	public function getCommentsAfter(array $options = array()) {
		$options['wheres'][] = "e.guid > {$this->comment->guid}";
		return elgg_get_entities_from_metadata($this->getFilterOptions($options));
	}

	/**
	 * Returns an array of getter options for retrieving attachments in the thread
	 *
	 * @param array $options Additional options
	 * @return array
	 */
	public function getAttachmentsFilterOptions(array $options = array()) {

		$dbprefix = elgg_get_config('dbprefix');

		$options['joins'][] = "JOIN {$dbprefix}entity_relationships er ON er.guid_two = e.guid";
		$options['joins'][] = "JOIN {$dbprefix}entities e2 ON er.guid_one = e2.guid";
		$options['wheres'][] = "er.relationship = 'attached'";
		$options['wheres'][] = "e2.container_guid = e.container_guid";

		return $options;
	}

	/**
	 * Returns an array of attachments in the thread
	 *
	 * @param array $options Additional options
	 * @return ElggEntity[]|false
	 */
	public function getAttachments(array $options = array()) {
		$options = $this->getAttachmentsFilterOptions($options);
		return elgg_get_entities($options);
	}

	/**
	 * Returns a count of attachments in the thread
	 *
	 * @param array $options Additional options
	 * @return int
	 */
	public function hasAttachments(array $options = array()) {
		$options['count'] = true;
		return $this->getAttachments($options);
	}

}
