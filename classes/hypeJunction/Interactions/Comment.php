<?php

namespace hypeJunction\Interactions;

use ElggComment;
use ElggEntity;
use ElggObject;
use ElggUser;

class Comment extends ElggComment {

	const TYPE = 'object';
	const SUBTYPE = 'comment';

	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function canComment($user_guid = 0, $default = null) {
		return ElggObject::canComment($user_guid, $default);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDisplayName() {
		$name = $this->title;
		if (!$name) {
			$owner = $this->getOwnerEntity();
			$name = elgg_echo('interactions:comment:subject', array($owner->name));
		}
		return $name;
	}

	/**
	 * Returns getter options for comment attachments
	 *
	 * @param array $options Additional options
	 * @return array
	 */
	public function getAttachmentsFilterOptions(array $options = array()) {
		$defaults = array(
			'relationship' => 'attached',
			'relationship_guid' => $this->guid,
			'inverse_relationship' => false,
		);
		return array_merge($defaults, $options);
	}

	/**
	 * Returns an array of attached entities
	 *
	 * @param array $options  Additional options
	 * @return ElggEntity[]|false
	 */
	public function getAttachments(array $options = array()) {
		$options = $this->getAttachmentsFilterOptions($options);
		return elgg_get_entities_from_relationship($options);
	}

	/**
	 * Check if comment has attachments
	 * Returns a count of attachments
	 *
	 * @param array $options Additional options
	 * @return int
	 */
	public function hasAttachments(array $options = array()) {
		$options['count'] = true;
		return $this->getAttachments($options);
	}

	/**
	 * Get entity that the original comment was made on in a comment thread
	 * @return ElggEntity
	 */
	public function getOriginalContainer() {
		$container = $this;
		while ($container instanceof Comment) {
			$container = $container->getContainerEntity();
		}
		return ($container instanceof Comment) ? $this->getOwnerEntity() : $container;
	}

	/**
	 * Get owner of the original content
	 * @return ElggEntity
	 */
	public function getOriginalOwner() {
		$container = $this->getOriginalContainer();
		return ($container instanceof ElggUser) ? $container : $container->getOwnerEntity();
	}

	/**
	 * Get nesting level of this comment
	 * @return int
	 */
	public function getDepthToOriginalContainer() {
		$depth = 0;
		$ancestry = $this->getAncestry();
		foreach ($ancestry as $a) {
			$ancestor = get_entity($a);
			if ($ancestor instanceof self) {
				$depth++;
			}
		}
		return $depth;
	}

	/**
	 * Get ancestry
	 * @return int[]
	 */
	public function getAncestry() {
		$ancestry = array();
		$container = $this;
		while ($container instanceof ElggEntity) {
			array_unshift($ancestry, $container->guid);
			$container = $container->getContainerEntity();
		}
		return $ancestry;
	}

	/**
	 * Returns getter options for comment subscribers
	 *
	 * @param array $options Additional options
	 * @return array
	 */
	public function getSubscriberFilterOptions(array $options = array()) {
		$defaults = array(
			'type' => 'user',
			'relationship' => 'subscribed',
			'relationship_guid' => $this->getOriginalContainer()->guid,
			'inverse_relationship' => true,
		);
		return array_merge($defaults, $options);
	}

	/**
	 * Returns an array of subscribed users
	 *
	 * @param array $options  Additional options
	 * @return ElggUser[]|false
	 */
	public function getSubscribedUsers() {
		$options = $this->getSubscriberFilterOptions($options);
		return elgg_get_entities_from_relationship($options);
	}

	/**
	 * {@inheritdoc}
	 */
	public function save($update_last_action = true) {
		$result = false;
		if (elgg_trigger_before_event('create', 'object', $this)) {
			$result = parent::save($update_last_action);
			if ($result) {
				elgg_trigger_after_event('create', 'object', $this);
			}
		}
		return $result;
	}

}
