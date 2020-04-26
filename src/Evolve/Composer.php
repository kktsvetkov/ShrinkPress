<?php

namespace ShrinkPress\Reframe\Evolve;

class Composer
{
	const vendors = 'shrinkpress-vendors';

	const source = array(
		'name' => 'shrinkpress/shrinkpress',
		'description' => 'ShrinkPress: Break WordPress Apart',
		'type' => 'project',
		'license' => 'GPL-2.0-or-later',
		'require' => array(
			'php' => '>=7.0.0',
			),
		'config' =>  array(
			'vendor-dir' => self::vendors,
			),
		'autoload' => array(
			'psr-4' => array(),
			),
	);

	public $psr4 = array();

	function addPsr4($namespace, $folder)
	{
		$this->psr4[ $namespace ] = $folder;
	}

	function jsonSerialize()
	{
		$data = self::source;
		$data['autoload']['psr-4'] = (object) $this->psr4;
		return $data;
	}
}
