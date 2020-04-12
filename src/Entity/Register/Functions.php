<?php

namespace ShrinkPress\Build\Entity\Register;

use ShrinkPress\Build\Entity\Funcs\Function_Entity;
use ShrinkPress\Build\Entity\Funcs\WordPress_Func;
use ShrinkPress\Build\Assist;

class Functions extends Register_Abstract
{
	use Assist\Instance;

	function getFunctions()
	{
		return $this->getEntities();
	}

	function getFunctionNames()
	{
		return $this->getKeys();
	}

	function addFunction(Function_Entity $func)
	{
		return $this->addEntity($func->functionName(), $func);
	}

	function getFunction($functionName)
	{
		if ($entity = $this->getEntity( $functionName ))
		{
			return $entity;
		}

		$this->addFunction( $entity = new WordPress_Func( $functionName ) );
		return $entity;
	}

	protected function stashEntityFilename($key)
	{
		return str_replace('\\', '/', $key) . '.json';
	}
}
