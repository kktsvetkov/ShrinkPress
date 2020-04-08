<?php

namespace ShrinkPress\Build\Assist;

use ShrinkPress\Build\Verbose;

class ComposerPhar
{
	const url = 'https://getcomposer.org/composer.phar';

	static function get()
	{
		$local = __DIR__ . '/../../composer.phar';
		if (!file_exists($local))
		{
			Verbose::log('Downloading composer.phar', 2);
			shell_exec(
				'curl -s ' . self::url
					. ' -O ' . escapeshellcmd($local)
				);

			if (!file_exists($local))
			{
				throw new \RuntimeException(
					'Unable to download composer.phar'
				);
			}
		}

		return realpath($local);
	}

	static function dumpautoload($basedir)
	{
		$basedir = (string) $basedir;
		if (!is_dir($basedir))
		{
			throw new \InvalidArgumentException(
				"Provided \$basedir must be an existing folder."
			);
		}
		chdir($basedir);

		$composer_phar = self::get();
		shell_exec(
			'php ' . escapeshellcmd($composer_phar) . ' dumpautoload'
			);
	}
}
