<?php

namespace ShrinkPress\Reframe\Evolve;

class ComposerPhar
{
	const url = 'https://getcomposer.org/composer.phar';

	static function get()
	{
		$local = __DIR__ . '/../../composer.phar';
		if (!file_exists($local))
		{
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

	static function dumpautoload()
	{
		$composer_phar = self::get();
		shell_exec(
			'php ' . escapeshellcmd($composer_phar) . ' dumpautoload'
			);
	}
}
