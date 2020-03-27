<?php

namespace ShrinkPress\Build\Condense;

class Transform
{
	const function_prefix = array(
		'wp_ajax_',
		'wp_',
	);

	static function wpFunction($name)
	{
		$prefix = false;
		foreach (self::function_prefix as $p)
		{
			if (0 === strpos($name, $p))
			{
				$name = substr($name, strlen($p));
				$prefix = true;
				break;
			}
		}

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
