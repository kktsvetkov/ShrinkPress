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

		// "ShrinkPress\Chrono\Time" to "shrinkpress/chrono/src/Time"
		//
		$p = explode('\\', $package);
		$l = array_shift($p);
		$lib = strtolower($l) . '/src/' . (
			!empty($p)
				? join('/', $p)
				: ''
			);

		$folder = Composer::vendors . '/shrinkpress/' . $lib;
		return array($namespace, $folder);
	}

	static function wpClassFile($classNamespace, $className)
	{
		$c = explode('\\', $className);
		$className = array_pop($c);
		if ($c)
		{
			$classNamespace .= join('\\', $c);
		}

		$classNamespace = rtrim($classNamespace, '\\');
		return '<?php '
			. "\n"
			. "\nnamespace {$classNamespace};"
			. "\n"
			. "\nclass {$className}"
			. "\n" . '{}'
			. "\n";
	}

	static function tabify($code)
	{
		$code = str_replace("\n", "\n\t" , $code);
		$code = "\t" . rtrim($code, "\t");
		return $code;
	}
}
