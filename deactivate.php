<?php

namespace hypeJunction\Interactions;

$subtypes = array(
	Comment::SUBTYPE,
	RiverObject::SUBTYPE,
	'hjcoment',
	'hjstream',
);

foreach ($subtypes as $subtype) {
	update_subtype('object', $subtype);
}
