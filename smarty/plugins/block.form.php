<?php

/**
 * block.form.php
 */
function smarty_block_form($params, $contents)
{
	if (!$contents) {
		return;
	}

	$attr = [];
	foreach ($params as $key => $val) {
		$attr[] = sprintf('%s="%s"', $key, $val);
	}
	$attribute = (count($attr) > 0) ? ' ' . implode(' ', $attr) : '';

	$html = sprintf('<form%s>', $attribute);
	$html .= $contents;

	$html .= sprintf('<input type="hidden" name="csrf_token" value="%s">'
		, MyApp\common\Csrf::get()
	);
	$html .= '</form>';

	return $html;
}
