<?php

namespace hypeJunction\Interactions;

use ElggUser;

/**
 * Formats notifications
 */
class NotificationFormatter {

	/**
	 * @var Comment
	 */
	private $comment;

	/**
	 * @var ElggEntity
	 */
	private $author;

	/**
	 * @var ElggEntity
	 */
	private $object;

	/**
	 * @var \ElggEntity
	 */
	private $root;

	/**
	 * @var ElggUser
	 */
	private $recipient;

	/**
	 * @var string
	 */
	private $language;

	/**
	 * Constructor
	 *
	 * @param Comment  $comment   Comment that was made
	 * @param ElggUser $recipient User to receive a notification
	 * @param string   $language  Language of the notification
	 */
	public function __construct(Comment $comment, \ElggUser $recipient, $language = 'en') {
		$this->comment = $comment;
		$this->recipient = $recipient;
		$this->language = $language;

		$this->author = $comment->getOwnerEntity();
		$this->object = $comment->getContainerEntity();
		$this->root = $comment->getOriginalContainer();
	}

	/**
	 * Prepares notification elements
	 * @return \stdClass
	 */
	public function prepare() {

		$object_type = $this->getObjectType();

		$object_link = elgg_view('output/url', array(
			'text' => $this->object->getDisplayName(),
			'href' => elgg_http_add_url_query_elements($this->object->getURL(), array(
				'active_tab' => 'comments',
			)),
		));

		if ($this->author->guid == $this->object->owner_guid) {
			$object_summary_title = elgg_echo('interactions:ownership:own', array($object_type), $this->language);
		} else if ($this->recipient->guid == $this->object->owner_guid) {
			$object_summary_title = elgg_echo('interactions:ownership:your', array($object_type), $this->language);
		} else {
			$object_owner = $this->object->getOwnerEntity() ? : elgg_get_site_entity();
			$object_summary_title = elgg_echo('interactions:ownership:owner', array($object_owner->getDisplayName(), $object_type), $this->language);
		}

		if ($this->object instanceof Comment) {
			$object_full_title = $object_summary_title;
		} else {
			$object_full_title = $object_summary_title . ' ' . $object_link;
		}

		if ($this->root->guid !== $this->object->guid) {
			$root_link = elgg_view('output/url', array(
				'text' => $this->root->getDisplayName(),
				'href' => elgg_http_add_url_query_elements($this->root->getURL(), array(
					'active_tab' => 'comments',
				)),
			));
			$object_full_title .= ' ' . elgg_echo('interactions:comment:in_thread', array($root_link));
		}

		$author_link = elgg_view('output/url', array(
			'text' => $this->author->name,
			'href' => $this->author->getURL(),
		));

		$object_summary_link = elgg_view('output/url', array(
			'text' => $object_summary_title,
			'href' => elgg_http_add_url_query_elements($this->object->getURL(), array(
				'active_tab' => 'comments',
			)),
		));

		$action_type = $this->getActionType();
		
		$notification = new \stdClass();
		$notification->summary = elgg_echo('interactions:response:email:subject', array(
			$author_link,
			$action_type,
			$object_summary_link
				), $this->language);
		$notification->subject = strip_tags($notification->summary);
		$notification->body = elgg_echo('interactions:response:email:body', array(
			$author_link,
			$action_type,
			$object_full_title,
			$this->getComment(),
			$this->comment->getURL(),
			$this->root->getURL(),
			$this->author->getDisplayName(),
			$this->author->getURL(),
				), $this->language);

		return $notification;
	}

	/**
	 * Prepare action type string (e.g. replied to)
	 * @return string
	 */
	public function getActionType() {
		$comment_subtype = $this->comment->getSubtype();

		$object_type = $this->object->getType();
		$object_subtype = $this->object->getSubtype() ? : 'default';
		$keys = [
			"interactions:action:$comment_subtype:on:$object_type:$object_subtype",
			"interactions:action:$comment_subtype:on:$object_type",
			"interactions:action:$comment_subtype",
		];
		foreach ($keys as $key) {
			if (elgg_language_key_exists($key)) {
				return elgg_echo($key, array(), $this->language);
			}
		}
		return elgg_echo('interactions:action:comment', $this->language);
	}

	/**
	 * Prepares object type string
	 * @return string
	 */
	public function getObjectType() {
		$type = $this->object->getType();
		$subtype = $this->object->getSubtype() ? : 'default';
		$keys = [
			"interactions:$type:$subtype",
			$this->object instanceof Comment ? "interactions:comment" : "interactions:post",
		];
		foreach ($keys as $key) {
			if (elgg_language_key_exists($key, $this->language)) {
				return elgg_echo($key, array(), $this->language);
			}
		}
		return elgg_echo('interactions:post', $this->language);
	}

	/**
	 * Prepares comment body, including the text and attachment info
	 * @return string
	 */
	protected function getComment() {
		$comment_body = elgg_view('output/longtext', array(
			'value' => $this->comment->description,
		));
//		if (elgg_view_exists('output/linkify')) {
//			$comment_body = elgg_view('output/linkify', array(
//				'value' => $comment_body
//			));
//		}
		$comment_body .= elgg_view('output/attached', array(
			'entity' => $this->comment,
		));

		$attachments = $this->comment->getAttachments(array('limit' => 0));
		if ($attachments && count($attachments)) {
			$attachments = array_map(__NAMESPACE__ . '\\get_linked_entity_name', $attachments);
			$attachments_text = implode(', ', array_filter($attachments));
			if ($attachments_text) {
				$comment_body .= elgg_echo('interactions:attachments:labelled', array($attachments_text));
			}
		}

		return $comment_body;
	}

}
