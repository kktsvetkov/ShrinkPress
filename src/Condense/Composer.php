<?php

namespace ShrinkPress\Build\Condense;

use ShrinkPress\Build\Verbose;

class Composer
{
	use \ShrinkPress\Build\Assist\Instance;

	const composer_phar = 'https://getcomposer.org/composer.phar';

	static function composer_phar()
	{
		$local = __DIR__ . '/../../composer.phar';
		if (!file_exists($local))
		{
			Verbose::log('Downloading composer.phar', 2);
			shell_exec(
				'curl -s ' . self::composer_phar
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

	const vendors = 'wp-includes/vendor';

	const source = array(
		'name' => 'shrinkpress/shrinkpress',
		'description' => 'ShrinkPress: Break WordPress Apart',
		'type' => 'project',
		'license' => 'GPL-2.0-or-later',
		'require' => array(
			'php' => '>=5.6.0',
			),
		'config' =>  array(
			'vendor-dir' => self::vendors,
			),
		'autoload' => array(
			'files' => array( Compat::compatibility_php ),
			'psr-4' => array(),
			),
	);

	protected $source;

	function __construct()
	{
		$this->source = self::source;
	}

	function json()
	{
		$json = $this->source;
		$json['autoload']['psr-4'] = (object) $json['autoload']['psr-4'];

		return json_encode(
			$json,
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
		);
	}

	function packages()
	{
		return $this->source['autoload']['psr-4'];
	}

	function addPsr4($namespace, $folder)
	{
		$this->source['autoload']['psr-4'][ $namespace ] = $folder;
	}

	function dumpautoload($basedir)
	{
		$composer_phar = self::composer_phar();
		chdir($basedir);
		shell_exec('php '
			. escapeshellcmd($composer_phar)
			. ' dumpautoload'
			);
	}
}
