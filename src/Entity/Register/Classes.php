<?php

namespace ShrinkPress\Build\Entity\Register;

use ShrinkPress\Build\Entity\Classes\Class_Entity;
use ShrinkPress\Build\Assist;

class Classes extends Register_Abstract
{
	use Assist\Instance;

	function getClasses()
	{
		return $this->getEntities();
	}

	function getClassNames()
	{
		return $this->getKeys();
	}

	function addClass(Class_Entity $class)
	{
		return $this->addEntity($class->className(), $class);
	}

	function getClass($class)
	{
		return $this->getEntity( $class );
	}

	protected function stashEntityFilename($key)
	{
		return str_replace('\\', '/', $key) . '.json';
	}
}
