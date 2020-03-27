<?php

namespace ShrinkPress\Build\Project\Storage;

use ShrinkPress\Build\Project\Entity;

class Stash extends StorageAbstract
{
	const ENTITY_CLASS = 'class';
	const ENTITY_FUNCTION = 'function';
	const ENTITY_GLOBAL = 'global';
	const ENTITY_INCLUDE = 'include';

	protected $build;

	function __construct($build)
	{
		$this->build = (string) $build;
	}

	function beforeScan()
	{
		shell_exec('rm -rf ' . $this->build . '/function');
		shell_exec('rm -f ' . $this->build . '/function.csv');
	}

	function afterScan() {}

	protected function local($entity, $name)
	{
		$prefix = $entity . '/' . substr($name, 0, 3) . '/';
		return $this->build . '/' . $prefix . $name . '.php';
	}

	function readFunction($name)
	{
		$object = new Entity\WpFunction($name);

		$local = $this->local(self::ENTITY_FUNCTION, $name);
		if (file_exists($local))
		{
			$data = include $local;
			$object->load((array) $data);
		}

		return $object;
	}

	function writeFunction(Entity\WpFunction $entity)
	{
		$data = $entity->getData();
		$local = $this->local(self::ENTITY_FUNCTION, $name = $data['name']);

		$dir = dirname($local);
		if (!file_exists($dir))
		{
			mkdir($dir, 0777, true);
		}

		file_put_contents(
			$local,
			'<?php return ' . var_export($data, true) . '; '
			);

		// update index
		//
		static $indexFile;
		if (empty($indexFile))
		{
			$indexFile = $this->build . '/function.csv';
			file_put_contents($indexFile, '');
		}

		static $index = array();
		if (empty($index[ $name ]))
		{
			$index[ $name ] = count($index);
			file_put_contents($indexFile, $name . "\n", FILE_APPEND);
		}
	}

	function getFunctions()
	{
		$all = array();

		if (file_exists($indexFile = $this->build . '/function.csv'))
		{
			$all = file($indexFile);
			$all = array_map('trim', $all);
			$all = array_unique($all);
		}

		return array_values($all);
	}
}
