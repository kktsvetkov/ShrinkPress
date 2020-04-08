<?php

namespace ShrinkPress\Build\Unparse\Task;

use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;

class PlantComposer extends TaskAbstract
{
	const find_WPINC = array(
		'define', '(', "'WPINC'", ',', "'wp-includes'", ')', ';'
		);

	const after_WPINC = array(
		"\n/** @see shrinkpress */",
		"\nrequire ABSPATH . WPINC . '/vendor/autoload.php';",
		"\n"
		);

	function build(Source $source, Storage\StorageAbstract $storage)
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
