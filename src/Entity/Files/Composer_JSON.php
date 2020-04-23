<?php

namespace ShrinkPress\Reframe\Entity\Files;

use ShrinkPress\Reframe\Assist;

class Composer_JSON implements File_Entity
{
	use Assist\Instance;

	const filename = 'composer.json';

	function filename()
	{
		return self::filename;
	}

	function load(array $data)
	{
		if (!empty($data['psr4']))
		{
			$this->psr4 = $data['psr4'];
		}
	}

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

	protected $psr4 = array();

	function packages()
	{
		return $this->psr4;
	}

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
