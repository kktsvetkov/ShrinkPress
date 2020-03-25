<?php

namespace ShrinkPress\Build\Project\Storage;

use ShrinkPress\Build\Project\Entity;

class Old extends StorageAbstract
{
	protected $build;

	function __construct($build)
	{
		$this->build = (string) $build;
	}

	protected function local($entity, $name)
	{
		$prefix = $entity . '/' . substr($name, 0, 3) . '/';
		return $this->build . '/' . $prefix . $name . '.php';
	}

	function read($entity, $name)
	{
		$local = $this->local($entity, $name);

		switch ($entity)
		{
			case self::ENTITY_FUNCTION:
				$object = new Entity\WpFunction($name);
				break;

			default:
				throw new \UnexpectedValueException(
					"Weird entity: {$entity}"
				);
				break;
		}

		if (file_exists($local))
		{
			$data = include $local;
			$object->load((array) $data);
		}

		return $object;
	}

	function write($entity, $name, array $data)
	{
		$local = $this->local($entity, $name);

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
		$indexFile = $this->build . '/' . $entity . '.php';
		$indexData = array();

		static $index = array();
		if (empty($index[ $entity ]))
		{
			$index[ $entity ] = $indexFile;
			file_put_contents($indexFile, '<?php $indexData = [];');
		} else
		{
			include $indexFile ;
		}

		if (empty($indexData[ $name ]))
		{
			$indexData[ $name ] = $local;

			file_put_contents($indexFile,
				"\n\$indexData["
					. json_encode($name) . '] = 1;',
				FILE_APPEND
			);
		}
	}
}
