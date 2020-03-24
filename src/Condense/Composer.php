<?php

namespace ShrinkPress\Build\Condense;

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
		'psr-4' => array(),
		),
	);

	protected $source;

	function __construct()
	{
		$this->source = self::source;
	}

	static protected $instance;

	static function instance()
	{
		if (empty(self::$instance))
		{
			self::$instance = new self;
		}

		return self::$instance;
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
		return $this->source['autoload']['psr-4'];
	}

	function addShrinkPressPsr4($package)
	{
		$namespace = 'ShrinkPress\\' . trim($package, '\\') . '\\';
		$folder = 'wp-includes/vendors/shrinkpress/'
			. str_replace('\\', '/', strtolower($package))
			. '/src';

		return $this->addPsr4($namespace, $folder);
	}

	function addPsr4($namespace, $folder)
	{
		$this->source['autoload']['psr-4'][ $namespace ] = $folder;
	}
}
