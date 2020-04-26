<?php

namespace ShrinkPress\Reframe\Evolve;

class Move
{
	static function classNameToFilename($fullClassName)
	{
		$c = explode('\\', $fullClassName);
		$topLevel = array_shift($c);
		$library = array_shift($c);

		$filename = Composer::vendors
			. '/' . strtolower($topLevel)
			. '/' . strtolower($library)
			. '/src/' . join('/', $c) . '.php';

		return $filename;
	}

	static function createClass(array $s, $classFilename)
	{

	}

	static function moveFunction(array $f, array $m)
	{
		$fullClassName = $m['namespace'] . '\\' . $m['class'];
		$classFilename = self::classNameToFilename($fullClassName);
		if (!file_exists($classFilename))
		{
			self::createClass($m, $classFilename);
		}

		print_r($m);var_dump($classFilename); // exit;


		Composer::updateComposer();
		Git::commit("{$f['function']}() moved to {$m['full']}()");
	}
}
