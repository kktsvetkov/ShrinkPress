<?php

namespace ShrinkPress\Reframe\Evolve;

class Reframe_PhpDoc extends Reframe
{
	function reframeFunction($function, $filename)
	{
		$d = self::extractPackages($filename);
		return array(
			'method' => $function,
			'class' => $d[1],
			'namespace' => $ns = join('\\', $d),
			'full' => $ns . '\\' . $d[1] . '::' . $function
			);
	}

	function reframeClass($class, $filename)
	{
		$d = self::extractPackages($filename);
		return array(
			'class' => $class,
			'namespace' => $ns = join('\\', $d),
			'full' => $ns . '\\' . $class
			);
	}

	function reframeGlobal($global, $filename)
	{
		$d = self::extractPackages($filename);
		return array(
			'global' => $global,
			'class' => $d[1],
			'namespace' => $ns = join('\\', $d),
			'full' => $ns . '\\' . $d[1] . '::$' . $global
			);
	}

	static $known = array();

	static function extractPackages($filename)
	{
		if (!empty(self::$known[ $filename ]))
		{
			return self::$known[ $filename ];
		}

		$result = array('WordPress', 'Core');
		$doccomment = substr(file_get_contents($filename), 0, 1024);

		if (preg_match('~\s*\*\s+@package\s+(.+)\s+\*~Uis', $doccomment, $R))
		{
			$result[0] = $R[1];
		}

		if (preg_match('~\s*\*\s+@subpackage\s+(.+)\s+\*~Uis', $doccomment, $R))
		{
			$result[1] = $R[1];
		}

		if (empty($result[1]))
		{
			$result[1] = $result[0];
		}

		return self::$known[ $filename ] = $result;
	}
}
