<?php

namespace ShrinkPress\Reframe\Unparse\Build\Start;

use ShrinkPress\Reframe\Unparse;
use ShrinkPress\Reframe\Index;

class PlantComposer implements Unparse\Build\Task
{

	const find_WPINC = array(
		'define', '(', "'WPINC'", ',', "'wp-includes'", ')', ';'
		);

	const after_WPINC = array(
		"\n/** @see shrinkpress */",
		"\nrequire ABSPATH . WPINC . '/vendor/autoload.php';",
		"\n"
		);

	function build(Unparse\Source $source, Index\Index_Abstract $index )
	{
		$code = $source->read($wp_settings = 'wp-settings.php');

		$tokens = token_get_all($code);
		$seek = self::find_WPINC;

		$modified = array();
		$last = array();
		foreach ($tokens as $token)
		{
			$oken = is_scalar($token) ? $token : $token[1];
			$modified[] = $oken;

			// skip T_WHITESPACE
			//
			if (382 == $token[0])
			{
				continue;
			}

			array_push($last, $oken);
			if (count($last) > count($seek))
			{
				array_shift($last);
			}

			// found "define( 'WPINC', 'wp-includes' );",
			// plant the vendors/autoload.php after it
			//
			if ($seek == $last)
			{
				$modified[] = join('', self::after_WPINC);
			}
		}

		$code = join('', $modified);
		$source->write($wp_settings, $code );
	}
}
