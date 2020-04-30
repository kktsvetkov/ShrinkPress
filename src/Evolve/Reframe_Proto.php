<?php

namespace ShrinkPress\Reframe\Evolve;

class Reframe_Proto extends Reframe
{
	function reframeFunction($function, $filename)
	{
		$n = self::protoPackage($filename);
		return array(
			'method' => $function,
			'class' => $n['class'],
			'namespace' => $n['ns'],
			'full' => $n['ns'] . '\\' . $n['class'] . '::' . $function
			);
	}

	function reframeClass($class, $filename)
	{
		$n = self::protoPackage($filename);
		return array(
			// 'class' => $n['class'],
			'class' => $class,
			'namespace' => $n['ns'],
			'full' => $n['ns'] . '\\' . $n['class']
			);
	}

	function reframeGlobal($global, $filename)
	{
		$n = self::protoPackage($filename);
		return array(
			'global' => $global,
			'class' => $n['class'],
			'namespace' => $n['ns'],
			'full' => $n['ns'] . '\\' . $n['class'] . '::$' . $global
			);
	}

	private static function classify($string)
	{
		$string = pathinfo($string, PATHINFO_FILENAME);

		$string = join('_', array_map('ucfirst', explode('-', $string)));
		$string = join('\\', array_map('ucfirst', explode('\\', $string)));
		$string = join('\\', array_map('ucfirst', explode('.', $string)));

		$string = str_replace('Wp_', '', $string);
		$string = str_replace('Class_', '', $string);

		return 'WordPress\\' . $string;
	}

	private static function protoPackage($file)
	{
		$full = self::classify( $file );
		$chunks = explode('\\', $full);

		if (2 == count($chunks))
		{
			$chunks[2] = $chunks[1];
		}

		return array(
			'class' => array_pop($chunks),
			'ns' => join('\\', $chunks),
		);
	}
}
