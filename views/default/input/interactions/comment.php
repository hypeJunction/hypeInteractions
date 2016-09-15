<?php

/**
 * Comment input
 */
$defaults = [
	'rows' => 2,
	'placeholder' => elgg_echo('generic_comments:add'),
	'id' => "elgg-input-" . base_convert(mt_rand(), 10, 36),
];

$class = (array) elgg_extract('class', $vars, []);
$class[] = 'elgg-input-comment';
$vars['class'] = $class;

$vars = array_merge($defaults, $vars);

$value = htmlspecialchars($vars['value'], ENT_QUOTES, 'UTF-8');
unset($vars['value']);

echo elgg_view_menu('longtext', array(
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
	'id' => $vars['id'],
));

echo elgg_format_element('textarea', $vars, $value);

if (elgg_is_active_plugin('ckeditor')) {
	?>
	<script>
		require(['input/interactions/comment']);
	</script>
	<?php

}