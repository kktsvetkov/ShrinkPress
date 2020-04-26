<?php

namespace ShrinkPress\Reframe\Evolve;

class Proto
{
	static function getFunction(array $f)
	{
		$n = self::protoPackage($f['filename']);
		return array(
			'method' => $f['function'],
			'class' => $n['class'],
			'namespace' => $n['ns'],
			'full' => $n['ns'] . '\\' . $n['class'] . '::' . $f['function']
			);
	}

	private static function classify($string)
	{
		$string = pathinfo($string, PATHINFO_FILENAME);

		$string = join('_', array_map('ucfirst', explode('-', $string)));
		$string = join('\\', array_map('ucfirst', explode('\\', $string)));

		$string = str_replace('Wp_', '', $string);
		$string = str_replace('Class_', '', $string);
		$string = str_replace('Wordpress', 'WordPress', $string);

		return $string;
	}

	private static function protoPackage($file)
	{
		$class = self::classify(basename($file));

		$dir = 'wordpress/' . dirname($file);
		$dir = str_replace('/', '\\', $dir);
		$namespace = self::classify($dir);

		return array(
			'ns' => $namespace,
			'class' => $class,
		);
	}
}
