<?php

namespace ShrinkPress\Build\Disintegrate;

class Composer
{
	const compatibility = 'wp-includes/vendors/shrinkpress/compatibility.php';

	const source = array(
		'name' => 'shrinkpress/shrinkpress',
		'description' => 'ShrinkPress: Break WordPress Apart',
		'type' => 'project',
		'license' => 'GPL-2.0-or-later',
		'require' => array(
			'php' => '>=5.6.0',
			),
		'config' =>  array(
			'vendor-dir' => 'wp-includes/vendors',
			),
		'autoload' => array(
			'files' => array(
				self::compatibility,
				),
		'psr-4' => array(
			'ShrinkPress\\Load\\' => 'wp-includes/vendors/shrinkpress/load/src',
			),
		),
	);

	protected $source;

	function __construct()
	{
		$this->source = self::source;
	}

	function json()
	{
		return json_encode(
			$this->source,
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
		);
	}

	function packages()
	{
		return $this->source['psr-4'];
	}

	function add($package)
	{
		$namespace = 'ShrinkPress\\' . trim($package, '\\') . '\\';
		$folder = 'wp-includes/vendors/shrinkpress/'
			. str_replace('\\', '/', strtolower($package))
			. '/src';

		$this->source['psr-4'][ $namespace ] = $folder;
	}

}
