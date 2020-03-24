<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project;
use ShrinkPress\Build\Condense;

class Start extends TaskAbstract
{
	const gitignore = array(
		'/composer.lock',
		'/wp-config.php',
		);

	const gitattributes = array();

	function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		)
	{
		// start with composer.json ...
		//
		$composer = Condense\Composer::instance();
		$source->write('composer.json', $composer->json() );

		// ... then do the dot files
		//
		if ($gitignore = join("\n", self::gitignore))
		{
			$source->write('.gitignore', $gitignore );
		}

		if ($gitattributes = join("\n", self::gitattributes))
		{
			$source->write('.gitattributes', $gitattributes );
		}

		// add "vendor/autoload.php"
		//
		$composer->dumpautoload( $source->basedir() );

		$code = $source->read($a = 'wp-settings.php');
		$source->write($a, $this->plant_vendors($code) );

		// compatibility file
		//
		$source->write(Condense\Compat::compatibility_php, '' );
	}

	const find_WPINC = array(
		'define', '(', "'WPINC'", ',', "'wp-includes'", ')', ';'
		);

	const after_WPINC = array(
		"\n/** @see shrinkpress */",
		"\nrequire ABSPATH . WPINC . '/vendor/autoload.php';",
		"\n"
		);

	protected function plant_vendors($code)
	{
		$wp_settings = token_get_all($code);
		$seek = self::find_WPINC;

		$modified = array();
		$last = array();
		foreach ($wp_settings as $token)
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

		return join('', $modified);
	}
}
