<?php

namespace ShrinkPress\Build\Condense;

class Transform
{
	static function wpFunction($name)
	{
		return $name;
	}

	static function wpNamespace($package)
	{
		$namespace = 'ShrinkPress\\' . trim($package, '\\') . '\\';
		$folder = Composer::vendors . '/shrinkpress/'
			. str_replace('\\', '/', strtolower($package))
			. '/src';

		return array($namespace, $folder);
	}
}
