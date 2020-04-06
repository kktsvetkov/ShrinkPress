<?php

namespace ShrinkPress\Build\File;

use ShrinkPress\Build\Verbose;

class ComposerJson extends FileAbstract
{
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
			'psr-4' => array(),
			),
	);

	protected $filename = 'composer.json';

	function __construct()
	{

	}

	protected $psr4 = array();

	function packages()
	{
		return $this->psr4;
	}

	function addPsr4($namespace, $folder)
	{
		$this->psr4[ $namespace ] = $folder;
	}

	function json()
	{
		$composerJson = self::source;
		$composerJson['autoload']['psr-4'] = (object) $this->psr4;

		return json_encode(
			$composerJson,
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
		);
	}

}
