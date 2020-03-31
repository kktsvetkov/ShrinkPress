<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project;
use ShrinkPress\Build\Condense;

class UseNamespaces extends TaskAbstract
{
	static $use = array();

	static function add($file, $replacement)
	{
		$s = Project\Entity\ShrinkPressClass::fromClassMethod($replacement);
		static::$use[ $file ][ $s->classNamespace() ][ $s->name ] = $replacement;
	}

	function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		)
	{

	}
}
