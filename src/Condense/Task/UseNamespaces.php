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

		$md5 = md5($replacement);
		if (empty(static::$use[ $file ][ $s->className() ][ $md5 ]))
		{
			static::$use[ $file ][ $s->className() ][ $md5 ] = $replacement;
		}
	}

	function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		)
	{
		file_put_contents(
			'/Users/polina/kt.kt/ShrinkPress/use.json',
			json_encode(self::$use, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
		);
	}
}
