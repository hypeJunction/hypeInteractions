<?php

namespace hypeJunction\Interactions;

$subtypes = array(
	Comment::SUBTYPE,
	RiverObject::SUBTYPE,
);

foreach ($subtypes as $subtype) {
	update_subtype('object', $subtype);
}
