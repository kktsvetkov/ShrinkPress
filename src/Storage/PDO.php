<?php

namespace ShrinkPress\Build\Storage;

use ShrinkPress\Build\Parse\Entity;

class PDO extends StorageAbstract
{
	protected $pdo;

	function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	function readFunction($functionName)
	{
		$entity = new Entity\WpFunction( (string) $functionName );

		if ($found = PDO\WpFunction::exists ( $entity, $this->pdo ))
		{
			foreach ($found as $k => $v)
			{
				$entity->$k = $v;
			}
		}

		return $entity;
	}

	function writeFunction(Entity\WpFunction $entity)
	{
		return PDO\WpFunction::write($entity, $this->pdo);
	}

	function getFunctions()
	{
		return PDO\WpFunction::all($this->pdo);
	}

	function writeCall( Entity\WpCall $call )
	{
		return PDO\WpCall::write($call, $this->pdo);
	}

	function clean()
	{
		PDO\WpFunction::clean($this->pdo);
		PDO\WpCall::clean($this->pdo);
	}
}
